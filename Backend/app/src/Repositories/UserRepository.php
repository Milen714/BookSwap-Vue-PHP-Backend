<?php
namespace App\Repositories;

use App\Framework\Repository;
use App\Repositories\Interfaces\IUserRepository;
use App\Models\User;
use App\Models\Enums\UserRole;
use PDO;
use PDOException;
use App\Exceptions\RepositoryException;

class UserRepository extends Repository implements IUserRepository {

    public function getAllUsers(): array {
        try {
            $pdo = $this->connect();
            $query = '
                SELECT
                    u.id,
                    u.fname,
                    u.lname,
                    u.role,
                    u.email,
                    u.address,
                    u.post_code,
                    u.country,
                    u.state,
                    u.joined_at,
                    u.isActive,
                    u.isVerified,
                    u.swap_tokens,
                    (
                        SELECT COUNT(*)
                        FROM books b
                        WHERE b.shared_by = u.id AND b.is_active = 1
                    ) AS listed_books_count,
                    (
                        SELECT COUNT(*)
                        FROM book_swap_requests bsr
                        WHERE bsr.owner_id = u.id OR bsr.requester_id = u.id
                    ) AS swap_count,
                    (
                        SELECT MAX(bsr.created_at)
                        FROM book_swap_requests bsr
                        WHERE bsr.owner_id = u.id OR bsr.requester_id = u.id
                    ) AS last_swap_at
                FROM users u
                ORDER BY u.joined_at DESC
            ';
            $stmt = $pdo->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new RepositoryException('Error fetching all users.', $e);
        }
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
        $user->phone_number = $data['phone_number'] ?? null;
        $user->bio = $data['bio'] ?? null;
        $user->resset_token = $data['resset_token'];
        if (isset($data['resset_token_expiry'])) {
            $user->resset_token_expiry = new \DateTime($data['resset_token_expiry']);
        } else {
            $user->resset_token_expiry = null;
        }
        $user->joined_at = new \DateTime($data['joined_at']);
        $user->isActive = (bool) $data['isActive'];
        $user->isVerified = (bool) $data['isVerified'];
        return $user;
    }

    public function getUserByEmail(string $email): ?User {
        try {
            $pdo = $this->connect();
            $query = 'SELECT * FROM users WHERE email = :email';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user ? $this->mapUser($user) : null;
        } catch (PDOException $e) {
            throw new RepositoryException('Error fetching user by email.', $e);
        }
    }

    public function getUserById(int $id): ?User {
        try {
            $pdo = $this->connect();
            $query = 'SELECT * FROM users WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            return $user ? $this->mapUser($user) : null;
        } catch (PDOException $e) {
            throw new RepositoryException('Error fetching user by ID.', $e);
        }
    }

    public function createUser(User $user): bool {
        try {
            $pdo = $this->connect();
            $query = 'INSERT INTO users (fname, lname, role, email, password_hash, address, post_code,  state, country, phone_number, bio, isActive, isVerified) 
                      VALUES (:fname, :lname, :role, :email, :password_hash, :address, :post_code, :state, :country, :phone_number, :bio, :isActive, :isVerified)';
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
            $stmt->bindParam(':phone_number', $user->phone_number);
            $stmt->bindParam(':bio', $user->bio);
            $stmt->bindParam(':isActive', $user->isActive, PDO::PARAM_BOOL);
            $stmt->bindParam(':isVerified', $user->isVerified, PDO::PARAM_BOOL);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new RepositoryException('Error creating user.', $e);
        }
    }

    public function updateUser(User $user): bool {
        try {
            $pdo = $this->connect();
            $query = 'UPDATE users SET fname = :fname, lname = :lname, role = :role, email = :email, 
                    password_hash = :password_hash, address = :address, post_code = :post_code, swap_tokens = :swap_tokens, 
                    country = :country, state = :state, phone_number = :phone_number, bio = :bio, isActive = :isActive, isVerified = :isVerified, 
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
            $stmt->bindParam(':state', $user->state);
            $stmt->bindParam(':phone_number', $user->phone_number);
            $stmt->bindParam(':bio', $user->bio);
            $stmt->bindParam(':isActive', $user->isActive, PDO::PARAM_BOOL);
            $stmt->bindParam(':isVerified', $user->isVerified, PDO::PARAM_BOOL);
            $stmt->bindParam(':id', $user->id, PDO::PARAM_INT);
            $stmt->bindParam(':resset_token', $user->resset_token);
            $ressetTokenExpiry = $user->resset_token_expiry ? $user->resset_token_expiry->format('Y-m-d H:i:s') : null;
            $stmt->bindParam(':resset_token_expiry', $ressetTokenExpiry);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw new RepositoryException('Error updating user.', $e);
        }
    }

