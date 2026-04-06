<?php
namespace App\Services;
use App\Models\BookSwapRequest;
use App\Models\Book;
use App\Repositories\Interfaces\IBookSwapRequestRepository;
use App\Repositories\BookSwapRequestRepository;
use App\Services\Interfaces\IBookRequestService;
use App\Models\User;
use App\Models\Enums\BookSwapStatus;

/**
 * Service layer for book swap request lifecycle operations.
 */
class BookRequestService implements IBookRequestService {
    /**
     * Repository handling book swap request persistence.
     */
    private IBookSwapRequestRepository $bookSwapRequestRepository;

    /**
     * Initialize default repository dependency.
     */
    public function __construct() {
        $this->bookSwapRequestRepository = new BookSwapRequestRepository();
    }

    /**
     * Create a new swap request.
     *
     * @param BookSwapRequest $request Swap request model.
     * @return int Created request id.
     */
    public function createRequest(BookSwapRequest $request): int {
        return $this->bookSwapRequestRepository->createRequest($request);
    }

    /**
     * Get a request by id.
     *
     * @param int $id Request id.
     * @return BookSwapRequest|null Request when found, otherwise null.
     */
    public function getRequestById(int $id): ?BookSwapRequest {
        return $this->bookSwapRequestRepository->getRequestById($id);
    }

    /**
     * Update status of an existing request.
     *
     * @param int $id Request id.
     * @param string $status New status value.
     * @return void
     */
    public function updateRequestStatus(int $id, string $status): void {
        $this->bookSwapRequestRepository->updateRequestStatus($id, $status);
    }

    /**
     * Update shipping cost details for a request.
     *
     * @param BookSwapRequest $request Request containing updated shipping values.
     * @return void
     */
    public function updateRequestShippingCost(BookSwapRequest $request): void {
        $this->bookSwapRequestRepository->updateRequestShippingCost($request);
    }

    /**
     * Retrieve requests visible to a user based on role and status filters.
     *
     * @param User $user Authenticated user.
     * @param bool $includeClosed Whether closed requests are included.
     * @param bool $isOwner Whether to fetch owner-side view.
     * @param BookSwapStatus|null $statusFilter Optional status filter.
     * @return array<BookSwapRequest>
     */
    public function getRequestsByUserId(User $user, bool $includeClosed, bool $isOwner, ?BookSwapStatus $statusFilter): array {
        // Assuming we want to fetch requests where the user is either requester or owner
        return $this->bookSwapRequestRepository->getRequestsByUserId($user, $includeClosed, $isOwner, $statusFilter);
    }

    /**
     * Retrieve a single request visible to a user.
     *
     * @param User $user Authenticated user.
     * @param int $requestId Request id.
     * @param bool $includeClosed Whether closed requests are included.
     * @param bool $isOwner Whether to fetch owner-side view.
     * @return BookSwapRequest|null Matching request when visible, otherwise null.
     */
    public function getRequestByUserIdAndRequestId(User $user, int $requestId, bool $includeClosed, bool $isOwner): ?BookSwapRequest {
        return $this->bookSwapRequestRepository->getRequestByUserIdAndRequestId($user, $requestId, $includeClosed, $isOwner);
    }

    /**
     * Find request currently associated with a given book.
     *
     * @param Book $book Book model.
     * @return BookSwapRequest|null Request when found, otherwise null.
     */
    public function getRequestByBookId(Book $book): ?BookSwapRequest {
        return $this->bookSwapRequestRepository->getRequestByBookId($book);
    }

    /**
     * Persist updates to a request entity.
     *
     * @param BookSwapRequest $request Request to update.
     * @return void
     */
    public function updateRequest(BookSwapRequest $request): void {
        $this->bookSwapRequestRepository->updateRequest($request);
    }

    /**
     * Resolve request id for a book-owner pair.
     *
     * @param Book $book Book model.
     * @param User $user Owner model.
     * @return int|null Request id when found.
     */
    public function getRequestIdByBookIdAndOwnerId(Book $book, User $user): ?int {
        return $this->bookSwapRequestRepository->getRequestIdByBookIdAndOwnerId($book, $user);
    }
}