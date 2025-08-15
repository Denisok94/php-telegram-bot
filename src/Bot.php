<?php

namespace denisok94\telegram;

use denisok94\telegram\model\Message;
use denisok94\telegram\model\Event;

/**
 * https://botphp.ru/guides/perviy-bot/own-class-bot
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
     * @var Message
     */
    public Message|null $message = null;

    /**
     * @var Event
     */
    public Event|null $event = null;
    /**
     * @var string|bool callback_query|inline_query|my_chat_member|message|document|bot_command|object_message
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
                    case 'bot_command':
                        $message = $this->data['message'];
                        $this->message = new Message($message);
                        $this->message->type = $this->type;
                        break;
                    case 'callback_query':
                    case 'inline_query':
                        $event = $this->data[$this->type];
                        $this->event = new Event($event);
                        $this->event->type = $this->type;
                        $this->message = $this->event->message;
                        break;
                    case 'my_chat_member':
                        break;
                    default:
                        break;
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
        } elseif (isset($this->data['message']['document'])) {
            return "document";
        } elseif (isset($this->data['message']['entities'])) {
            $entities = $this->data['message']['entities'][0];
            if (isset($entities['type']) && $entities['type'] == 'bot_command') {
                return "bot_command";
            }
            return "message";
        } elseif (isset($this->data['message']['text'])) {
            //если это простой текст боту, то вернем "message".
            return "message";
        } elseif (array_key_exists('message', $this->data)) {
            return "object_message";
        } else {
            return false;
        }
    }

    //----------------------------

    /**
     * Отправить текстовое сообщение
     */
    public function sendMessage($data = [])
    {
        $res = $this->sendApiQuery('sendMessage', $data);
        return $res;
    }
    /**
     * отправляем запрос к API Telegram, функция получает метод отправки
     * запроса и массив данных, отправляет запрос и возвращает результат в виде массива.
     * Подробней в http://botphp.ru/guides/perviy-bot/first-bot     
     */
    public function sendApiQuery($method, $data = [])
    {
        $ch = curl_init('https://api.telegram.org/bot' . $this->token . '/' . $method);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($data),
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_TIMEOUT => 10
        ]);
        $res = json_decode(curl_exec($ch), true);
        curl_close($ch);
        return $res;
    }
}