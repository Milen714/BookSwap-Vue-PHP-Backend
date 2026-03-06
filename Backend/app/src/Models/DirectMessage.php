<?php
namespace App\Models;

class DirectMessage
{
    public ?int $id;
    public ?int $senderId;
    public ?int $recipientId;
    public ?string $message;
    public ?bool $is_read;
    public ?\DateTime $created_at;

    public function __construct(){}

    public static function fromArray(array $data , int $senderId): DirectMessage {
        $dm = new DirectMessage();
        $dm->id = $data['id'] ?? null;
        $dm->senderId = $senderId;
        $dm->recipientId = $data['recipient_id'] ?? null;
        $dm->message = $data['message'] ?? null;
        $dm->is_read = isset($data['is_read']) ? (bool)$data['is_read'] : false;
        $dm->created_at = !empty($data['created_at']) ? new \DateTime($data['created_at']) : new \DateTime();
        return $dm;
    }

}