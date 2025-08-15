<?php

namespace denisok94\telegram\model;

/**
 * Summary of From
 */
class From
{
    public int $id;
    public bool $is_bot;
    public string $username;
    public string $first_name;
    public ?string $last_name = null;
    public ?string $language_code = null;
    public bool $is_premium = false;
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->is_bot = $data['is_bot'];
        $this->username = $data['username'];
        $this->first_name = $data['first_name'];
        $this->last_name = $data['last_name'] ?? null;
        $this->language_code = $data['language_code'] ?? null;
        $this->is_premium = $data['is_premium'] ?? false;
    }
}