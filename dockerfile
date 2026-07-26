########################################
# Stage 1: resolve & install Composer dependencies
# Runs composer install/update against Packagist directly, so it always
# resolves against real PHP 8.3 + Laravel 13 constraints — no dependency
# on a pre-existing composer.lock being committed or in sync.
########################################
FROM composer:2 AS vendor

WORKDIR /app

# Copy composer.json. A committed composer.lock is used as a starting point
# if present, but we run `composer update` (not `install`) below so a lock
# that's out of sync with composer.json (e.g. after bumping laravel/framework
# to ^13.x) self-heals instead of hard-failing the build.
COPY composer.json composer.lock* ./

# --ignore-platform-req=ext-* : the composer:2 image is a minimal CLI image
# without gd/intl/swoole/etc compiled in. We only need this stage to resolve
# versions and download packages, not execute PHP extension code, so we
# ignore extension platform checks here — the *real* extension check still
# happens later in the app image, which has all extensions installed.
#
# `composer update` here (not `install`) means: always re-resolve against
# composer.json's actual constraints. If composer.lock matches already,
# this is a fast no-op. If it's stale/mismatched, it gets regenerated
# correctly instead of erroring out.
# Pin the platform PHP version Composer resolves against to 8.3, matching
# the actual app image (Stage 2, php:8.3-fpm). Without this, Composer
# resolves using whatever PHP ships inside the composer:2 image itself
# (which can be newer, e.g. 8.5), and may pick versions that are
# incompatible with your real PHP 8.3 runtime, or reject packages that
# cap out below the image's bundled PHP version.
RUN composer config platform.php 8.3.99

# Full update (not limited to specific packages) so the whole dependency
# graph — including transitive constraints like phpunit/phpunit and
# laravel/passport — resolves together consistently against Laravel 13's
# requirements, rather than leaving unrelated packages locked to stale
# versions that later conflict.
RUN composer update \
    --no-interaction \
    --no-dev \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts \
    --ignore-platform-req=ext-*

########################################
# Stage 2: application image
########################################
FROM php:8.3-fpm

# Step 1: Install system dependencies (base layer - cached)
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libicu-dev \
    zip \
    unzip \
    sqlite3 \
    libsqlite3-dev \
    build-essential \
    autoconf \
    automake \
    libtool \
    make \
    libssl-dev \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Step 2: Install PHP extensions (base layer - cached)
RUN docker-php-ext-install \
    pdo_mysql \
    pdo_sqlite \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    sockets \
    zip \
    intl

# Step 3: Install Swoole extension
RUN pecl install swoole-5.1.0 && docker-php-ext-enable swoole

# Step 4: Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Step 5: Get Composer (base layer - cached)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Step 6: Set working directory
WORKDIR /app

# Step 7: Copy composer.json (kept for reference / composer show, etc.)
COPY composer.json ./

# Step 8: Pull the resolved composer.lock and vendor/ from the vendor stage.
# This is the real fix for the ext-intl / lock-mismatch failures: dependency
# resolution happened in Stage 1 against actual Packagist + PHP 8.3 platform
# constraints, so what lands here is guaranteed installable.
COPY --from=vendor /app/composer.lock ./composer.lock
COPY --from=vendor /app/vendor ./vendor

# Step 9: Copy the rest of the app
COPY . /app

# Step 10: Regenerate optimized autoloader now that full app code exists.
# (package:discover / config caching happens in entrypoint.sh at runtime,
# since it needs .env / APP_KEY which usually aren't present at build time)
RUN composer dump-autoload --optimize

# Step 11: Copy entrypoint script (handles artisan key:generate, migrations, caching, etc.)
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

# Step 12: Set ownership of the whole app to www-data, not just storage/cache
RUN chown -R www-data:www-data /app

# Step 13: Ensure required runtime directories exist with correct permissions
RUN mkdir -p /app/bootstrap/cache /app/storage/logs && \
    chown -R www-data:www-data /app/bootstrap/cache /app/storage/logs

# Step 14: Switch to www-data user (better security)
USER www-data

# Step 15: Expose port
EXPOSE 8081

# Step 16: Health check
HEALTHCHECK --interval=15s --timeout=15s --start-period=20s --retries=3 \
    CMD php -r "file_exists('/app/vendor/autoload.php') or exit(1);"

# Step 17: Entrypoint runs setup, then hands off to CMD via exec "$@"
ENTRYPOINT ["/entrypoint.sh"]

# Step 18: Run Laravel Octane
CMD ["php", "artisan", "octane:start", "--server=swoole", "--host=0.0.0.0", "--port=8081"]