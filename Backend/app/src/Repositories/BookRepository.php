<?php
namespace App\Repositories;
use App\Framework\Repository;
use App\Repositories\Interfaces\IBookRepository;
use App\Models\Book;
use App\Models\BookCondition;
use App\Models\BookSwapRequest;
use App\config\DatabaseConfig;
use App\Models\User;
use PDO;
use PDOException;
use App\Repositories\BookSwapRequestRepository;
use App\Services\BookRequestService;
use App\Services\UserService;
use App\Repositories\UserRepository;
class BookRepository extends Repository implements IBookRepository {
      private BookRequestService $bookRequestService;
      private BookSwapRequestRepository $bookSwapRequestRepository;
      private UserService $userService;
      private UserRepository $userRepository;


    public function __construct() {
        $this->bookSwapRequestRepository = new BookSwapRequestRepository();
        $this->bookRequestService = new BookRequestService($this->bookSwapRequestRepository);
        $this->userRepository = new UserRepository();
        $this->userService = new UserService($this->userRepository);
    }

    private function mapBook(array $data): Book {
        $book = new Book();
           $book->id  = $data['id'];
            $book->title = $data['title'];
            $book->author = $data['author'];
            $book->isbn = $data['isbn'] ?? '';
            $book->page_count = $data['page_count'] ?? 0;
            $book->published_year = $data['published_year'] ?? 0;
            $book->genre = $data['genre'] ?? '';
            $book->description = $data['description'] ?? '';
            $book->cover_image_url = $data['cover_image_url'] ?? '';
            $book->thumbnail_image_url = $data['thumbnail_image_url'] ?? '';
            $book->is_active = $data['is_active'] ?? true;
            $book->condition = BookCondition::fromValue($data['book_condition'] ?? 'Unknown');
            $book->shared_by = new User();
            $book->shared_by->id = $data['user_id'] ?? 0;
            $book->shared_by->fname = $data['fname'] ?? '';
            $book->shared_by->lname = $data['lname'] ?? '';
            $book->shared_by->email = $data['email'] ?? '';
            $book->shared_by->state = $data['state'] ?? '';
            $book->owner_review = $data['owner_review'] ?? null;
            //$book->condition = $data['book_condition'] ?? '';
        return $book;
    }
    public function getAllBooks(?string $genreFilter, ?string $generalFilter): array {
        $genre = '';
        $general = '';
       
        try {
            $pdo = $this->connect();
            
            if ($genreFilter !== null && trim($genreFilter) !== '') {
                $genre = ' AND B.genre LIKE :genre';
            }
            if ($generalFilter !== null && trim($generalFilter) !== '') {
                $general = ' AND (B.title LIKE :general OR B.author LIKE :general OR B.isbn LIKE :general)';
            }
            
            $query = 'SELECT U.id as user_id, U.fname, U.lname, U.email, U.state, B.*  
            FROM users U JOIN books B ON U.id = B.shared_by 
            WHERE B.is_active = 1' . $genre . $general;   
            $stmt = $pdo->prepare($query);
            
            if ($genreFilter !== null && trim($genreFilter) !== '') {
                $stmt->bindValue(':genre', '%' . trim($genreFilter) . '%');
            }
            if ($generalFilter !== null && trim($generalFilter) !== '') {
                $stmt->bindValue(':general', '%' . trim($generalFilter) . '%');
            }
            $stmt->execute();
            $booksData = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $books = [];
            foreach ($booksData as $data) {
                $books[] = $this->mapBook($data);
            }
            return $books;
        } catch (PDOException $e) {
            die("Error fetching books: " . $e->getMessage());
        }
    }

    public function getBookById(int $id): ?Book {
        try {
            $pdo = $this->connect();
            $query = 'SELECT  U.id as user_id, U.fname, U.lname, U.email, U.state, B.*  FROM users U JOIN books B ON U.id = B.shared_by WHERE B.id = :id'; 
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                return $this->mapBook($data);
            }
            return null;
        } catch (PDOException $e) {
            die("Error fetching book by ID: " . $e->getMessage());
        }
    }

    
    public function saveBook(Book $book): void {
        try {
            $pdo = $this->connect();
            $query = 'INSERT INTO books (title, author, isbn, shared_by, page_count, published_year, genre, description, cover_image_url, thumbnail_image_url, book_condition, owner_review) 
                      VALUES (:title, :author, :isbn, :shared_by, :page_count, :published_year, :genre, :description, :cover_image_url, :thumbnail_image_url, :book_condition, :owner_review)';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':title', $book->title);
            $stmt->bindParam(':author', $book->author);
            $stmt->bindParam(':isbn', $book->isbn);
            $stmt->bindParam(':shared_by', $book->shared_by->id);
            $stmt->bindParam(':page_count', $book->page_count);
            $stmt->bindParam(':published_year', $book->published_year);
            $stmt->bindParam(':genre', $book->genre);
            $stmt->bindParam(':description', $book->description);
            $stmt->bindParam(':cover_image_url', $book->cover_image_url);
            $stmt->bindParam(':thumbnail_image_url', $book->thumbnail_image_url);
            $stmt->bindParam(':owner_review', $book->owner_review);
            
            $condition = $book->condition->value;
            $stmt->bindParam(':book_condition', $condition);
            $stmt->execute();
            $bookId = (int) $pdo->lastInsertId();
            $book->id = $bookId;
            $bookRequest = (new BookSwapRequest())->mapOnBookCreation($book->shared_by, $book);
            $this->bookRequestService->createRequest($bookRequest);

        } catch (PDOException $e) {
            die("Error saving book: " . $e->getMessage());
        }
    }
    
    public function deactivateBookPost(int $bookId): void {
        try {
            $pdo = $this->connect();
            $query = 'UPDATE books SET is_active = 0 WHERE id = :id';
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $bookId);
            $stmt->execute();
        } catch (PDOException $e) {
            die("Error deactivating book: " . $e->getMessage());
        }
    }
    public function getBooksGenres(): array {
        try {
            $pdo = $this->connect();
            $query = 'SELECT DISTINCT genre FROM books WHERE is_active = 1 AND genre IS NOT NULL AND genre != ""';
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            $genresData = $stmt->fetchAll(PDO::FETCH_COLUMN);
            return $genresData;
        } catch (PDOException $e) {
            die("Error fetching book genres: " . $e->getMessage());
        }
    }
}