<?php

namespace denisok94\telegram\model;

/**
 * Summary of Photo
 */
class Photo
{
    public string $file_id;
    public string $file_unique_id;
    public int $file_size;
    public int $width;
    public int $height;

    public function __construct(array $data)
    {
        $this->file_id = $data['file_id'];
        $this->file_unique_id = $data['file_unique_id'];
        $this->file_size = $data['file_size'];
        $this->width = $data['width'];
        $this->height = $data['height'];
    }
}