<?php
namespace App\Models;
use DateTime;
use App\Models\Enums\BookCondition;
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
    /** @var float[]|null */
    public ?array $embedding = null;
    public ?float $distance = null;

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
        $book->embedding = null;
        return $book;
    }

    /**
     * @return float[]|null
     */
    public static function parseEmbedding(mixed $embedding): ?array {
        if ($embedding === null || $embedding === '') {
            return null;
        }

        if (is_array($embedding)) {
            return array_map('floatval', $embedding);
        }

        $embedding = trim((string) $embedding, '[]');
        if ($embedding === '') {
            return null;
        }

        return array_map('floatval', explode(',', $embedding));
    }

    public static function formatEmbedding(?array $embedding): ?string {
        if ($embedding === null || $embedding === []) {
            return null;
        }

        return '[' . implode(',', array_map('floatval', $embedding)) . ']';
    }
    public function getEmbeddingString(): ?string {
        return trim(implode("\n", array_filter([
            'Title: ' . $this->title,
            'Author: ' . $this->author,
            'Genre: ' . $this->genre,
            'Description: ' . $this->description,
            'Published year: ' . $this->published_year,
            $this->owner_review ? 'Owner review: ' . $this->owner_review : null,
        ])));
    }

}
