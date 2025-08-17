<?php

namespace denisok94\telegram\app\model;

class InitDataModel
{
    public UserModel $user;
    /**
     * @var int Уникальный идентификатор сессии чата
     */
    public int $ichat_instanced;
    /**
     * @var string:
     * - `private` — личный чат;
     * - `group` — групповой чат;
     * - `supergroup` — супергруппа;
     * - `channel` — канал;
     * - `sender` — другое место.
     */
    public string $chat_type;
    public int $auth_date;
    public ?string $start_param = null;
}