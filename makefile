dbuild:
	docker compose up --build
	docker exec -it app php artisan migrate
dpassportkeys:
	docker exec -it app php artisan passport:keys
drebuild:
	docker compose down
	docker compose up -d --build
doldimageremove:
	docker compose down
	docker image prune -f
dstop:
	docker compose down
dbash:
	docker exec -it app bash
drefresh:
	docker exec -it app php artisan migrate:refresh
drollback:
	docker exec -it app php artisan migrate:rollback
dmstatus:
	docker exec -it app php artisan migrate:status



start_serve:
	php artisan serve
start_octane:
	php artisan octane:start --server=swoole
refresh:
	php artisan migrate:refresh
migrate:
	php artisan migrate
seed:
	php artisan db:seed
passport_client:
	php artisan passport:client --personal

optimize:
	php artisan optimize
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear