<?php

namespace denisok94\telegram\model;


use denisok94\telegram\model\From;
use denisok94\telegram\model\Chat;
use denisok94\telegram\model\Document;

/**
 * Summary of Message
 */
class Message
{
    public int $id;
    public int $message_id;
    public int $date;
    /**
     * @var string message|document|bot_command|object_message
     */
    public string $type = 'message';
    public From $from;
    public Chat $chat;
    public ?string $text = null;
    public Message|null $reply_to_message = null;
    public ?Document $document = null;
    public ?array $entities = null;


    public function __construct(array $data)
    {
        $this->id = $this->message_id = $data['message_id'];
        $this->date = $data['date'];
        $this->text = $data['text'] ?? null;
        $this->from = new From($data['from']);
        $this->chat = new Chat($data['chat']);
        $this->entities = $data['entities'] ?? null;
        if (isset($data['reply_to_message'])) {
            $this->reply_to_message = new Message($data['reply_to_message']);
        }
        if (isset($data['document'])) {
            $this->document = new Document($data['document']);
        }
    }
}