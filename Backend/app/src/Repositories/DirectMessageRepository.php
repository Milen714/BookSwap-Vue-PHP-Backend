<?php
namespace App\Repositories;
use App\Framework\Repository;
use App\Repositories\Interfaces\IDirectMessageRepository;
use App\Models\User;
use App\Models\Enums\UserRole;
use App\Models\DirectMessage;
use PDO;
use PDOException;
use App\Exceptions\RepositoryException;

class DirectMessageRepository extends Repository implements IDirectMessageRepository {
    public function saveDirectMessage(DirectMessage $directMessage) {
        try {
            $pdo = $this->connect();
            $query = 'INSERT INTO direct_messages (sender_id, recipient_id, message) VALUES (:senderId, :recipientId, :message)';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':senderId', $directMessage->senderId);
            $stmt->bindParam(':recipientId', $directMessage->recipientId);
            $stmt->bindParam(':message', $directMessage->message);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new RepositoryException("Error saving direct message.", $e);
        }
    }

    public function getDirectMessages($userId1, $userId2) : array {
        try {
            $pdo = $this->connect();
            $query = 'SELECT * FROM direct_messages WHERE (sender_id = :userId1 AND recipient_id = :userId2) OR (sender_id = :userId2 AND recipient_id = :userId1) ORDER BY created_at ASC';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':userId1', $userId1);
            $stmt->bindParam(':userId2', $userId2);
            $stmt->execute();
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $messages = [];
            foreach ($data as $row) {
                $messages[] = (new DirectMessage())->fromPDOArray($row);
            }
            return $messages;
            

        } catch (PDOException $e) {
            throw new RepositoryException("Error fetching direct messages.", $e);
        }
    }
    public function getMyChatPartners($userId): array {
        try {
            $pdo = $this->connect();
            $query = '
                SELECT u.id, u.fname, u.lname, dm.message, dm.created_at, dm.sender_id
                FROM users u
                INNER JOIN (
                    SELECT 
                        IF(sender_id = :userId, recipient_id, sender_id) as partner_id,
                        message,
                        created_at,
                        sender_id,
                        ROW_NUMBER() OVER (PARTITION BY IF(sender_id = :userId, recipient_id, sender_id) ORDER BY created_at DESC) as rn
                    FROM direct_messages
                    WHERE sender_id = :userId OR recipient_id = :userId
                ) dm ON u.id = dm.partner_id AND dm.rn = 1
                WHERE u.id != :userId
                ORDER BY dm.created_at DESC
            ';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':userId', $userId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RepositoryException("Error fetching chat partners.", $e);
        }
    }
}