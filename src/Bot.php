<?php

namespace denisok94\telegram;

use Exception, Throwable;
use denisok94\telegram\Response;
use denisok94\telegram\request\Message;
use denisok94\telegram\request\CallbackQuery;
use denisok94\telegram\request\InlineQuery;
use denisok94\telegram\inline\InlineResultInterface;
use denisok94\telegram\model\FileInfo;

/**
 * https://botphp.ru/guides/perviy-bot/first-bot
 * https://botphp.ru/guides/perviy-bot/own-class-bot
 * ```php
 * $bot = new Bot([
 *  'bot_token' => '...', 
 *  'bot_name' => 'MyBot'
 * ]);
 * $bot->setData(file_get_contents('php://input'));
 * ```
 */
class Bot
{
    /**
     * бот токен.
     * @var string
     */
    private string $token;
    /**
     * имя бота
     * @var string
     */
    public string $bot_name;
    /**
     * id админа(создателя)
     * @var int|null
     */
    public int|null $admin_id = null;
    /**
     * Данные которые мы получим через webhook
     * @var array
     */
    public array $data = [];
    public int|null $update_id = null;
    /**
     * @var Message|null
     */
    public Message|null $message = null;
    /**
     * @var CallbackQuery|InlineQuery|null
     */
    public null $event = null;
    /**
     * @var string|bool callback_query|inline_query|my_chat_member|message|document|sticker|bot_command|object_message
     */
    public string|bool $type;

    //создаем экземпляр бота, при создании бота указываем токен

    /**
     * ```php
     * $bot = new Bot([
     *  'bot_token' => '...', 
     *  'bot_name' => 'MyBot'
     * ]);
     * ```
     * @param array $parama
     */
    public function __construct(array $parama)
    {
        $this->token = $parama['bot_token'];
        $this->bot_name = $parama['bot_name'] ?? '';
        $this->admin_id = $parama['admin_id'] ?? null;
    }

    /**
     * Данные которые мы получим через webhook/
     * ```php
     * $bot->setData(file_get_contents('php://input'));
     * ```
     * @param string $data json
     * @return self
     */
    public function setData(string $data): self
    {
        $this->data = json_decode($data, true);
        if ($this->data == false || $this->data == null) {
            $this->data = [];
        }
        $this->parseMessage();
        return $this;
    }

    public function parseMessage(): self
    {
        if (isset($this->data['update_id'])) {
            $this->update_id = $this->data['update_id'];
            if ($this->type = $this->getType()) {
                switch ($this->type) {
                    case 'message':
                    case 'document':
                    case 'sticker':
                    case 'bot_command':
                        $this->message = new Message($this->data['message']);
                        $this->message->type = $this->type;
                        break;
                    case 'object_message':
                        $message = $this->data['message'];
                        $this->message = new Message($message);
                        $this->message->type = $this->type;
                        unset($message['message_id']);
                        unset($message['date']);
                        unset($message['from']);
                        unset($message['chat']);
                        $this->message->other = $message;
                        break;
                    case 'callback_query':
                        $this->event = new CallbackQuery($this->data['callback_query']);
                        $this->message = $this->event->message;
                        break;
                    case 'inline_query':
                        $this->event = new InlineQuery($this->data['inline_query']);
                        break;
                    case 'my_chat_member':
                    default:
                        throw new Exception('Поддержка сообщений/событий типа: "' . $this->type . '" ещё пока не реализована =(');
                }
            }
        }
        return $this;
    }

    /**
     * получение id чата
     * @return int|null
     */
    public function getChatId(): int|null
    {
        return $this->message->chat->id ?? null;
    }

    /**
     * функция что бы получить текст сообщения из полученных данных
     * @return mixed|string|null
     */
    public function getText(): ?string
    {
        return match ($this->type) {
            "callback_query" => $this->event->data,
            "inline_query" => $this->event->query,
            default => $this->message->text,
        };
    }

    /**
     * Функция что бы получить тип сообщения
     * @return bool|string
     */
    public function getType(): bool|string
    {
        if (isset($this->data['callback_query'])) {
            return "callback_query";
        } elseif (isset($this->data['inline_query'])) {
            return "inline_query";
        } elseif (isset($this->data['my_chat_member'])) {
            return "my_chat_member";
        } elseif (isset($this->data['message'])) {
            $message = $this->data['message'];
            if (isset($message['document']) || isset($message['photo']) || isset($message['video']) || isset($message['audio'])) {
                return "document";
            } elseif (isset($message['sticker'])) {
                return "sticker";
            } elseif (isset($message['entities'])) {
                $entities = $message['entities'][0];
                if (isset($entities['type']) && $entities['type'] == 'bot_command') {
                    return "bot_command";
                }
                return "message";
            } elseif (isset($message['text'])) {
                return "message";
            } else {
                return "object_message";
            }
        } else {
            return false;
        }
    }

