# =====================================================
# Conwix — Makefile (DEV)
# Правило: простые алиасы, одинаковые команды всегда
# =====================================================

COMPOSE = docker compose -f infra/docker-compose.yml


# -----------------------------------------------------
# CORE
# -----------------------------------------------------

up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down --remove-orphans

down-clear:
	$(COMPOSE) down -v --remove-orphans

restart:
	$(COMPOSE) down
	$(COMPOSE) up -d

ps:
	$(COMPOSE) ps

logs:
	$(COMPOSE) logs -f --tail=200


# -----------------------------------------------------
# SHELL / DEBUG
# -----------------------------------------------------

# Одноразовый shell в CLI-контейнере
cli:
	$(COMPOSE) run --rm --entrypoint sh app-php-cli

# Shell в PHP-FPM (для отладки)
fpm:
	$(COMPOSE) exec app-php-fpm sh


# -----------------------------------------------------
# SYMFONY / APP
# -----------------------------------------------------

# Composer install
composer-install:
	$(COMPOSE) run --rm --entrypoint composer app-php-cli install

# Symfony console
# Пример: make console cmd="cache:clear"
console:
	$(COMPOSE) run --rm app-php-cli php bin/console $(cmd)

# Очистка кэшей/логов на хосте (быстро)
app-clear:
	docker run --rm -v ${PWD}/app:/app -w /app alpine:3.20 sh -lc 'rm -rf var/cache/* var/log/* var/test/* .ready || true'


# -----------------------------------------------------
# DATABASE
# -----------------------------------------------------

db-migrate:
	$(COMPOSE) run --rm app-php-cli php bin/console doctrine:migrations:migrate --no-interaction

db-fixtures:
	$(COMPOSE) run --rm app-php-cli php bin/console doctrine:fixtures:load --no-interaction

db-reset:
	$(COMPOSE) run --rm app-php-cli php bin/console doctrine:database:drop --force
	$(COMPOSE) run --rm app-php-cli php bin/console doctrine:database:create
	$(COMPOSE) run --rm app-php-cli php bin/console doctrine:migrations:migrate --no-interaction
	$(COMPOSE) run --rm app-php-cli php bin/console doctrine:fixtures:load --no-interaction


# -----------------------------------------------------
# INIT
# -----------------------------------------------------

# Полная инициализация проекта (после первого запуска)
init: down-clear up composer-install db-wait db-migrate


# -----------------------------------------------------
# CODE STYLE / TESTS (если появятся в composer.json)
# -----------------------------------------------------

cs-check:
	$(COMPOSE) run --rm --entrypoint composer app-php-cli cs:check

cs-fix:
	$(COMPOSE) run --rm --entrypoint composer app-php-cli cs:fix

test-unit:
	$(COMPOSE) run --rm --entrypoint composer app-php-cli test:unit

test:
	$(COMPOSE) run --rm --entrypoint composer app-php-cli test
