<?php

namespace denisok94\telegram\inline;

/**
 * Summary of Photo (фото)
 */
class Photo implements InlineResultInterface
{
    public string $id = uniqid();
    public string $type = 'photo';
    public string $photo_url;
    public string $thumb_url;
    public string $caption;
}