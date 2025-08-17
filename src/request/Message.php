<?php

namespace denisok94\telegram\request;

use denisok94\telegram\model\From;
use denisok94\telegram\model\Chat;
use denisok94\telegram\message\Document;
use denisok94\telegram\message\Photo;
use denisok94\telegram\message\Video;
use denisok94\telegram\message\Audio;

/**
 * Summary of Message
 */
class Message
{
    public int $id;
    public int $message_id;
    public int $date;
    /**
     * @var string message|document|bot_command|object_message
     */
    public string $type = 'message';
    public From $from;
    public Chat $chat;
    public ?string $text = null;
    public Message|null $reply_to_message = null;
    /**
     * @var string document|photo|video|audio
     */
    public ?string $document_type = null;
    public ?Document $document = null;
    /**
     * @var Photo[]|array|null
     */
    public ?array $photo = null;
    public ?Video $video = null;
    public ?Audio $audio = null;
    public ?array $entities = null;
    public ?array $sticker = null;

    public function __construct(array $data)
    {
        $this->id = $this->message_id = $data['message_id'];
        $this->date = $data['date'];
        $this->text = $data['text'] ?? null;
        $this->from = new From($data['from']);
        $this->chat = new Chat($data['chat']);
        $this->entities = $data['entities'] ?? null;
        if (isset($data['reply_to_message'])) {
            $this->reply_to_message = new Message($data['reply_to_message']);
        }
        if (isset($data['document'])) {
            $this->document = new Document($data['document']);
            $this->document_type = 'document';
        }
        if (isset($data['photo'])) {
            $this->photo = [];
            $this->document_type = 'photo';
            foreach ($data['photo'] as $key => $value) {
                $this->photo[] = new Photo($value);
            }
        }
        if (isset($data['video'])) {
            $this->video = new Video($data['video']);
            $this->document_type = 'video';
        }
        if (isset($data['audio'])) {
            $this->audio = new Audio($data['audio']);
            $this->document_type = 'audio';
        }
        if (isset($data['sticker'])) {
            $this->sticker = $data['sticker'];
            $this->document_type = 'sticker';
        }
    }
}