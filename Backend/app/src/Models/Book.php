<?php
namespace App\Models;
use DateTime;
use App\Models\BookCondition;
class Book{
    public int $id;
    public string $title;
    public string $author;
    public string $isbn;
    public User $shared_by;
    public int $page_count;
    public int $published_year;
    public string $genre;
    public string $description;
    public ?string $cover_image_url;
    public ?string $thumbnail_image_url;
    public BookCondition $condition;
    public bool $is_active;
    public ?string $owner_review;

    public function __construct(){}

    public function mapBookFromApi(array $data): Book {
        $book = new Book();
        $book->title = $data['volumeInfo']['title'] ?? '';
        $book->author = implode(', ', $data['volumeInfo']['authors'] ?? []);
        $book->isbn = $data['volumeInfo']['industryIdentifiers'][1]['identifier'] ?? '';
        $book->page_count = $data['volumeInfo']['pageCount'] ?? 0;
        $book->published_year = isset($data['volumeInfo']['publishedDate']) ? (int)substr($data['volumeInfo']['publishedDate'], 0, 4) : 0;
        $book->genre = implode(', ', $data['volumeInfo']['categories'] ?? []);
        $book->description = $data['volumeInfo']['description'] ?? '';
        $book->cover_image_url = $data['volumeInfo']['imageLinks']['thumbnail'] ?? '';
        $book->thumbnail_image_url = $data['volumeInfo']['imageLinks']['smallThumbnail'] ?? '';
        $book->condition = BookCondition::New;
        return $book;
    }

}