    public function isAdmin(): bool
    {
        return $this->admin_id ? ($this->message->from->id === $this->admin_id) : false;
    }

    //----------------------------

    /**
     * Отправить сообщение
     * @param string|array $data
     * @return Response
     */
    public function sendMessage(string|array $data): Response
    {
        if (is_array($data)) {
            $data['chat_id'] = $data['chat_id'] ?? $this->getChatId();
        } else {
            $data = [
                'chat_id' => $this->getChatId(),
                'text' => (string) $data,
                'parse_mode' => 'html',
            ];
        }
        return $this->sendApiQuery('sendMessage', $data);
    }

    /**
     * Отправить картинку
     * ```php
     * $file_path = __DIR__ . '/image.png';
     * $res = $bot->sendDocument([
     *  'chat_id' => $this->bot->getChatId(),
     *  'caption' => 'Описание файла',
     *  'photo' => curl_file_create($file_path, 'image/png', basename($file_path))
     * ]);
     * //
     * $file_id = "FILE_ID_ОТ_ТЕЛЕГРАМ";
     * $res = $bot->sendDocument([
     *  'chat_id' => $this->bot->getChatId(),
     *  'caption' => 'Описание файла',
     *  'photo' => $file_id
     * ]);
     * ```
     * @param array $data
     * @return Response
     */
    public function sendPhoto(array $data): Response
    {
        return $this->sendApiQuery('sendphoto', $data, true);
    }

    /**
     * Отправить файл
     * ```php
     * $file_path = __DIR__ . '/файлу.pdf';
     * $res = $bot->sendDocument([
     *  'chat_id' => $this->bot->getChatId(),
     *  'caption' => 'Описание файла',
     *  'document' => curl_file_create($file_path, 'application/pdf', basename($file_path)),
     *  'parse_mode' => 'Markdown'
     * ]);
     * ```
     * ```php
     * $file_id = "FILE_ID_ОТ_ТЕЛЕГРАМ";
     * $res = $bot->sendDocument([
     *  'chat_id' => $this->bot->getChatId(),
     *  'caption' => 'Описание файла',
     *  'document' => $file_id,
     *  'parse_mode' => 'Markdown',
     *  'protect_content' => true // Запретить сохранение и пересылку
     * ]);
     * ```
     * @param array $data
     * @return Response
     */
    public function sendDocument(array $data): Response
    {
        return $this->sendApiQuery('sendDocument', $data, true);
    }

    /**
     * Отправка индикации набора текста в Telegram
     * ```php
     * // Показываем, что бот печатает
     * $bot->sendChatAction('typing');
     * // Имитируем обработку запроса
     * sleep(2);
     * // Отправляем реальное сообщение
     * $bot->sendMessage("Привет! Я обработал ваш запрос.");
     * ```
     * Практическое применение:
     * - Улучшение пользовательского опыта — создает впечатление живого общения
     * - Индикация обработки — показывает, что бот работает над запросом
     * - Создание эффекта ожидания — помогает избежать ощущения “зависания”
     * Важные особенности:
     * - Индикатор отображается не более 5 секунд
     * - Можно отправлять несколько раз подряд
     * - Действие автоматически сбрасывается при отправке сообщения
     * - Рекомендуется использовать перед длительными операциями
     * Рекомендации по использованию:
     * - Не злоупотребляйте индикатором
     * - Используйте только когда действительно происходит обработка
     * - Комбинируйте с реальным временем обработки (sleep())
     * - Учитывайте, что индикатор может не отображаться у всех пользователей
     * 
     * @param string $action
     * `typing` — бот печатает текст;
     * `upload_photo` — загрузка фото;
     * `record_video` — запись видео;
     * `upload_video` — загрузка видео;
     * `record_audio` — запись аудио;
     * `upload_audio` — загрузка аудио;
     * `upload_document` — загрузка документа;
     * `find_location` — поиск местоположения;
     * `record_voice` — запись голосового сообщения;
     * @return Response
     */
    public function sendChatAction(string $action): Response
    {
        return $this->sendApiQuery('sendChatAction', [
            'chat_id' => $this->getChatId(),
            'action' => $action
        ], true);
    }

