<?php

namespace denisok94\telegram\inline;

/**
 * Summary of Video (видео)
 */
class Video implements InlineResultInterface
{
    public string $id = uniqid();
    public string $type = 'video';
    /**
     * https://example.com/video.mp4
     */
    public string $video_url;
    /** 
     * video/mp4
     */
    public string $mime_type;
    /**
     * https://example.com/thumb.jpg
     */
    public string $thumb_url;
    public string $title;
    public string $caption;
}