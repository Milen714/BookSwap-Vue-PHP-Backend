<?php
namespace App\Repositories;
use App\Framework\Repository;
use App\Repositories\Interfaces\IUserRepository;
use App\Models\User;
use App\Models\UserRole;
use PDO;
use PDOException;


class UserRepository extends Repository implements IUserRepository {
    
    public function getAllUsers(): array {
        // Implementation to fetch all users from the database
        return [];
    }
    private function mapUser(array $data): User {
        $user = new User();
        $user->id = $data['id'];
        $user->fname = $data['fname'];
        $user->lname = $data['lname'];
        $user->role = UserRole::from($data['role'] ?? 'GUEST');
        $user->email = $data['email'];
        $user->password_hash = $data['password_hash'];
        $user->address = $data['address'];
        $user->swapTokens = $data['swap_tokens'] ?? 0;
        $user->post_code = $data['post_code'];
        $user->state = $data['state'];
        $user->country = $data['country'];
        $user->resset_token = $data['resset_token'];
        if (isset($data['resset_token_expiry'])) {
    $user->resset_token_expiry = new \DateTime($data['resset_token_expiry']);
} else {
    unset($user->resset_token_expiry);
}
        $user->joined_at = new \DateTime($data['joined_at']);
        $user->isActive = (bool)$data['isActive'];
        $user->isVerified = (bool)$data['isVerified'];
        return $user;
    }
    public function getUserByEmail(string $email): ?User {
        // Implementation to fetch a user by email from the database
        try{
            $pdo = $this->connect();
            $query = 'SELECT * FROM users WHERE email = :email';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $user ? $this->mapUser($user) : null;


        }catch(PDOException $e){
            die("Error fetching user: " . $e->getMessage());
        }
    }
    public function getUserById(int $id): ?User {
        try{
            $pdo = $this->connect();
            $query = 'SELECT * FROM users WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $user ? $this->mapUser($user) : null;
        }catch(PDOException $e){
            die("Error fetching user: " . $e->getMessage());    
        }
    }
    public function createUser(User $user): bool {
        try {
            $pdo = $this->connect();
            $query = 'INSERT INTO users (fname, lname, role, email, password_hash, address, post_code,  state, country, isActive, isVerified) 
                      VALUES (:fname, :lname, :role, :email, :password_hash, :address, :post_code, :state, :country, :isActive, :isVerified)';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':fname', $user->fname);
            $stmt->bindParam(':lname', $user->lname);
            $roleValue = $user->role->value;
            $stmt->bindParam(':role', $roleValue);
            $stmt->bindParam(':email', $user->email);
            $stmt->bindParam(':password_hash', $user->password_hash);
            $stmt->bindParam(':address', $user->address);
            $stmt->bindParam(':post_code', $user->post_code);
            $stmt->bindParam(':country', $user->country);
            $stmt->bindParam(':state', $user->state);
            $stmt->bindParam(':isActive', $user->isActive, PDO::PARAM_BOOL);
            $stmt->bindParam(':isVerified', $user->isVerified, PDO::PARAM_BOOL);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error creating user: " . $e->getMessage());
        }
    }
    public function updateUser(User $user): bool {
        // Implementation to update user details in the database
        try {
            $pdo = $this->connect();
            $query = 'UPDATE users SET fname = :fname, lname = :lname, role = :role, email = :email, 
                    password_hash = :password_hash, address = :address, post_code = :post_code, swap_tokens = :swap_tokens, 
                    country = :country, isActive = :isActive, isVerified = :isVerified, 
                    resset_token = :resset_token, resset_token_expiry = :resset_token_expiry
                    WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':fname', $user->fname);
            $stmt->bindParam(':lname', $user->lname);
            $roleValue = $user->role->value;
            $stmt->bindParam(':role', $roleValue);
            $stmt->bindParam(':email', $user->email);
            $stmt->bindParam(':password_hash', $user->password_hash);
            $stmt->bindParam(':address', $user->address);
            $stmt->bindParam(':post_code', $user->post_code); 
            $stmt->bindParam(':swap_tokens', $user->swapTokens, PDO::PARAM_INT);      
            $stmt->bindParam(':country', $user->country);
            $stmt->bindParam(':isActive', $user->isActive, PDO::PARAM_BOOL);
            $stmt->bindParam(':isVerified', $user->isVerified, PDO::PARAM_BOOL);
            $stmt->bindParam(':id', $user->id, PDO::PARAM_INT); 
            $stmt->bindParam(':resset_token', $user->resset_token);
            $ressetTokenExpiry = $user->resset_token_expiry ? $user->resset_token_expiry->format('Y-m-d H:i:s') : null;
            $stmt->bindParam(':resset_token_expiry', $ressetTokenExpiry);
            return $stmt->execute();

        } catch (PDOException $e) {
            throw new \Exception("Error updating user: " . $e->getMessage());
        }
        
    }
    public function deductSwapToken(int $userId): bool {
    try {
        $pdo = $this->connect();
        // Only update if user has at least 1 token
        $query = 'UPDATE users SET swap_tokens = swap_tokens - 1 WHERE id = :id AND swap_tokens > 0';
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        // Check if the update affected any rows
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        throw new \Exception("Error deducting swap token: " . $e->getMessage());
    }
}
    public function addSwapTokens(int $userId, int $amount): bool {
    try {
        $pdo = $this->connect();
        $query = 'UPDATE users SET swap_tokens = swap_tokens + :amount WHERE id = :id';
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':amount', $amount, PDO::PARAM_INT);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        throw new \Exception("Error adding swap tokens: " . $e->getMessage());
    }
} 
    public function numberOfListedBooks(int $userId): int {
    try {
        $pdo = $this->connect();
        $query = 'SELECT COUNT(*) as num_listings FROM books WHERE shared_by = :user_id';
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['num_listings'];
    } catch (PDOException $e) {
        throw new \Exception("Error counting listed books: " . $e->getMessage());
    }
    
}
}



   