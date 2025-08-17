<?php

namespace denisok94\telegram\app\model;

class UserModel
{
    public string $first_name;
    public string $last_name;
    public string $username;
    public string $language_code;
    public bool $is_premium = false;
    public string $photo_url;
    /** 
     * может ли бот отправлять личные сообщения пользователю из мини-приложения
     */
    public bool $allows_write_to_pm;
}