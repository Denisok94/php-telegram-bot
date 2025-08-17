<?php

namespace denisok94\telegram\message;

/**
 * Summary of Video
 */
class Video extends File
{
    public int $width;
    public int $height;
    public int $duration;
    public Photo $thumbnail;
    public Photo $thumb;

    public function __construct(array $data)
    {
        $this->file_id = $data['file_id'];
        $this->file_name = $data['file_name'];
        $this->mime_type = $data['mime_type'];
        $this->file_unique_id = $data['file_unique_id'];
        $this->file_size = $data['file_size'];
        $this->width = $data['width'];
        $this->height = $data['height'];
        $this->duration = $data['duration'];
        $this->thumbnail = new Photo($data['thumbnail']);
        $this->thumb = new Photo($data['thumb']);
    }
}