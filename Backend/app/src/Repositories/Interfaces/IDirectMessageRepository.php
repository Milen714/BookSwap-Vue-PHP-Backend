<?php
namespace App\Repositories\Interfaces;
use App\Models\DirectMessage;

interface IDirectMessageRepository
{
    public function saveDirectMessage(DirectMessage $directMessage);
    public function getDirectMessages($userId1, $userId2);
}