# php telegram bot

# Установка

Run:

```bash
composer require --prefer-dist denisok94/telegram-bot
# or
php composer.phar require --prefer-dist denisok94/telegram-bot
```

or add to the `require` section of your `composer.json` file:

```json
"denisok94/telegram-bot": "*"
```

```bash
composer update
# or
php composer.phar update
```

# Методы

| Method | Description |
|----------------|:----------------|
| setData(string $data) | Сообщение от пользователя которое прислал Телеграм |
| getChatId(): ?int | ид чата в котором идёт беседа |
| getType(): string | Тип присланного сообщения/события |
| isAdmin() | Является автор сообщения адсином бота |
| getText(): ?string | текст сообщения |
| | |
| sendMessage(string\|array $data) | Отправить сообщение |
| sendPhoto(array $data) | Отправить картинку |
| sendDocument(array $data) | Отправить файл (фото/видео/музыку/гифку и т.д.) |
| sendChatAction(string $action) | Отправка индикации набора текста |
| editMessage(string $message_id, string\|array $data) | Обновить сообщение (только собственные сообщения бота) |
| deleteMessage(int $chat_id, int $message_id) | Удалить сообщение |
| deleteMessages(int $chat_id, array $message_id) | Удалить сообщения |
| sendInlineResults(InlineQuery $query, array $results) | Ответ на inline-запрос |
| getFileInfo(string $file_id) | Получить информацию о файле и ссылку для скачивания |
| downloadFileById(string $file_id, string $savePath) | Скачать файл по его ид |
| downloadFileByUrl(string $url, string $savePath) | Скачать файл по url |
| | |
| sendApiQuery(string $method, $data = [], bool $raw = false) | отправляем свой кастомный запрос к API Telegram |

# Использование

```php
// webhook.php
use denisok94\telegram\Bot;

$bot = new Bot([
    'bot_token' => $botToken,
    'bot_name' => $bot_name,
    'admin_id' => $admin_id,
]);
$bot->setData(file_get_contents('php://input'));

if ($chat_id = $bot->getChatId()) {
    $type = $bot->getType();
    if ($type == 'bot_command') { // отправлена команда "/command"
        $message = $bot->getText();
        $message = str_replace($bot_name, "", $message);
        switch ($message) {
            case '/start':
                // Получили текст start и проверяем, от админа сообщение или нет
                if ($bot->isAdmin()) {
                    $text = 'Привет, создатель!';
                } else {
                    $text = 'Здравствуйте, товарищ!';
                }
                $resp = $bot->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => $text,
                    'reply_markup' => json_encode([
                        'keyboard' => [
                            [
                                [
                                    'text' => 'Button 1',
                                    'callback_data' => 'keyboard_test_2',
                                ],
                                [
                                    'text' => 'Button 2',
                                    'callback_data' => 'keyboard_test_2',
                                ]
                            ]
                        ],
                        'one_time_keyboard' => true,
                        'resize_keyboard' => true,
                    ])
                ]);
                break;
            case '/help':
                $resp = $bot->sendMessage('Чем я могу Вам помочь?');
                break;
        }
    } else if ($type == 'message') { // Простой текст
        $message = $bot->getText();
        switch ($message) {
            case 'Button 1':
                $resp = $bot->sendMessage('Button 1 =)');
                break;
            case 'Свяжи с оператором!!':
                $resp = $bot->sendMessage('Все операторы заняты! =)');
                break;
        }
    } else {
        $resp = $bot->sendMessage([
            'chat_id' => $chat_id,
            'text' => 'Я пока не умею обрабатывать такие данные. Подождите, когда разработчик придумает, как реагировать на такие сообщения.',
            "reply_to_message_id" => $bot->message->id ?? null,
            'reply_markup' => json_encode([
                'keyboard' => [
                    [
                        [
                            'text' => 'Свяжи с оператором!!',
                            'callback_data' => 'keyboard_help',
                        ],
                    ]
                ],
                'one_time_keyboard' => true,
                'resize_keyboard' => true,
            ]),
        ]);
    }
    if ($resp->ok != true) {
        $bot->sendMessage([
            'chat_id' => $bot->admin_id,
            'text' => "<pre>\n" . print_r($resp,true) . "\n</pre>",
            'parse_mode' => 'html',
        ]);
    }
}
```

