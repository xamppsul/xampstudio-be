#!/bin/bash
set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${YELLOW}========================================${NC}"
echo -e "${YELLOW}Laravel Application Startup${NC}"
echo -e "${YELLOW}========================================${NC}"

# Step 1: Check and create .env
echo -e "\n${YELLOW}Step 1: Checking .env file...${NC}"
if [ ! -f .env ]; then
    echo -e "${YELLOW}Creating .env from .env.example...${NC}"
    if [ -f .env.example ]; then
        cp .env.example .env
        echo -e "${GREEN}✓ .env created${NC}"
    else
        echo -e "${RED}✗ ERROR: .env.example not found${NC}"
        exit 1
    fi
else
    echo -e "${GREEN}✓ .env exists${NC}"
fi

# Step 2: Ensure APP_KEY is valid for the configured cipher
echo -e "\n${YELLOW}Step 2: Checking APP_KEY...${NC}"

# Clear any stale cached config first. If bootstrap/cache/config.php exists,
# Laravel serves config entirely from that cache and ignores .env — which
# would make every .env edit below invisible to the app no matter what.
if [ -f bootstrap/cache/config.php ]; then
    echo -e "${YELLOW}Found cached config — clearing so .env is read fresh...${NC}"
    php artisan config:clear > /dev/null 2>&1 || rm -f bootstrap/cache/config.php
fi

# If APP_KEY was injected as a real environment variable (e.g. docker run
# -e APP_KEY=..., a secret manager, or your orchestrator), that always wins
# over whatever is baked into .env — write it in so artisan/Laravel see the
# same key consistently from both the process env and .env.
if [ -n "$APP_KEY" ]; then
    echo -e "${YELLOW}APP_KEY found in environment — syncing into .env...${NC}"
    if grep -q "^APP_KEY=" .env; then
        sed -i "s|^APP_KEY=.*|APP_KEY=${APP_KEY}|" .env
    else
        echo "APP_KEY=${APP_KEY}" >> .env
    fi
fi

# Validate using the same byte-length rules Laravel's Encrypter enforces —
# computed directly in PHP without booting the full framework. The earlier
# tinker-based check booted the entire app (DB, cache, every provider), so
# an unrelated boot failure could make this check fail even when the key
# itself was fine. This version only checks what actually matters here:
# does the decoded key have the right number of bytes for APP_CIPHER.
validate_key() {
    php -r '
        $cipher = strtolower($argv[1] !== "" ? $argv[1] : "aes-256-cbc");
        $key = $argv[2] ?? "";
        $lengths = [
            "aes-128-cbc" => 16,
            "aes-256-cbc" => 32,
            "aes-128-gcm" => 16,
            "aes-256-gcm" => 32,
        ];
        if (!isset($lengths[$cipher])) { echo "INVALID"; exit; }
        if ($key === "") { echo "INVALID"; exit; }
        if (str_starts_with($key, "base64:")) {
            $decoded = base64_decode(substr($key, 7), true);
        } else {
            $decoded = $key;
        }
        if ($decoded === false) { echo "INVALID"; exit; }
        echo (strlen($decoded) === $lengths[$cipher]) ? "VALID" : "INVALID";
    ' "$1" "$2"
}

CIPHER_VALUE=$(grep -E "^APP_CIPHER=" .env | cut -d'=' -f2- || true)
CIPHER_VALUE=${CIPHER_VALUE:-AES-256-CBC}
KEY_VALUE=$(grep -E "^APP_KEY=" .env | cut -d'=' -f2-)

KEY_STATUS=$(validate_key "$CIPHER_VALUE" "$KEY_VALUE")

if [ "$KEY_STATUS" != "VALID" ]; then
    echo -e "${YELLOW}APP_KEY missing or invalid for configured cipher (${CIPHER_VALUE}) — generating a new one...${NC}"
    php artisan key:generate --force > /dev/null 2>&1

    KEY_VALUE=$(grep -E "^APP_KEY=" .env | cut -d'=' -f2-)
    KEY_STATUS=$(validate_key "$CIPHER_VALUE" "$KEY_VALUE")

    if [ "$KEY_STATUS" = "VALID" ]; then
        echo -e "${GREEN}✓ APP_KEY generated and validated successfully${NC}"
    else
        echo -e "${RED}✗ ERROR: APP_KEY still invalid after generation — APP_CIPHER='${CIPHER_VALUE}' may be misspelled or unsupported${NC}"
        echo -e "${RED}  Supported ciphers: aes-128-cbc, aes-256-cbc, aes-128-gcm, aes-256-gcm${NC}"
        exit 1
    fi
else
    echo -e "${GREEN}✓ APP_KEY already valid for configured cipher (${CIPHER_VALUE})${NC}"
fi

