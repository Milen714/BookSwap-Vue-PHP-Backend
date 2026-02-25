<?php
namespace App\Repositories;
use App\Models\BookSwapRequest;
use App\Models\BookSwapStatus;
use App\Framework\Repository;
use App\Repositories\Interfaces\IBookSwapRequestRepository;
use App\Models\Book;
use PDO;
use PDOException;
use App\Models\User;
class BookSwapRequestRepository extends Repository implements IBookSwapRequestRepository {

    private string $SELECT_STATEMENT = 'SELECT
                -- Swap request
                bsr.id                AS swap_id,
                bsr.status            AS swap_status,
                bsr.created_at        AS swap_created_at,
                bsr.closed_at         AS swap_closed_at,
                bsr.requester_action_token,
                bsr.owner_action_token,
                bsr.shipping_street,
                bsr.shipping_post_code,
                bsr.shipping_state,
                bsr.shipping_country,
                bsr.shipping_cost,
                -- Book
                b.id                  AS book_id,
                b.shared_by           AS book_shared_by,
                b.title,
                b.author,
                b.isbn,
                b.published_year,
                b.genre,
                b.description,
                b.cover_image_url,
                b.thumbnail_image_url,
                b.book_condition,
                b.page_count,
                b.created_at          AS book_created_at,
                b.updated_at          AS book_updated_at,
                b.owner_review,
                -- Owner user
                owner.id              AS owner_id,
                owner.fname           AS owner_fname,
                owner.lname           AS owner_lname,
                owner.address         AS owner_address,
                owner.post_code       AS owner_post_code,
                owner.country         AS owner_country,
                owner.state           AS owner_state,
                -- Requester user
                requester.id          AS requester_id,
                requester.fname       AS requester_fname,
                requester.lname       AS requester_lname,
                requester.address     AS requester_address,
                requester.post_code   AS requester_post_code,
                requester.country     AS requester_country,
                requester.state       AS requester_state';
    public function createRequest(BookSwapRequest $request): int {
    
    try {
        $pdo = $this->connect();
        $query = 'INSERT INTO book_swap_requests (requester_id, owner_id, book_id, status, created_at, requester_action_token, owner_action_token, shipping_street, shipping_post_code, shipping_state, shipping_country, shipping_cost) 
                  VALUES (:requester_id, :owner_id, :book_id, :status, :created_at, :requester_action_token, :owner_action_token, :street, :post_code, :state, :country, :shipping_cost)';
        $stmt = $pdo->prepare($query);
        
        $requesterId = $request->requester?->id ? : null;
        $ownerId = $request->owner?->id;
        $bookId = $request->book?->id;
        $status = $request->status->value;
        $createdAt = $request->created_at->format('Y-m-d H:i:s');
        
        $stmt->bindParam(':requester_id', $requesterId);
        $stmt->bindParam(':owner_id', $ownerId);
        $stmt->bindParam(':book_id', $bookId);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':created_at', $createdAt);
        $stmt->bindParam(':requester_action_token', $request->requester_action_token);
        $stmt->bindParam(':owner_action_token', $request->owner_action_token);
        $stmt->bindParam(':street', $request->street);
        $stmt->bindParam(':post_code', $request->post_code);
        $stmt->bindParam(':state', $request->state);
        $stmt->bindParam(':country', $request->country);
        $stmt->bindValue(':shipping_cost', $request->shipping_cost, $request->shipping_cost === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
        $stmt->execute();

        return (int) $pdo->lastInsertId();
    } catch (PDOException $e) {
        throw new \Exception("Error creating book swap request: " . $e->getMessage());
    }
}
    public function getRequestById(int $id): ?BookSwapRequest {
        try {
            $pdo = $this->connect();
            $query = $this->SELECT_STATEMENT . ' FROM book_swap_requests bsr

            JOIN books b
                ON bsr.book_id = b.id

            JOIN users owner
                ON bsr.owner_id = owner.id

            LEFT JOIN users requester
                ON bsr.requester_id = requester.id

            WHERE bsr.id = :id';
            
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$data) {
                return null;
            }
            
            return $this->mapBookSwapRequest($data);
        } catch (PDOException $e) {
            throw new \Exception("Error fetching book swap request: " . $e->getMessage());
        }
    }

    private function mapBookSwapRequest(array $data): BookSwapRequest {
        $request = new BookSwapRequest();
        $request->id = isset($data['swap_id']) && $data['swap_id'] !== null ? (int)$data['swap_id'] : null;;
        $request->status = BookSwapStatus::from($data['swap_status']);
        $request->created_at = new \DateTime($data['swap_created_at']);
        $request->closed_at = $data['swap_closed_at'] ? new \DateTime($data['swap_closed_at']) : null;
        $request->requester_action_token = $data['requester_action_token'];
        $request->owner_action_token = $data['owner_action_token'];
        $request->street = $data['shipping_street'];
        $request->post_code = $data['shipping_post_code'];
        $request->state = $data['shipping_state'];
        $request->country = $data['shipping_country'];
        $request->shipping_cost = isset($data['shipping_cost']) && $data['shipping_cost'] !== null ? (float)$data['shipping_cost'] : null;

        // Map Owner (who will be the shared_by user)
            $owner = new User();
            $owner->id = isset($data['owner_id']) && $data['owner_id'] !== null ? (int)$data['owner_id'] : null;
            $owner->fname = $data['owner_fname'] ?? '';
            $owner->lname = $data['owner_lname'] ?? '';
            $owner->address = $data['owner_address'];
            $owner->post_code = $data['owner_post_code'];
            $owner->country = $data['owner_country'];
            $owner->state = $data['owner_state'];

        // Map Book
        $request->book = new \App\Models\Book();
        $request->book->id = $data['book_id'];
        $request->book->title = $data['title'];
        $request->book->author = $data['author'];
        $request->book->isbn = $data['isbn'];
        $request->book->published_year = $data['published_year'];
        $request->book->genre = $data['genre'];
        $request->book->description = $data['description'];
        $request->book->cover_image_url = $data['cover_image_url'];
        $request->book->thumbnail_image_url = $data['thumbnail_image_url'];
        $request->book->condition = \App\Models\BookCondition::fromValue($data['book_condition']);
        $request->book->page_count = $data['page_count'] === null ? 0 : $data['page_count'];
        $request->book->shared_by = $owner;
        $request->book->owner_review = $data['owner_review'] === null ? null : $data['owner_review']; 

        // Map Owner
        $request->owner = $owner;

        // Map Requester
            $request->requester = new User();
            $request->requester->id = isset($data['requester_id']) && $data['requester_id'] !== null ? (int)$data['requester_id'] : null;
            $request->requester->fname = $data['requester_fname'] ?? '';
            $request->requester->lname = $data['requester_lname'] ?? '';
            $request->requester->address = $data['requester_address'] ? : null;
            $request->requester->post_code = $data['requester_post_code'] ? : null;
            $request->requester->country = $data['requester_country'] ? : null;
            $request->requester->state = $data['requester_state'] ? : null;

        return $request;
    }

     
    
    public function updateRequestStatus(int $id, string $status): void{
        try {
            $pdo = $this->connect();
            $query = 'UPDATE book_swap_requests SET status = :status WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error updating book swap request status: " . $e->getMessage());
        }
    }
    public function updateRequestShippingCost(BookSwapRequest $request): void {
        try {
            $pdo = $this->connect();
            $query = 'UPDATE book_swap_requests SET shipping_cost = :shipping_cost WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':shipping_cost', $request->shipping_cost);
            $stmt->bindParam(':id', $request->id);
            $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error updating book swap request shipping cost: " . $e->getMessage());
        }
    }
    public function getRequestsByUserId(User $user, bool $includeClosed, bool $isOwner, ?BookSwapStatus $statusFilter): array{
        switch($statusFilter) {
            case BookSwapStatus::ALL:
                $statusFilterQuery = ' AND bsr.status NOT IN ("TAKENDOWN", "COMPLETED") ';
                break;
            case BookSwapStatus::COMPLETED:
                $statusFilterQuery = ' AND bsr.status = "COMPLETED" ';
                break;
            case BookSwapStatus::TAKENDOWN:
                $statusFilterQuery = ' AND bsr.status = "TAKENDOWN" ';
                break;
            case null:
                $statusFilterQuery = '';
                break;
            default:
                // No status filter applied
                break;
        }
        try {
            $pdo = $this->connect();
            $query = $this->SELECT_STATEMENT . '
            FROM book_swap_requests bsr

            LEFT JOIN books b
                ON bsr.book_id = b.id

            LEFT JOIN users owner
                ON bsr.owner_id = owner.id

            LEFT JOIN users requester
                ON bsr.requester_id = requester.id

            WHERE ' . ($isOwner ? 'bsr.owner_id' : 'bsr.requester_id') . ' = :userId' . 
            ($includeClosed ? '' : ' AND bsr.closed_at IS NULL'). '
            ' . ($statusFilter ? $statusFilterQuery : '') . '
            ORDER BY bsr.created_at DESC';
            
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':userId', $user->id, PDO::PARAM_INT);
            $stmt->execute();
            
            $results = [];
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $results[] = $this->mapBookSwapRequest($data);
            }
            
            return $results;
        } catch (PDOException $e) {
            throw new \Exception("Error fetching book swap requests: " . $e->getMessage());
        }
    }
    public function getRequestByUserIdAndRequestId(User $user, int $requestId, bool $includeClosed, bool $isOwner): ?BookSwapRequest{
        try {
            $pdo = $this->connect();
            $query = $this->SELECT_STATEMENT . '
            FROM book_swap_requests bsr

            JOIN books b
                ON bsr.book_id = b.id

            JOIN users owner
                ON bsr.owner_id = owner.id

            JOIN users requester
                ON bsr.requester_id = requester.id

            WHERE ' . ($isOwner ? 'bsr.owner_id' : 'bsr.requester_id') . ' = :userId AND bsr.id = :requestId' .
            ($includeClosed ? '' : ' AND bsr.closed_at IS NULL');
            
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':userId', $user->id, PDO::PARAM_INT);
            $stmt->bindParam(':requestId', $requestId, PDO::PARAM_INT);
            $stmt->execute();
            
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$data) {
                throw new \Exception("No book swap request found for the given user and request ID.");
            }
            return $this->mapBookSwapRequest($data);
        } catch (PDOException $e) {
            throw new \Exception("Error fetching book swap requests: " . $e->getMessage());
        }
    }

    public function getRequestByBookId(Book $book): ?BookSwapRequest {
        try {
            $pdo = $this->connect();
            $query = $this->SELECT_STATEMENT . '
            FROM book_swap_requests bsr

            JOIN books b
                ON bsr.book_id = b.id

            JOIN users owner
                ON bsr.owner_id = owner.id

            JOIN users requester
                ON bsr.requester_id = requester.id
            WHERE book_id = :book_id';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':book_id', $book->id, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$data) {
                return null;
            }
            return $this->mapBookSwapRequest($data);
        } catch (PDOException $e) {
            throw new \Exception("Error fetching book swap request by book ID: " . $e->getMessage());
        }
    }
    public function updateRequest(BookSwapRequest $request): void{
        try {
            $pdo = $this->connect();
            $query = 'UPDATE book_swap_requests 
                      SET status = :status,
                          closed_at = :closed_at,
                          requester_action_token = :requester_action_token,
                          owner_action_token = :owner_action_token,
                          shipping_street = :shipping_street,
                          shipping_post_code = :shipping_post_code,
                          shipping_state = :shipping_state,
                          shipping_country = :shipping_country,
                          shipping_cost = :shipping_cost,
                          requester_id = :requester_id
                      WHERE id = :id';
            $stmt = $pdo->prepare($query);
            
            $closedAt = $request->closed_at ? $request->closed_at->format('Y-m-d H:i:s') : null;
            $status = $request->status->value;

            $stmt->bindParam(':status', $status);
            $stmt->bindValue(':closed_at', $closedAt, $closedAt === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':requester_action_token', $request->requester_action_token);
            $stmt->bindParam(':owner_action_token', $request->owner_action_token);
            $stmt->bindParam(':shipping_street', $request->street);
            $stmt->bindParam(':shipping_post_code', $request->post_code);
            $stmt->bindParam(':shipping_state', $request->state);
            $stmt->bindParam(':shipping_country', $request->country);
            $stmt->bindValue(':shipping_cost', $request->shipping_cost, $request->shipping_cost === null ? PDO::PARAM_NULL : PDO::PARAM_STR);
            $stmt->bindParam(':id', $request->id);
            $stmt->bindParam(':requester_id', $request->requester->id); 
            $stmt->execute();
        } catch (PDOException $e) {
            throw new \Exception("Error updating book swap request: " . $e->getMessage());
        }
    }
    public function getRequestIdByBookIdAndOwnerId(Book $book, User $user): ?int {
        try {
            $pdo = $this->connect();
            $query = 'SELECT bsr.id AS swap_id
            FROM book_swap_requests bsr

            JOIN books b
                ON bsr.book_id = b.id

            JOIN users owner
                ON bsr.owner_id = owner.id

            
            WHERE bsr.book_id = :book_id AND bsr.owner_id = :owner_id';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':book_id', $book->id, PDO::PARAM_INT);
            $stmt->bindParam(':owner_id', $user->id, PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$data) {
                return null;
            }
            return (int)$data['swap_id'];
        } catch (PDOException $e) {
            throw new \Exception("Error fetching book swap request by book ID and user ID: " . $e->getMessage());
        }
    }
}