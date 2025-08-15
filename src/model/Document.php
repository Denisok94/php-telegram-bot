<?php

namespace denisok94\telegram\model;

/**
 * Summary of Document
 */
class Document
{
    public string $file_id;
    public string $file_name;
    public string $mime_type;
    public string $file_unique_id;
    public int $file_size;
    public function __construct(array $data)
    {
        $this->file_id = $data['file_id'];
        $this->file_name = $data['file_name'];
        $this->mime_type = $data['mime_type'];
        $this->file_unique_id = $data['file_unique_id'];
        $this->file_size = $data['file_size'];
    }
}