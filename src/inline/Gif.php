<?php

namespace denisok94\telegram\inline;

/**
 * Summary of Gif (гифка)
 */
class Gif implements InlineResultInterface
{
    public string $id = uniqid();
    public string $type = 'article';
    public string $gif_url;
    public string $thumb_url;
    public string $caption;
}