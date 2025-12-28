# Установка и запуск Symfony (DEV)

## Где живёт Symfony
Symfony-приложение находится в каталоге `app/` и монтируется в контейнеры как `/var/www/app`.

## Как запускать dev-окружение
- `make up` — поднять инфраструктуру
- `make composer-install` — установить зависимости
- `make console cmd="about"` — проверить Symfony

## Важные правила
- Symfony `.env` используется только в `app/.env`.
- Инфраструктура управляется через `infra/docker-compose.yml`.
- CLI-контейнер не “висит”, команды выполняются через `docker compose run --rm …` или `make …`.
