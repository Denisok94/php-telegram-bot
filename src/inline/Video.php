<?php

namespace denisok94\telegram\inline;

/**
 * Summary of Video (видео)
 */
class Video extends BaseResult
{
    public string $id;
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