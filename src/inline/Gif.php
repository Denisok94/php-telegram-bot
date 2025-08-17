<?php

namespace denisok94\telegram\inline;

/**
 * Summary of Gif (гифка)
 */
class Gif extends BaseResult
{
    public string $id;
    public string $type = 'gif';
    public string $gif_url;
    public string $thumb_url;
    public string $caption;
}