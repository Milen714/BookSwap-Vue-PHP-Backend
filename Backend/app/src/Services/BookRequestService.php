<?php
namespace App\Services;
use App\Models\BookSwapRequest;
use App\Models\Book;
use App\Repositories\Interfaces\IBookSwapRequestRepository;
use App\Services\Interfaces\IBookRequestService;
use App\Models\User;
use App\Models\BookSwapStatus;
class BookRequestService implements IBookRequestService {
    private IBookSwapRequestRepository $bookSwapRequestRepository;

    public function __construct(IBookSwapRequestRepository $bookSwapRequestRepository) {
        $this->bookSwapRequestRepository = $bookSwapRequestRepository;
    }

    public function createRequest(BookSwapRequest $request): int {
        return $this->bookSwapRequestRepository->createRequest($request);
    }

    public function getRequestById(int $id): ?BookSwapRequest {
        return $this->bookSwapRequestRepository->getRequestById($id);
    }

    public function updateRequestStatus(int $id, string $status): void {
        $this->bookSwapRequestRepository->updateRequestStatus($id, $status);
    }
    public function updateRequestShippingCost(BookSwapRequest $request): void {
        $this->bookSwapRequestRepository->updateRequestShippingCost($request);
    }

    public function getRequestsByUserId(User $user, bool $includeClosed, bool $isOwner, ?BookSwapStatus $statusFilter): array {
        // Assuming we want to fetch requests where the user is either requester or owner
        return $this->bookSwapRequestRepository->getRequestsByUserId($user, $includeClosed, $isOwner, $statusFilter);
    }
    public function getRequestByUserIdAndRequestId(User $user, int $requestId, bool $includeClosed, bool $isOwner): ?BookSwapRequest {
        return $this->bookSwapRequestRepository->getRequestByUserIdAndRequestId($user, $requestId, $includeClosed, $isOwner);
    }

    public function getRequestByBookId(Book $book): ?BookSwapRequest {
        return $this->bookSwapRequestRepository->getRequestByBookId($book);
    }
    public function updateRequest(BookSwapRequest $request): void {
        $this->bookSwapRequestRepository->updateRequest($request);
    }
    public function getRequestIdByBookIdAndOwnerId(Book $book, User $user): ?int {
        return $this->bookSwapRequestRepository->getRequestIdByBookIdAndOwnerId($book, $user);
    }
}