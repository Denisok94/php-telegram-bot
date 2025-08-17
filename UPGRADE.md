Инструкции по обновлению для php Telegram Bot
=========================================

Upgrade from Helper 0.1.1
-----------------------
fixs inline query
add `setWebhook()` & `deleteWebhook()`

Upgrade from Helper 0.1.0
-----------------------
add:
- type message: `sticker`, `inline_query`
- `sendInlineResults()`, `model/InlineQuery()` + inline/*Results* models
- `editMessage()`, `deleteMessage()` & `deleteMessages()`
edit:
- `model/Event()` → `model/CallbackQuery()`

Upgrade from Helper 0.0.2
-----------------------
add:
- model: `File`, `FileInfo` & `Response`
- действия бота `sendChatAction(string $action):Response`
- получение информации и ссылки на файл `getFileInfo`
- скачивание файла `downloadFileById` & `downloadFileByUrl`
upd:
- all send() return `Response`
- `sendMessage(array $data):mixed` → `sendMessage(string|array $data): Response`

Upgrade from Helper 0.0.1
-----------------------
init