<?php
namespace App\Repositories\Interfaces;
use App\Models\BookSwapRequest;
use App\Models\BookSwapStatus;
use App\Models\User;
use App\Models\Book;

interface IBookSwapRequestRepository {
    public function createRequest(BookSwapRequest $request): int;
    public function getRequestById(int $id): ?BookSwapRequest;
    public function updateRequestStatus(int $id, string $status): void;
    public function updateRequest(BookSwapRequest $request): void;
    public function updateRequestShippingCost(BookSwapRequest $request): void;
    public function getRequestsByUserId(User $user, bool $includeClosed, bool $isOwner, ?BookSwapStatus $statusFilter): array;
    public function getRequestByUserIdAndRequestId(User $user, int $requestId, bool $includeClosed, bool $isOwner): ?BookSwapRequest;
    public function getRequestByBookId(Book $book): ?BookSwapRequest;
    public function getRequestIdByBookIdAndOwnerId(Book $book, User $user): ?int;
}