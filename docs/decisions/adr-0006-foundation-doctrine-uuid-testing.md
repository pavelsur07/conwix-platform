# Foundation: Doctrine, UUID, Builders-first (MVP)

## Статус
Accepted

## Контекст
Проект Conwix находится на начальной стадии (пустой MVP). Для предотвращения архитектурного долга необходимо зафиксировать базовые технические правила, связанные с идентификацией сущностей, работой с базой данных и тестированием. В рамках инициализации проекта были выполнены установка Symfony Skeleton, подключение Doctrine ORM и Doctrine Migrations, настройка Docker dev-окружения и реализация первого пользовательского сценария (регистрация).

## Решения

### Идентификаторы (UUID)
Все доменные сущности используют UUID в качестве идентификатора. UUID хранится в базе данных типом Doctrine guid. UUID всегда передаётся в конструктор сущности извне. Сущности не генерируют идентификаторы самостоятельно. Генерация UUID выполняется в контроллерах, сервисах или builders с использованием библиотеки ramsey/uuid.

Разрешено:
#[ORM\Id]
#[ORM\Column(type: 'guid', unique: true)]
private string $id;

public function __construct(string $id, string $email, \DateTimeImmutable $createdAt)
{
$this->id = $id;
}

Запрещено:
public function __construct(string $email)
{
$this->id = Uuid::uuid4()->toString();
}

### Doctrine ORM и миграции
Doctrine ORM используется как единственный слой persistence. Все изменения схемы базы данных оформляются исключительно через Doctrine Migrations. Ручные изменения схемы базы данных запрещены. В Docker-окружении миграции выполняются только в неинтерактивном режиме.

Разрешено:
php bin/console doctrine:migrations:migrate --no-interaction

Запрещено:
интерактивный ввод подтверждений в docker compose run;
применение миграций без фиксации в репозитории.

Причина:
docker compose run --rm не гарантирует корректную работу STDIN.

### Testing: Builders-first
Тестирование является частью архитектуры. Принят подход builders-first. Любая сущность в тестах создаётся только через Builder. Builder задаёт id (uuid строкой), createdAt (фиксированное значение) и остальные поля по умолчанию. В тестах запрещены случайные UUID, создание сущностей напрямую через new и генерация времени внутри сущностей. Тесты проверяют бизнес-инварианты, а не инфраструктуру.

### Структура тестов и автозагрузка
Структура тестов:
app/tests/Builders/AccountBuilder.php
app/tests/Entity/AccountTest.php

PSR-4 правило:
путь и namespace совпадают 1:1.
tests/Builders/AccountBuilder.php соответствует namespace App\Tests\Builders.

Для тестов используется autoload-dev в composer.json:
"autoload-dev": {
"psr-4": {
"App\\Tests\\": "tests/"
}
}

После любых изменений autoload обязательно выполняется:
composer dump-autoload

### CLI и Makefile
Все команды, выполняемые через Docker CLI, должны быть детерминированными, воспроизводимыми и неинтерактивными. Для миграций и CI-подобных шагов всегда используется флаг --no-interaction.

Пример:
make console cmd="doctrine:migrations:migrate --no-interaction"

## Последствия
Сущности детерминированы и тестируемы. UUID безопасно используются в API и интеграциях. Builders позволяют масштабировать модель без переписывания тестов. Dev-окружение воспроизводимо и предсказуемо. Исключены ошибки, связанные с интерактивными командами в Docker.

## Anti-goals
Генерация ID внутри сущностей. Использование auto-increment идентификаторов. Создание сущностей в тестах без builders. Случайные данные в unit-тестах. Интерактивные команды в docker compose run.
