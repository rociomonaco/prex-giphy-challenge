.PHONY: up down build shell test coverage logs migrate

up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose build

shell:
	docker compose exec app sh

test:
	docker compose exec app php artisan test

coverage:
	docker compose exec app php artisan test --coverage

logs:
	docker compose logs -f

migrate:
	docker compose exec app php artisan migrate
