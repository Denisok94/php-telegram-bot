<?php

namespace denisok94\telegram\model;

/**
 * Summary of Chat
 */
class Chat
{
    public int $id;
    /**
     * private|group|supergroup|channel
     * @var string
     */
    public string $type;
    public ?string $title = null;
    public ?string $username = null;
    public ?string $first_name = null;
    public ?string $last_name = null;
    public bool $is_forum = false;
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->type = $data['type'];
        $this->title = $data['title'] ?? null;
        $this->username = $data['username'] ?? null;
        $this->first_name = $data['first_name'] ?? null;
        $this->last_name = $data['last_name'] ?? null;
        $this->is_forum = $data['is_forum'] ?? false;
    }
}