# ADR-0007: Dialog-Centric Core (MVP)

## Статус
Accepted

## Контекст
Conwix строится как диалого-центричная система.
Диалог является первичной сущностью, из которой вырастают клиенты, действия и автоматизация.

На этапе MVP необходимо было:
- создать минимальный, но полноценный диалоговый контур
- избежать преждевременных ролей, каналов и security
- обеспечить простой API, пригодный для UI и дальнейшего расширения

## Принятое решение

В MVP зафиксирована следующая модель:

### Сущности
- Account — владелец системы
- Company — рабочий контекст владельца
- Client — внешний пользователь (абстрактный, без канала)
- Conversation — диалог клиента с компанией
- Message — сообщения в диалоге (direction: in | out)

### Принципы
- UUID используется везде как идентификатор
- Все действия выполняются в контексте Company
- Доступ проверяется через owner Company
- Каналы и роли сознательно исключены из MVP

### API (baseline)

Account:
- POST /register
- GET /me

Company:
- POST /companies
- GET /companies
- GET /companies/{companyId}/conversations

Messaging:
- POST /events/incoming-message
- POST /conversations/{conversationId}/reply
- GET /conversations/{conversationId}/messages

Scoped (UI-ready):
- GET /companies/{companyId}/conversations/{conversationId}/messages
- POST /companies/{companyId}/conversations/{conversationId}/reply

## Последствия

Плюсы:
- Чёткая диалоговая модель
- Простой и расширяемый API
- Готовность к UI и каналам

Минусы:
- Нет ролей (Operator появится позже)
- Нет каналов (Telegram, Web и т.д.)
- Нет security слоя (JWT / sessions)

## Что сознательно НЕ делаем в этом ADR
- не добавляем Channel
- не добавляем Operator
- не вводим ACL / RBAC
- не меняем существующий API

Все изменения выше этого уровня требуют нового ADR.
