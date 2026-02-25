<?php
namespace App\Services\Interfaces;
use App\Repositories\Interfaces\IBookRepository;
use App\Models\Book;
interface IBookService {
    public function getAllBooks(?string $genreFilter, ?string $generalFilter): array;
    public function getBookById(int $id): ?Book;
    public function saveBook(Book $book): void;
    public function deactivateBookPost(int $bookId): void;
    public function getBookByISBNFromGoogleApi(string $isbn): ?Book;
    public function getBooksGenres(): array;
}