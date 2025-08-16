<?php

namespace denisok94\telegram\model;

class FileInfo
{
    public string $file_id;
    public string $file_unique_id;
    public int $file_size;
    public string $file_path;
    public ?string $file_url = null;
    
    public function __construct(array $data)
    {
        $this->file_id = $data['file_id'];
        $this->file_unique_id = $data['file_unique_id'];
        $this->file_size = $data['file_size'];
        $this->file_path = $data['file_path'];
    }
}
