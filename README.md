# php telegram bot

## Установка

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

## Описание

| Method | Description |
|----------------|:----------------|
| setData(string $data) | Сообщение от пользователя которое прислал Телеграм |
| getChatId(): ?int | Ид чата в котором идёт беседа |
| getType(): string | Тип присланного сообщения/события |
| isAdmin() | Является автор сообщения адсином бота |
| getText(): ?string | Текст сообщения |
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
| sendApiQuery(string $method, $data = [], bool $raw = false) | Отправляем свой кастомный запрос к API Telegram, [подробнее обо всех методах на руском](https://botphp.ru/docs/api) |

| Property | Tupe | Description |
|----------------|:----------------|----------------|
| $bot_name | string | |
| $admin_id | int\|null | |
| $data | array | Оригинальное сообщение от Telegram |
| $update_id | int\|null | |
| $type | string\|bool | Тип сообщения |
| $message | Message\|null | Класс сообщеия |
| $event | CallbackQuery\|InlineQuery\|null | Класс события |

### Планы

- &#x2611; Обрабортка `inline_query` сообщений
- &#x2610; Отдельные методы отправки для каждого типа файла (видео, аудио, стикеров и тд)
- &#x2610; Массовая отправка фото (галереи)
- &#x2610; Массовая отправка других типов файлов (_если возможно_)
- &#x2610; Доработка шаблонов ответа для `inline_query`
- &#x2610; Реакции на сообщения
- &#x2610; Реакции на события в группах/каналах
- &#x2610; Поддержка типов событий `edited_channel_post`, `my_chat_member` и других
- &#x2610; Всё переделать

|checked|unchecked|crossed|
|---|---|---|
|&check;|_|&cross;|
|&#x2611;|&#x2610;|&#x2612;|

## Использование

### Регистрация webhook

указывает Телеграму url, куда ему отправлять нам сообщения отправленные боту
```php
$bot = new Bot('123456:qwerty');
$url = "https://ваш-домен.ru/webhook.php";
$bot->setWebhook($url);
// or
$bot->setWebhook([
    'url' => $url,
    'max_connections' => 5,
    'allowed_updates' => json_encode(["message", "callback_query"]),
]);
// удалить текущий webhook
$bot->deleteWebhook();
```

### Примеры

```php
// webhook.php
use denisok94\telegram\Bot;

$bot = new Bot([
    'bot_token' => $botToken,
    'bot_name' => $bot_name,
    'admin_id' => $admin_id,
]);
// получаем сообщение от Телеграмма
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
            case 'Button 2':
                $resp = $bot->sendMessage('Button =)');
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

## Работа с файлами

### Отправить/загрузить файлы в чат

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

### Повторно отправить файл используя ид телеграма

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

### Отправить несколько фото (до 10)

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

### Скачать полученный файл

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

## inline_keyboard и callback_query

```php
$type = $bot->getType();
$chat_id = $bot->getChatId();
if ($type == 'bot_command') {
    $message = $bot->getText();
    switch ($message) {
        case '/start':
            $resp = $bot->sendMessage([
                    'chat_id' => $chat_id,
                    'text' => 'test inline_keyboard',
                    'reply_markup' => json_encode([
                        'inline_keyboard' => [
                            [
                                [
                                    'text' => 'Button 1',
                                    'callback_data' => 'inline_test_1',
                                ]
                            ],
                            [
                                [
                                    'text' => 'Button 2',
                                    'callback_data' => 'inline_test_2',
                                ],
                                [
                                    'text' => 'Button 3',
                                    'callback_data' => 'inline_test_3',
                                ]
                            ]
                        ],
                        'resize_keyboard' => true,
                    ]),
                ]);
            break;
    }
} else if ($type == 'callback_query') {
    $message = $bot->getText();
    switch ($message) {
        case 'inline_test_1':
        case 'inline_test_2':
        case 'inline_test_3':
            $resp = $bot->editMessage($bot->message->id, [
                "text" => "Реакция принята!\n а теперь этап 2й)",
                'reply_markup' => json_encode([
                    'inline_keyboard' => [
                        [
                            [
                                'text' => '1',
                                'callback_data' => 'inline_test_4',
                            ],
                            [
                                'text' => '2',
                                'callback_data' => 'inline_test_5',
                            ]
                        ]
                    ],
                    'resize_keyboard' => true,
                ]),
            ]);
            break;
        case 'inline_test_4':
        case 'inline_test_5':
            $resp = $bot->editMessage($bot->message->id, "Спасибо за участие в опросе!");
            break;
    }
}
```

## inline_query

в разработке...
https://botphp.ru/docs/api#inlinequeryresult
```php
$bot = new Bot('123456:qwerty');
$type = $bot->getType();
if ($type == 'inline_query') {
    $results = [];
    $search = $this->bot->getText();
    if (mb_strlen($search) < 10) {
        // испольование готовых шаблонов
        $article = new \denisok94\telegram\inline\Article();
        $article->id = uniqid();
        $article->title = "Маловато будет!";
        $article->description = "Для поиска необходимо минимум 10 символов";
        $article->thumb_url = 'https://example.com/photo.jpg';
        $article->input_message_content = [
            'message_text' => "-article: " . $article->id
        ];
        $results[] = $article;
    } else {
        // свой вариант, если шаблонов мало
        for ($i = 0; $i < 10; $i++) {
            $results[] = [
                'type' => 'article',
                'id' => uniqid(),
                'title' => "Результат $i",
                'description' => 'Описание',
                'thumb_url' => 'https://example.com/photo.jpg',
                'input_message_content' => [
                    'message_text' => 'Текст сообщения ' . $i // что будет отправлено при выборе пользователем
                ]
            ];
        }
    }
    $resp = $this->bot->sendInlineResults($results); // максимум 50 результатов за раз
} else if ($type == 'message') {
    $message = $bot->getText();
    // получит выбранный объект
    if (isset($bot->data['message']['via_bot'])) {
        $bot_id = $bot->data['message']['via_bot']['id'];
        // проверяем, что это выбор от нашего бота (малолди в чате есть другие)
        switch ($message) {
            case 'Текст сообщения 1':
            case 'Текст сообщения 2':
            default:
                $resp = $this->bot->sendMessage('Выбранный объект: "' . $message . '"');
                break;
        }
    }
}
```

## MiniApp

```html
<script src="https://telegram.org/js/telegram-web-app.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function ()
    {
        const tg = Telegram.WebApp;
        // Получаем данные initData
        const initData = tg.initData;
        tg.ready(); // сообщаем Telegram, что готовы
        // отправляем данные на валидацию
        fetch("api/get-user", {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            body: JSON.stringify({ initData })
        })
            .then(response => response.json())
            .then(data => {
                console.log("Ответ:", data);
            })
            .catch(error => {
                console.error("Ошибка запроса:", error);
            });
    });
</script>
```

```php
// ~ApiController
public function actionGetUser()
{
    try {
        // Получаем данные из параметра tgWebAppData
        $initData = json_decode(file_get_contents('php://input') ?? '', true)['initData'] ?? '';
        // Проверяем и декодируем данные
        $result = \denisok94\telegram\InitData::isValid($initData, $botToken, true);
        if ($result['isValid'] == true) {
            return $this->sendSuccess($result['data']['parsed']); // 200
        } else {
            $result['data'] = $result['data']['parsed'] ?? $initData;
            return $this->sendError("Bad Request", $result); // 400
        }
    } catch (\Exception $e) {
        return $this->sendError($e->getMessage()); // 500
    }
}
```
