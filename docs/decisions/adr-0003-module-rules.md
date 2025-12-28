# ADR-0003: Правила модулей (нейминг, границы, размещение кода)

- **Статус:** Accepted
- **Дата:** 2025-12-28
- **Проект:** Conwix (conwix-platform)

---

## 1. Контекст

В проекте используется простая модульная структура для MVP, чтобы:

- код фичи не был раскидан по всему `app/src/`,
- не создавать “общие свалки”,
- сохранить скорость разработки.

Нужно зафиксировать:

- как называть модули,
- что считается модулем,
- куда класть разные типы кода внутри модуля.

---

## 2. Решение

### 2.1. Что считается модулем

**Модуль** = функциональная зона продукта, которую можно объяснить одной фразой.

Примеры модулей (примерные, не обязательные):

- Chat
- BotFlow
- Integrations
- Accounts
- Notifications

Модуль НЕ создаётся “на всякий случай”.  
Создаём модуль только когда есть реальный код/задача.

---

## 3. Нейминг модулей

### 3.1. Формат имени

- Имя модуля: **PascalCase**, **одно слово**, **существительное**
- Только латиница, без дефисов и подчёркиваний

✅ Правильно:

- `Chat`
- `BotFlow`
- `Integrations`
- `Billing`

❌ Неправильно:

- `chat-center`
- `bot_flow`
- `ChatCenterModule`
- `module-chat`

### 3.2. Правило переименований

Переименование модуля допускается только через отдельный PR и отдельное ADR (если влияет на публичные namespaces/API).

---

## 4. Размещение кода

### 4.1. Backend (Symfony)

Любой backend-код размещается строго в:
`app/src/Modules/<ModuleName>/...`

Разрешённые папки (создаём по необходимости):

- `Controller/` — HTTP/API контроллеры (без бизнес-логики)
- `Service/` — сценарии/оркестрация/логика модуля
- `Entity/` — сущности Doctrine (если модуль хранит данные)
- `Repository/` — Doctrine репозитории модуля
- `DTO/` — DTO/Request/Response структуры модуля
- `Contract/` — интерфейсы модуля (если нужно)
- `Event/` — доменные/приложенческие события
- `Message/` — сообщения для messenger/очередей (если используем)
- `_templates/` — twig-шаблоны, относящиеся к модулю

### 4.2. Frontend (React)

React-модули (Chat Center, BotFlow) считаются модулями на уровне документации, но физически располагаются в:

`app/assets/react/<feature>/`

Правило:

- `assets/react/chat-center/` — только Chat Center
- `assets/react/botflow/` — только BotFlow
- общий код допускается только как:
  `assets/react/_shared/` (создаётся по факту повторного использования)

---

## 5. Правила “не раскидывать код”

### 5.1. Запрет “общих свалок” в `app/src/`

Запрещены папки:

- `app/src/Service`
- `app/src/Utils`
- `app/src/Helpers`
- `app/src/Manager`

### 5.2. Shared код (только по факту)

`app/src/Shared/` допускается только если:

- один и тот же код реально нужен минимум двум модулям,
- и это подтверждено конкретными местами использования.

Разрешённая минимальная структура Shared:

app/src/Shared/
Service/
DTO/
Contract/


---

## 6. Примеры (правильно/неправильно)

### 6.1. Правильно

app/src/Modules/Chat/Controller/ChatApiController.php
app/src/Modules/Chat/Service/SendMessageService.php
app/src/Modules/Chat/Entity/Message.php
app/assets/react/chat-center/...

### 6.2. Неправильно

app/src/Controller/ChatController.php
app/src/Service/SendMessageService.php
app/src/Entity/Message.php
app/assets/react/components/...

---

## 7. Связанные ADR

- ADR-0001: структура monorepo и `assets/react/*`
- ADR-0002: базовая модульная структура (MVP)
