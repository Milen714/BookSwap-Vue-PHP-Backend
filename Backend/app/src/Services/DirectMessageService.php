<?php
namespace App\Services;

use App\Repositories\Interfaces\IDirectMessageRepository;
use App\Services\Interfaces\IDirectMessageService;
use App\Models\DirectMessage;

/**
 * Service for direct chat messaging operations.
 */
class DirectMessageService implements IDirectMessageService
{
    /**
     * Repository abstraction for message persistence and retrieval.
     */
    private IDirectMessageRepository $directMessageRepository;

    /**
     * @param IDirectMessageRepository $directMessageRepository Message repository.
     */
    public function __construct(IDirectMessageRepository $directMessageRepository) {
        $this->directMessageRepository = $directMessageRepository;
    }

    /**
     * Persist a new direct message.
     *
     * @param DirectMessage $directMessage Message payload.
     * @return mixed Repository-specific result.
     */
    public function saveDirectMessage(DirectMessage $directMessage) {
        return $this->directMessageRepository->saveDirectMessage($directMessage);
    }

    /**
     * Get chat history between two users.
     *
     * @param mixed $userId1 First user id.
     * @param mixed $userId2 Second user id.
     * @return array<DirectMessage> Ordered direct messages.
     */
    public function getDirectMessages($userId1, $userId2) : array {
        return $this->directMessageRepository->getDirectMessages($userId1, $userId2);
    }

    /**
     * Get users who exchanged messages with the given user.
     *
     * @param mixed $userId User id.
     * @return array Chat partner summaries.
     */
    public function getMyChatPartners($userId): array {
        return $this->directMessageRepository->getMyChatPartners($userId);
    }
}