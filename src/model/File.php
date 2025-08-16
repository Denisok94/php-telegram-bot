<?php

namespace denisok94\telegram\model;

class File
{
    public string $file_id;
    public string $file_unique_id;
    public int $file_size;
    /** 
     * null if Photo
     */
    public ?string $file_name = null;
    /** 
     * null if Photo 
     */
    public ?string $mime_type = null;
    
}
