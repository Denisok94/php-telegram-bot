<?php

namespace denisok94\telegram\request;

use denisok94\telegram\model\From;

abstract class Event
{
    /** уникальный идентификатор запроса */
    public string $id;
    public From $from;
}
