# ADR-0002: Модульная структура проекта (MVP, без усложнений)

- **Статус:** Accepted
- **Дата:** 2025-12-28
- **Проект:** Conwix (conwix-platform)

---

## 1. Контекст

Проект развивается как MVP, но важно не допустить “раскидывания” кода по всему `app/src/`.
Нужна простая модульность:

- без конкурентных/маркетинговых названий модулей,
- без сложного DDD-слоения,
- но с жёстким правилом: **код фичи живёт в одном месте**.

React-части (Chat Center / BotFlow) считаются “модулями”, но физически остаются в `app/assets/react/<feature>/`.

---

## 2. Решение

### 2.1. Структура модулей backend (Symfony)

Все backend-модули размещаются в:

app/src/Modules/<ModuleName>/

Рекомендуемая (минимальная) структура внутри модуля:
app/src/Modules/<ModuleName>/
Controller/
Service/
Entity/
Repository/
DTO/
Contract/
Event/
Message/
_templates/

Важно:

- не нужно создавать все папки заранее;
- папка создаётся только если в ней есть код.

### 2.2. Структура модулей frontend (React)

React-модули размещаются в:

app/assets/react/<feature>/

На старте используются:

- `app/assets/react/chat-center/`
- `app/assets/react/botflow/`

React-модуль документируется как “модуль”, но не переносится в `app/src/Modules`.

---

## 3. Правила и ограничения (жёсткие)

### 3.1. Главное правило

Любой новый backend-код относится к конкретному модулю и размещается в:
`app/src/Modules/<ModuleName>/...`

### 3.2. Запреты (чтобы не возникла “свалка”)

Запрещено создавать в корне `app/src/` общие папки:

- `app/src/Service`
- `app/src/Utils`
- `app/src/Helpers`
- `app/src/Manager`

Если код “общий”, он всё равно должен жить в модуле, пока не станет очевидно, что он переиспользуется.

### 3.3. Контроллеры и логика

- В `Controller/` **нет** бизнес-логики.
- Контроллер вызывает `Service/` и возвращает ответ.
- Основная логика и оркестрация — в `Service/`.

### 3.4. Общий код (Shared) — только по факту

`app/src/Shared/` разрешено создавать только при выполнении условия:

- один и тот же код/подход повторился **минимум 2 раза** и реально нужен нескольким модулям.

Разрешённая минимальная структура Shared:

app/src/Shared/
Service/
DTO/
Contract/


---

## 4. Пример “правильно”

Пример модуля `Chat`:

app/src/Modules/Chat/
Controller/
ChatApiController.php
Service/
SendMessageService.php
LoadMessagesService.php
Entity/
Client.php
Message.php
Repository/
MessageRepository.php
DTO/
SendMessageRequest.php

React модуль:

app/assets/react/chat-center/

---

## 5. Пример “неправильно” (запрещено)

### 5.1. Размазанная структура

app/src/Controller/ChatController.php
app/src/Service/SendMessageService.php
app/src/Entity/Message.php

### 5.2. Свалка общих утилит

app/src/Utils/TelegramHelper.php
app/src/Helpers/AiHelper.ph

---

## 6. Последствия

### Плюсы

- Код фичи не размазывается по проекту.
- Простая структура, дружелюбная к MVP.
- Легко добавлять новые модули без рефакторинга всего проекта.

### Минусы

- При сильном росте домена может понадобиться более строгая архитектура.
- Нужна дисциплина соблюдать запреты на “общие папки” в `app/src/`.

---

## 7. Связанные ADR

- ADR-0001: Структура monorepo и размещение React внутри Symfony (`assets/react/*`)
