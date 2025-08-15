# php-telegram-bot
php telegram bot

___

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
                $resp = $bot->sendMessage(['chat_id' => $chat_id, 'text' => 'Чем я могу Вам помочь?']);
                break;
        }
    } else if ($type == 'message') { // Простой текст
        $message = $bot->getText();
        switch ($message) {
            case 'Button 1':
                $resp = $bot->sendMessage(['chat_id' => $chat_id, 'text' => 'Button 1 =)']);
                break;
            case 'Свяжи с оператором!!':
                $resp = $bot->sendMessage(['chat_id' => $chat_id, 'text' => 'Все операторы заняты! =)']);
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
    if (isset($resp['ok']) && $resp['ok'] != true) {
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