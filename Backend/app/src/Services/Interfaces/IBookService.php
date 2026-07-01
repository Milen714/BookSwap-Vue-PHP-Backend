<?php
namespace App\Services\Interfaces;
use App\Repositories\Interfaces\IBookRepository;
use App\Models\Book;

/**
 * IBookService
 * 
 * Book service interface defining contract for book catalog operations
 * including retrieval, storage, searching, and external API integration.
 */
interface IBookService {
    /**
     * Retrieve all books with optional filtering by genre and search term
     * 
     * @param string|null $genreFilter Genre to filter by
     * @param string|null $generalFilter General search term
     * @return array List of Book objects matching criteria
     */
    public function getAllBooks(?string $genreFilter, ?string $generalFilter, ?int $page = null): array;
    
    /**
     * Retrieve a single book by ID
     * 
     * @param int $id Book ID
     * @return Book|null Book object if found, null otherwise
     */
    public function getBookById(int $id): ?Book;
    
    /**
     * Save new book to the catalog
     * 
     * @param Book $book Book object to save
     * @return void
     */
    public function saveBook(Book $book): void;
    
    /**
     * Deactivate and remove book from active listings
     * 
     * @param int $bookId Book ID to deactivate
     * @return void
     */
    public function deactivateBookPost(int $bookId): void;
    
    /**
     * Retrieve book data from Google Books API by ISBN
     * 
     * @param string $isbn ISBN number to search for
     * @return Book|null Book object from API if found, null otherwise
     */
    public function getBookByISBNFromGoogleApi(string $isbn): ?Book;
    
    /**
     * Retrieve all genres available in the catalog
     * 
     * @return array List of genre strings
     */
    public function getBooksGenres(): array;
    
    /**
     * Retrieve all books posted by a specific user
     * 
     * @param int $userId User ID
     * @return array List of Book objects owned by user
     */
    public function getBooksByUserId(int $userId): array;

    public function updateBook(Book $book): Book;
}
