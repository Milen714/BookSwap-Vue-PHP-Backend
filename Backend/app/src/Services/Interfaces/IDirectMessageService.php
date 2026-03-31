<?php
namespace App\Services\Interfaces;
use App\Models\DirectMessage;

/**
 * IDirectMessageService
 * 
 * Direct message service interface defining contract for managing user-to-user
 * messaging including message persistence and chat partner retrieval.
 */
interface IDirectMessageService
{
    /**
     * Save a direct message to persistent storage
     * 
     * @param DirectMessage $directMessage Message object to save
     * @return void
     */
    public function saveDirectMessage(DirectMessage $directMessage);
    
    /**
     * Retrieve message history between two users
     * 
     * @param int $userId1 First user ID
     * @param int $userId2 Second user ID
     * @return array List of DirectMessage objects between the two users
     */
    public function getDirectMessages($userId1, $userId2) : array;
    
    /**
     * Get list of all users the current user has messaged with
     * 
     * @param int $userId Current user ID
     * @return array List of chat partner user data
     */
    public function getMyChatPartners($userId): array;
}