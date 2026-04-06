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


class BookService implements IBookService {
    private IBookRepository $bookRepository;
    private Client $httpClient;
    private string $apiBaseUrl = 'https://www.googleapis.com/books/v1/volumes?q=isbn:';
    public const ITEMS_PER_PAGE = 10 + 1; // +1 to check if there's a next page

    public function __construct(IBookRepository $bookRepository) {
        $this->bookRepository = $bookRepository;
        $this->httpClient = new Client();
    }

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

    public function getBookById(int $id): ?Book {
        return $this->bookRepository->getBookById($id);
    }

    public function saveBook(Book $book): void {
        $this->bookRepository->saveBook($book);
    }
    public function deactivateBookPost(int $bookId): void {
        $this->bookRepository->deactivateBookPost($bookId);
    }
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

    public function getBooksGenres(): array {
        return $this->bookRepository->getBooksGenres();
    }
    public function getBooksByUserId(int $userId): array {
        return $this->bookRepository->getBooksByUserId($userId);
    }
}