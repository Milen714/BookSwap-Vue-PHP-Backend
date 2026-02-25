<?php
namespace App\Services\Interfaces;
use App\Models\BookSwapRequest;
use App\Models\User;
use App\Models\Book;
use App\Models\BookSwapStatus;
interface IBookRequestService {
    public function createRequest(BookSwapRequest $request): int;
    public function getRequestById(int $id): ?BookSwapRequest;
    public function updateRequestStatus(int $id, string $status): void;
    public function updateRequestShippingCost(BookSwapRequest $request): void;
        public function getRequestsByUserId(User $user, bool $includeClosed, bool $isOwner, ?BookSwapStatus $statusFilter): array;
    public function getRequestByUserIdAndRequestId(User $user, int $requestId, bool $includeClosed, bool $isOwner): ?BookSwapRequest;
    public function getRequestByBookId(Book $book): ?BookSwapRequest;
    public function updateRequest(BookSwapRequest $request): void;
    public function getRequestIdByBookIdAndOwnerId(Book $book, User $user): ?int;
}