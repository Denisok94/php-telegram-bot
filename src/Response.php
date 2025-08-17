<?php

namespace denisok94\telegram;

/**
 * Ответ от телеграмма
 */
class Response
{
    public bool $ok;
    /**
     * @var mixed|null
     */
    public $result;
    public ?int $error_code;
    public ?string $description;

    public function __construct(array $data)
    {
        $this->ok = $data['ok'];
        $this->result = $data['result'] ?? null;
        $this->error_code = $data['error_code'] ?? null;
        $this->description = $data['description'] ?? null;
    }
}
