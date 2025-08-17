<?php

namespace denisok94\telegram\request;

use denisok94\telegram\model\From;

/**
 * Summary of InlineQuery
 * https://botphp.ru/docs/api#inlinequery
 */
class InlineQuery
{
    /** уникальный идентификатор запроса */
    public string $id;
    public From $from;
    /** текст запроса пользователя */
    public string $query;
    /** смещение для постраничной навигации */
    public string $offset;
    /** private|group|supergroup|channel */
    public ?string $chat_type;
    public ?string $location;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->from = new From($data['from']);
        $this->query = $data['query'];
        $this->offset = $data['offset'];
        $this->chat_type = $data['chat_type'] ?? null;
        $this->location = $data['location'] ?? null;
    }
}