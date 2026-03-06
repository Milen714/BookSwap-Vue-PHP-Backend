<?php
namespace App\Repositories;
use App\Framework\Repository;
use App\Repositories\Interfaces\IDirectMessageRepository;
use App\Models\User;
use App\Models\UserRole;
use App\Models\DirectMessage;
use PDO;
use PDOException;

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
            die("Error saving direct message: " . $e->getMessage());
        }
    }

    public function getDirectMessages($userId1, $userId2) {
        try {
            $pdo = $this->connect();
            $query = 'SELECT * FROM direct_messages WHERE (sender_id = :userId1 AND recipient_id = :userId2) OR (sender_id = :userId2 AND recipient_id = :userId1) ORDER BY created_at ASC';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':userId1', $userId1);
            $stmt->bindParam(':userId2', $userId2);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching direct messages: " . $e->getMessage());
        }
    }
}