    public function setUserActive(int $userId, bool $isActive): bool {
        try {
            $pdo = $this->connect();
            $query = 'UPDATE users SET isActive = :isActive WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':isActive', $isActive, PDO::PARAM_BOOL);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RepositoryException('Error updating user status.', $e);
        }
    }

    public function deductSwapToken(int $userId): bool {
        try {
            $pdo = $this->connect();
            $query = 'UPDATE users SET swap_tokens = swap_tokens - 1 WHERE id = :id AND swap_tokens > 0';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            throw new RepositoryException('Error deducting swap token.', $e);
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
            throw new RepositoryException('Error adding swap tokens.', $e);
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
            return (int) $result['num_listings'];
        } catch (PDOException $e) {
            throw new RepositoryException('Error counting listed books.', $e);
        }
    }

    public function getAdminAnalytics(): array {
        try {
            $pdo = $this->connect();

            $summaryQueries = [
                'totalUsers' => 'SELECT COUNT(*) FROM users',
                'activeUsers' => 'SELECT COUNT(*) FROM users WHERE isActive = 1',
                'bannedUsers' => 'SELECT COUNT(*) FROM users WHERE isActive = 0',
                'verifiedUsers' => 'SELECT COUNT(*) FROM users WHERE isVerified = 1',
                'adminUsers' => "SELECT COUNT(*) FROM users WHERE role = 'ADMIN'",
                'totalListings' => 'SELECT COUNT(*) FROM books',
                'activeListings' => 'SELECT COUNT(*) FROM books WHERE is_active = 1',
                'totalSwaps' => 'SELECT COUNT(*) FROM book_swap_requests',
                'completedSwaps' => "SELECT COUNT(*) FROM book_swap_requests WHERE status = 'COMPLETED'",
                'pendingSwaps' => "SELECT COUNT(*) FROM book_swap_requests WHERE status = 'PENDING'",
                'shippingPaidSwaps' => "SELECT COUNT(*) FROM book_swap_requests WHERE status = 'SHIPPINGPAID'",
                'shippedSwaps' => "SELECT COUNT(*) FROM book_swap_requests WHERE status = 'SHIPPED'",
                'deliveredSwaps' => "SELECT COUNT(*) FROM book_swap_requests WHERE status = 'DELIVERED'",
                'takenDownSwaps' => "SELECT COUNT(*) FROM book_swap_requests WHERE status = 'TAKENDOWN'",
            ];

            $summary = [];
            foreach ($summaryQueries as $key => $query) {
                $summary[$key] = (int) $pdo->query($query)->fetchColumn();
            }

            $statusBreakdown = $pdo->query('SELECT status, COUNT(*) AS total FROM book_swap_requests GROUP BY status ORDER BY total DESC')->fetchAll(PDO::FETCH_ASSOC);
            $monthlyTrend = $pdo->query("SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, COUNT(*) AS total FROM book_swap_requests WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 5 MONTH) GROUP BY DATE_FORMAT(created_at, '%Y-%m') ORDER BY month ASC")->fetchAll(PDO::FETCH_ASSOC);
            $genreBreakdown = $pdo->query('SELECT b.genre AS genre, COUNT(*) AS total FROM book_swap_requests r INNER JOIN books b ON b.id = r.book_id WHERE b.genre IS NOT NULL AND b.genre != "" GROUP BY b.genre ORDER BY total DESC LIMIT 6')->fetchAll(PDO::FETCH_ASSOC);

            return [
                'summary' => $summary,
                'statusBreakdown' => $statusBreakdown,
                'monthlyTrend' => $monthlyTrend,
                'genreBreakdown' => $genreBreakdown,
            ];
        } catch (PDOException $e) {
            throw new RepositoryException('Error fetching admin analytics.', $e);
        }
    }
}



