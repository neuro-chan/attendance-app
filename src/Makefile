init:
	docker compose build
	docker compose up -d
	@if [ ! -f src/.env ]; then cp src/.env.example src/.env; fi
	docker compose exec -T php composer install
	docker compose exec -T php php artisan key:generate
	@echo "MySQLの起動を待っています..."
	@until docker compose exec -T mysql mysqladmin ping -h 127.0.0.1 -u laravel_user -plaravel_pass --silent; do \
		echo "接続待機中..."; \
		sleep 2; \
	done
	docker compose exec -T php php artisan migrate:fresh --seed
	docker compose exec -T php chown -R www-data:www-data storage bootstrap/cache || true
	docker compose exec -T php chmod -R 775 storage bootstrap/cache
	docker compose exec -T php rm -f public/storage
	docker compose exec -T php php artisan storage:link
	@echo "初期セットアップ完了"

test:
	@if [ ! -f src/.env.testing ]; then cp src/.env.testing.example src/.env.testing; fi
	@echo "MySQLの起動を待っています..."
	@until docker compose exec -T mysql mysqladmin ping -h 127.0.0.1 -u laravel_user -plaravel_pass --silent; do \
		echo "接続待機中..."; \
		sleep 2; \
	done
	docker compose exec -T php php artisan key:generate --env=testing
	docker compose exec -T mysql mysql -u laravel_user -plaravel_pass -e "CREATE DATABASE IF NOT EXISTS attendance_db_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
	docker compose exec -T php php artisan migrate:fresh --env=testing
	docker compose exec -T php php artisan test tests/Feature
