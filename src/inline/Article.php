<?php

namespace denisok94\telegram\inline;

/**
 * Summary of Article (статья)
 */
class Article extends BaseResult
{
    public string $id;
    public string $type = 'article';
    public string $title = '';
    public string $description = '';
    public string $thumb_url = '';
    /**
     * @var array
     * ```php 
     * [
     *  'message_text' => 'Текст статьи',
     *  'parse_mode' => 'HTML'
     * ]
     * ```
     */
    public array $input_message_content = ['message_text' => 'Текст статьи', 'parse_mode' => 'HTML'];

}