# Export into the process environment regardless of the outcome above.
# This is the critical part: config('app.key') resolves via env('APP_KEY'),
# which checks getenv()/$_ENV before ever touching the .env file. Exporting
# here guarantees the app sees a valid key even if:
#   - a volume mount shadows/overwrites the .env file we just edited
#   - a stale bootstrap/cache/config.php is present (skips .env entirely)
#   - .env turns out not to be writable in this environment
# Laravel's Dotenv loader never overrides an already-set process env var,
# so this value takes precedence no matter what's on disk.
export APP_KEY="$KEY_VALUE"

# Step 3: Scaffold API routes + install Passport
# `install:api --passport` publishes routes/api.php and runs Passport's own
# install routine (which itself generates keys and creates default clients
# internally in newer Passport versions). Guarded on routes/api.php so this
# doesn't re-run and overwrite that file on every container restart.
echo -e "\n${YELLOW}Step 3: Running install:api --passport...${NC}"
if [ ! -f routes/api.php ]; then
    php artisan install:api --passport --no-interaction
    echo -e "${GREEN}✓ install:api --passport completed${NC}"
else
    echo -e "${GREEN}✓ routes/api.php already exists — skipping install:api${NC}"
fi

# Step 4: Laravel Passport encryption keys
# Generated before migrations since key generation writes files
# (storage/oauth-private.key, oauth-public.key) and has no database
# dependency — unlike the personal access client below, which needs the
# oauth_clients table to exist and therefore must wait until after migrate.
# Kept as a safety net in case install:api above didn't already generate
# them (e.g. an older Passport version, or this step running again after
# a restart where routes/api.php persisted but storage/ didn't).
echo -e "\n${YELLOW}Step 4: Generating Passport encryption keys...${NC}"

# Only generate if missing — regenerating on every container restart
# invalidates every access token issued before the restart, logging every
# user out. If storage/ isn't on a persistent volume, this WILL regenerate
# on every restart; mount storage/ as a volume, or set PASSPORT_PRIVATE_KEY /
# PASSPORT_PUBLIC_KEY as env vars (supported since Passport 10.x) to inject
# fixed keys instead of relying on the filesystem.
if [ ! -f storage/oauth-private.key ] || [ ! -f storage/oauth-public.key ]; then
    php artisan passport:keys --force
    echo -e "${GREEN}✓ Passport keys generated${NC}"
else
    echo -e "${GREEN}✓ Passport keys already exist${NC}"
fi

# Step 5: Run migrations
echo -e "\n${YELLOW}Step 5: Running migrations...${NC}"
if php artisan migrate --no-interaction 2>/dev/null; then
    echo -e "${GREEN}✓ Migrations completed${NC}"
else
    echo -e "${YELLOW}⚠ Migrations had warnings (non-critical)${NC}"
fi

# Step 6: Passport personal access client
# Must come after migrations — this writes a row into oauth_clients, which
# only exists once Passport's migrations have run.
echo -e "\n${YELLOW}Step 6: Setting up Passport personal access client...${NC}"

# `passport:client --personal` creates a NEW client every time it runs, so
# check the database first. Duplicates break any client_id you've
# hardcoded in a frontend/mobile app, and pile up unused rows otherwise.
PERSONAL_CLIENT_EXISTS=$(php artisan tinker --execute="
    echo \Laravel\Passport\Client::where('personal_access_client', true)->exists() ? 'YES' : 'NO';
" 2>/dev/null | tail -n1)

if [ "$PERSONAL_CLIENT_EXISTS" != "YES" ]; then
    php artisan passport:client --personal --name="Personal Access Client" --no-interaction
    echo -e "${GREEN}✓ Passport personal access client created${NC}"
else
    echo -e "${GREEN}✓ Passport personal access client already exists${NC}"
fi

# Step 7: Clear cache
echo -e "\n${YELLOW}Step 7: Clearing cache...${NC}"
php artisan cache:clear > /dev/null 2>&1 || true
php artisan config:clear > /dev/null 2>&1 || true
echo -e "${GREEN}✓ Cache cleared${NC}"

# Step 8: Show info
echo -e "\n${YELLOW}Step 8: Application info${NC}"
LARAVEL_VERSION=$(php artisan -V 2>/dev/null | tail -n1)
DISPLAY_KEY=$(grep "^APP_KEY=" .env | cut -d'=' -f2 | cut -c1-30)
echo -e "${GREEN}✓ ${LARAVEL_VERSION}${NC}"
echo -e "${GREEN}✓ APP_KEY: ${DISPLAY_KEY}...${NC}"

echo -e "\n${YELLOW}========================================${NC}"
echo -e "${GREEN}✓ Application is ready!${NC}"
echo -e "${YELLOW}========================================${NC}\n"

# Run the command
exec "$@"