<?php 
namespace App\Services;
use App\Services\Interfaces\IBookService;
use App\Repositories\Interfaces\IBookRepository;
use App\Models\Book;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use App\config\Secrets;
use App\Exceptions\NotFoundException;
use App\Exceptions\ExternalServiceException;


/**
 * Service layer for book catalog operations and Google Books enrichment.
 */
class BookService implements IBookService {
    /**
     * Repository abstraction for persistence operations.
     */
    private IBookRepository $bookRepository;

    /**
     * HTTP client used for external Google Books API calls.
     */
    private Client $httpClient;

    /**
     * Base URL for ISBN volume lookup.
     */
    private string $apiBaseUrl = 'https://www.googleapis.com/books/v1/volumes?q=isbn:';

    /**
     * Number of items returned in paginated calls (+1 sentinel to detect next page).
     */
    public const ITEMS_PER_PAGE = 10 + 1; // +1 to check if there's a next page

    /**
     * @param IBookRepository $bookRepository Book repository implementation.
     */
    public function __construct(IBookRepository $bookRepository) {
        $this->bookRepository = $bookRepository;
        $this->httpClient = new Client();
    }

    /**
     * Get books optionally filtered by genre/general query and optionally paginated.
     *
     * @param string|null $genreFilter Optional genre filter.
     * @param string|null $generalFilter Optional text search filter.
     * @param int|null $page Optional page number (1-based). Null returns full result set.
     * @return array<Book> Matching books.
     */
    public function getAllBooks(?string $genreFilter, ?string $generalFilter, ?int $page = null): array {
        // If page is null return all books
        if($page === null){
            return $this->bookRepository->getAllBooks($genreFilter, $generalFilter);
        }
        // Page number valideation
        if($page < 1){
            $page = 1;
        }

        $offset = $page !== null ? ($page - 1) * (self::ITEMS_PER_PAGE - 1) : null;

        return $this->bookRepository->getAllBooks($genreFilter, $generalFilter, self::ITEMS_PER_PAGE, $offset);
    }

    /**
     * Find a single book by id.
     *
     * @param int $id Book id.
     * @return Book|null Book when found, otherwise null.
     */
    public function getBookById(int $id): ?Book {
        return $this->bookRepository->getBookById($id);
    }

    /**
     * Persist a new book listing.
     *
     * @param Book $book Book model to persist.
     * @return void
     */
    public function saveBook(Book $book): void {
        $this->bookRepository->saveBook($book);
    }

    /**
     * Deactivate an existing book listing.
     *
     * @param int $bookId Book id.
     * @return void
     */
    public function deactivateBookPost(int $bookId): void {
        $this->bookRepository->deactivateBookPost($bookId);
    }

    /**
     * Resolve book metadata from Google Books by ISBN.
     *
     * @param string $isbn ISBN-10 or ISBN-13 value.
     * @return Book|null Parsed book model when data exists.
     * @throws NotFoundException When no matching item is returned.
     * @throws ExternalServiceException When Google Books call fails.
     */
    public function getBookByISBNFromGoogleApi(string $isbn): Book|null {
        try {
            $headers = ["Content-Type" => "application/json; charset=UTF-8"];
            
            $response = $this->httpClient->request('GET', $this->apiBaseUrl . $isbn . '&key=' . Secrets::$booksApiKey, [
                "headers" => $headers,
                ]);
            $data = json_decode($response->getBody()->getContents(), true);
            
            if (isset($data['items'][0])) {
                foreach ($data['items'] as $item) {
                    $bookData = $this->parseBookJson($item);
                    if ($bookData['isbn13'] === $isbn || $bookData['isbn10'] === $isbn) {
                        return new Book()->mapBookFromApi($item);
                    }
                }
                return new Book()->mapBookFromApi($data['items'][0]);
            }
            throw new NotFoundException("No book found for ISBN " . $isbn);
        } catch (RequestException $e) {
            throw new ExternalServiceException("Error fetching book data from Google Books API.", $e);
        }

    }

    /**
     * Normalize Google Books item payload into minimal searchable fields.
     *
     * @param array<string, mixed> $item Raw Google Books item.
     * @return array{title: string, authors: array, thumbnail: mixed, isbn10: mixed, isbn13: mixed}
     */
    private function parseBookJson($item) {
    $info = $item['volumeInfo'] ?? [];

    $thumbnail = $info['imageLinks']['thumbnail'] ?? null;

    $isbn10 = null;
    $isbn13 = null;

    foreach ($info['industryIdentifiers'] ?? [] as $identifier) {
        if ($identifier['type'] === 'ISBN_10') $isbn10 = $identifier['identifier'];
        if ($identifier['type'] === 'ISBN_13') $isbn13 = $identifier['identifier'];
    }

    return [
        'title'     => $info['title'] ?? 'Unknown Title',
        'authors'   => $info['authors'] ?? [],
        'thumbnail' => $thumbnail,
        'isbn10'    => $isbn10,
        'isbn13'    => $isbn13,
    ];
    }

    /**
     * Get all available book genres.
     *
     * @return array<string> Genre names.
     */
    public function getBooksGenres(): array {
        return $this->bookRepository->getBooksGenres();
    }

    /**
     * Get books listed by a specific user.
     *
     * @param int $userId User id.
     * @return array<Book> User's listed books.
     */
    public function getBooksByUserId(int $userId): array {
        return $this->bookRepository->getBooksByUserId($userId);
    }
}