# файлы

## Отправить/загрузить файлы в чат

```php
$chat_id = $bot->getChatId();
$file_path = __DIR__ . '/files/denis_Charlotte.png';
if (file_exists($file_path)) {
    $resp = $bot->sendDocument([
        'chat_id' => $chat_id,
        'caption' => 'Отправка картинки документом',
        'document' => curl_file_create($file_path, 'image/png', basename($file_path))
    ]);
    $resp = $bot->sendPhoto([
        'chat_id' => $chat_id,
        'caption' => 'Отправка картинки',
        'photo' => curl_file_create($file_path, 'image/png', basename($file_path))
    ]);
}
```

## Повторно отправить файл используя ид телеграма

```php
$chat_id = $bot->getChatId();
$file_id = "FILE_ID_ОТ_ТЕЛЕГРАМ";
if (file_exists($file_path)) {
    $resp = $bot->sendDocument([
        'chat_id' => $chat_id,
        'caption' => 'Отправка картинки документом',
        'document' => $file_id
    ]);
    $resp = $bot->sendPhoto([
        'chat_id' => $chat_id,
        'caption' => 'Отправка картинки',
        'photo' => $file_id
    ]);
}
```

## Отправить несколько фото (до 10)

```php
$chat_id = $bot->getChatId();
// Показываем, что бот отправляет фото
$resp = $bot->sendChatAction('upload_photo');
// Массив путей к файлам
$photo_paths = [
   __DIR__. '/files/screenshots0.jpg',
   __DIR__. '/files/screenshots1.jpg',
   __DIR__. '/files/screenshots2.jpg',
];
$arrayQuery = [
    'chat_id' => $chat_id,
    'media' => json_encode([
        ['type' => 'photo', 'media' => 'attach://screenshots0.jpg', 'caption' => 'Описание альбома'],
        ['type' => 'photo', 'media' => 'attach://screenshots1.jpg'],
        ['type' => 'photo', 'media' => 'attach://screenshots2.jpg'],
    ]),
    'screenshots0.jpg' => curl_file_create($photo_paths[0], 'image/jpeg', basename($photo_paths[0])),
    'screenshots1.jpg' => curl_file_create($photo_paths[1], 'image/jpeg', basename($photo_paths[1])),
    'screenshots2.jpg' => curl_file_create($photo_paths[2], 'image/jpeg', basename($photo_paths[2])),
];

$resp = $bot->sendApiQuery('sendMediaGroup', $arrayQuery, true);
```

## Скачать полученный файл

```php
$message = $bot->message;
$type = $message->document_type;
if ($type != 'photo') {
    $file_id = $message->{$type}->file_id;
    $file_name = $message->{$type}->file_name;
    $savePath = __DIR__ . '/' . $file_name;
    try {
        // бот сам получить информацию о файле и скачает его в нужное место
        $bot->downloadFileById($file_id, $savePath);
    } catch (Throwable $th) {
        // Ошибка при скачивании файла
    }
} else if ($message->photo) { // вариант 2
    $photo = $message->photo[array_key_last($message->photo)];
    $file_id = $photo->file_id;
    // сами получаем информацию об файле
    $file = $bot->getFileInfo($file_id);
    if ($file instanceof \denisok94\telegram\model\FileInfo) {
        // проверяем что всё ли ок
        $extension = pathinfo($file->file_path, PATHINFO_EXTENSION);
        $file_name = pathinfo($file->file_path, PATHINFO_BASENAME);
        $savePath = __DIR__ ."/$file_name.$extension";
        // и скачиваем по ссывлке куда нам надо и потом прверяем
        $bot->downloadFileByUrl($file->file_url, $savePath);
        if (file_exists($savePath)) {
            // code
        } else {
            // Ошибка при скачивании файла
        }
    } else {
        // Не удалось получить информацию об файле, см $file->description
    }
}
```
