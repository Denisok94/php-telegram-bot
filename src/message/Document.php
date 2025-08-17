<?php

namespace denisok94\telegram\message;

/**
 * Summary of Document
 */
class Document extends File
{

    public function __construct(array $data)
    {
        $this->file_id = $data['file_id'];
        $this->file_name = $data['file_name'];
        $this->mime_type = $data['mime_type'];
        $this->file_unique_id = $data['file_unique_id'];
        $this->file_size = $data['file_size'];
    }
}