<?php

namespace denisok94\telegram\bot;

/**
 * https://botphp.ru/guides/perviy-bot/own-class-bot
 */
class Bot
{
    //бот токен.
    private $token;
    //Данные которые мы получим через webhook
    public $data = [];
    //Массив с данными о пользователе у которого диалог с ботом
    public $user = [];
    public $chat = [];
    //создаем экземпляр бота, при создании бота указываем токен
    public function __construct($token)
    {
        //сохраняем в свойства полученный токен
        $this->token = $token;
        //получаем данные от webhook
        $this->data = json_decode(file_get_contents('php://input'), true);
        if ($this->data == false || $this->data == null) {
            $this->data = [];
        }
        //записываем информация о пользователе
        $this->setUser();
    }

    //Функция что бы установить пользователя в свойство user
    public function setUser()
    {
        if ($this->getType() == "callback_query") {
            $this->user = $this->data['callback_query']['from'];
        } elseif ($this->getType() == "inline_query") {
            $this->user = $this->data['inline_query']['from'];
        } else {
            if (isset($this->data['message'])) {
                $this->user = $this->data['message']['from'];
            } else {
                $this->user = null;
            }
        }
        //исходя из типа полученного update записываем информацию о текущем чате
        if ($this->getType() == "callback_query") {
            $this->chat = $this->data['callback_query']['message']['chat'];
        } elseif ($this->getType() == "inline_query") {
            // $this->chat = $this->data['inline_query']['from'];
        } else {
            if (isset($this->data['message'])) {
                $this->chat = $this->data['message']['chat'];
            } else {
                $this->chat = null;
            }
        }
    }

    //получение id чата
    public function getUserId()
    {
        return $this->user['id'] ?? null;
    }

    //получение id чата
    public function getChatId()
    {
        return $this->chat['id'] ?? null;
    }

    //Функция что бы получить тип сообщения
    //Другие типы сообщений мы рассмотрим в следующих уроках
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

    //функция что бы получить текст сообщения из полученных данных
    public function getText(): string
    {
        if ($this->getType() == "callback_query") {
            return $this->data['callback_query']['data'];
        } elseif ($this->getType() == "inline_query") {
            return $this->data['inline_query']['query'];
        }
        return $this->data['message']['text'];
    }
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