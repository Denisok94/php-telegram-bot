<?php

namespace denisok94\telegram\model;

/**
 * Summary of Event
 */
class Event
{
    /**
     * @var string callback_query|inline_query|my_chat_member
     */
    public string $type;
    public ?int $id = null;
    public ?From $from = null;
    public Message|null $message = null;

    public $data = null;

    public $query = null;
    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? null;
        if (isset($data['message'])) {
            $this->message = new Message($data['message']);
        }
        if (isset($data['from'])) {
            $this->from = new From($data['from']);
        }
        $this->data = $data['data'] ?? null;
        $this->query = $data['query'] ?? null;

    }
}