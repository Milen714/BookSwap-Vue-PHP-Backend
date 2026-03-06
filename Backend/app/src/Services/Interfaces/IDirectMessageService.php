<?php
namespace App\Services\Interfaces;
use App\Models\DirectMessage;

interface IDirectMessageService
{
    public function saveDirectMessage(DirectMessage $directMessage);
    public function getDirectMessages($userId1, $userId2);
}