    //--------------

    /**
     * Обновить сообщение (только собственные сообщения бота)
     * @param string $message_id
     * @param string|array $data
     * @return Response
     */
    public function editMessage(string $message_id, string|array $data): Response
    {
        if (is_array($data)) {
            $data['chat_id'] = $data['chat_id'] ?? $this->getChatId();
            $data['message_id'] = $message_id;
        } else {
            $data = [
                'chat_id' => $this->getChatId(),
                'message_id' => $message_id,
                'text' => (string) $data,
                'parse_mode' => 'html',
            ];
        }
        return $this->sendApiQuery('editMessageText', $data, true);
    }

    /**
     * Удалить сообщение
     * https://botphp.ru/docs/api#deletemessage
     * @param int $chat_id
     * @param int $message_id
     * @return Response
     */
    public function deleteMessage(int $chat_id, int $message_id): Response
    {
        return $this->sendApiQuery('deleteMessage', [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
        ], true);
    }

    /**
     * Удалить сообщения
     * https://botphp.ru/docs/api#deletemessages
     * @param int $chat_id
     * @param array<int> $message_id
     * @return Response
     */
    public function deleteMessages(int $chat_id, array $message_id): Response
    {
        return $this->sendApiQuery('deleteMessages', [
            'chat_id' => $chat_id,
            'message_ids' => $message_id,
        ], true);
    }

    //--------------

    /**
     * Ответ на inline-запрос
     * @param InlineQuery $query
     * @param InlineResultInterface[]|array $results max 50
     */
    public function sendInlineResults(InlineQuery $query, array $results)
    {
        $ch = curl_init($this->getBaseBotUrl() . '/answerInlineQuery');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'inline_query_id' => $query->id,
            'results' => json_encode([$results])
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);
    }

    //--------------

    /**
     * Получить информацию о файле и ссылку для скачивания
     * Важные моменты:
     * - Временный URL действителен 60 минут
     * - После истечения срока нужно заново запросить URL через getFile
     * - Файл можно скачать только один раз по полученному URL
     * 
     * @param string $file_id
     * @return FileInfo|Response
     */
    public function getFileInfo(string $file_id): FileInfo|Response
    {
        $result = $this->sendApiQuery('getFile', ['file_id' => $file_id]);
        if ($result->ok && $result->result) {
            $file = new FileInfo($result->result);
            $file->file_url = 'https://api.telegram.org/file/bot' . $this->token . '/' . $file->file_path;
            return $file;
        }
        return $result;
    }

    /**
     * @param string $file_id
     * @param string $savePath путь куда сохранить файл
     * @throws Exception|Throwable
     */
    public function downloadFileById(string $file_id, string $savePath): void
    {
        /** @var FileInfo|Response $file */
        $file = $this->getFileInfo($file_id);
        if ($file instanceof FileInfo) {
            $ch = curl_init($file->file_url);
            $fp = fopen($savePath, 'wb');

            curl_setopt($ch, CURLOPT_FILE, $fp);
            curl_setopt($ch, CURLOPT_HEADER, 0);

            curl_exec($ch);
            curl_close($ch);
            fclose($fp);
            if (!file_exists($savePath)) {
                throw new Exception('Ошибка при скачивании файла');
            }
        } else {
            throw new Exception('Не удалось получить информацию об файле' . $file->description);
        }
    }

    /**
     * @param string $url 
     * @param string $savePath путь куда сохранить файл
     */
    public function downloadFileByUrl(string $url, string $savePath): void
    {
        $ch = curl_init($url);
        $fp = fopen($savePath, 'wb');

        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        curl_exec($ch);
        curl_close($ch);
        fclose($fp);
    }

    //----------------------------

    /**
     * отправляем запрос к API Telegram, функция получает метод отправки
     * запроса и массив данных, отправляет запрос и возвращает результат в виде массива.
     * @param string $method
     * @param mixed $data
     * @param bool $raw
     * @return Response
     * @throws Throwable
     */
    public function sendApiQuery(string $method, $data = [], bool $raw = false): Response
    {
        $ch = curl_init($this->getBaseBotUrl() . '/' . $method);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $raw ? $data : http_build_query($data),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => 10
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        try {
            return new Response(json_decode($response, true));
        } catch (Throwable $th) {
            throw $th;
        }
    }

    public function getBaseBotUrl()
    {
        return 'https://api.telegram.org/bot' . $this->token;
    }
}