<?php 
namespace App\Services;
use App\Services\Interfaces\IBookService;
use App\Repositories\Interfaces\IBookRepository;
use App\Models\Book;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
class BookService implements IBookService {
    private IBookRepository $bookRepository;
    private Client $httpClient;
    private string $apiBaseUrl = 'https://www.googleapis.com/books/v1/volumes?q=isbn:';

    public function __construct(IBookRepository $bookRepository) {
        $this->bookRepository = $bookRepository;
        $this->httpClient = new Client();
    }

    public function getAllBooks(?string $genreFilter, ?string $generalFilter): array {
        return $this->bookRepository->getAllBooks($genreFilter, $generalFilter);
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
            
            require_once '../config/secrets.php';
            $response = $this->httpClient->request('GET', $this->apiBaseUrl . $isbn . '&key=' . $BOOKS_API_KEY, [
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
            throw new \Exception("Error fetching book data: No book found for ISBN " . $isbn);
        } catch (RequestException $e) {
            // Log error or handle it as needed
            throw new \Exception("Error fetching book data: FromCatch " . $e->getMessage());
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
}