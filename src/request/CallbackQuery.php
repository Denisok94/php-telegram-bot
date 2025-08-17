<?php

namespace denisok94\telegram\request;

use denisok94\telegram\model\From;
use denisok94\telegram\request\Message;

/**
 * Summary of CallbackQuery
 */
class CallbackQuery
{
    public string $id;
    public From $from;
    public Message|null $message = null;
    public string $chat_instance;
    public string $data;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->from = new From($data['from']);
        if (isset($data['message'])) {
            $this->message = new Message($data['message']);
        }
        $this->chat_instance = $data['chat_instance'];
        $this->data = $data['data'];
    }
}