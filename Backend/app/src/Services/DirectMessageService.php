<?php
namespace App\Services;

use App\Repositories\Interfaces\IDirectMessageRepository;
use App\Services\Interfaces\IDirectMessageService;
use App\Models\DirectMessage;
class DirectMessageService implements IDirectMessageService
{
    private IDirectMessageRepository $directMessageRepository;

    public function __construct(IDirectMessageRepository $directMessageRepository) {
        $this->directMessageRepository = $directMessageRepository;
    }

    public function saveDirectMessage(DirectMessage $directMessage) {
        return $this->directMessageRepository->saveDirectMessage($directMessage);
    }

    public function getDirectMessages($userId1, $userId2) {
        return $this->directMessageRepository->getDirectMessages($userId1, $userId2);
    }
}