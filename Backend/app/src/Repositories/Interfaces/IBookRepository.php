<?php
namespace App\Repositories\Interfaces;
use App\Models\Book;
interface IBookRepository {
    public function getAllBooks(?string $genreFilter, ?string $generalFilter): array;
    public function getBookById(int $id): ?Book;
    public function saveBook(Book $book): void;
    public function deactivateBookPost(int $bookId): void;
    public function getBooksGenres(): array;
}