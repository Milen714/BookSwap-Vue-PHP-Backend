<?php
namespace App\Services\Interfaces;
use App\Models\BookSwapRequest;
use App\Models\User;
use App\Models\Book;
use App\Models\Enums\BookSwapStatus;

/**
 * IBookRequestService
 * 
 * Book request service interface defining contract for managing book swap requests
 * including creation, status updates, and request retrieval operations.
 */
interface IBookRequestService {
    /**
     * Create new book swap request
     * 
     * @param BookSwapRequest $request Request object to create
     * @return int ID of created request
     */
    public function createRequest(BookSwapRequest $request): int;
    
    /**
     * Retrieve a specific book swap request by ID
     * 
     * @param int $id Request ID
     * @return BookSwapRequest|null Request object if found, null otherwise
     */
    public function getRequestById(int $id): ?BookSwapRequest;
    
    /**
     * Update request status (e.g., pending, accepted, completed)
     * 
     * @param int $id Request ID
     * @param string $status New status value
     * @return void
     */
    public function updateRequestStatus(int $id, string $status): void;
    
    /**
     * Calculate and update shipping cost for a request
     * 
     * @param BookSwapRequest $request Request to update shipping cost for
     * @return void
     */
    public function updateRequestShippingCost(BookSwapRequest $request): void;
    
    /**
     * Retrieve requests for a user with filtering options
     * 
     * @param User $user User to get requests for
     * @param bool $includeClosed Whether to include closed requests
     * @param bool $isOwner If true, get requests user owns; if false, get requests they initiated
     * @param BookSwapStatus|null $statusFilter Optional status to filter by
     * @return array List of BookSwapRequest objects
     */
    public function getRequestsByUserId(User $user, bool $includeClosed, bool $isOwner, ?BookSwapStatus $statusFilter): array;
    
    /**
     * Retrieve a specific request for a user by request ID
     * 
     * @param User $user User requesting the data
     * @param int $requestId Request ID
     * @param bool $includeClosed Whether to include closed requests
     * @param bool $isOwner If true, user is the book owner; if false, user initiated request
     * @return BookSwapRequest|null Request object if found and accessible, null otherwise
     */
    public function getRequestByUserIdAndRequestId(User $user, int $requestId, bool $includeClosed, bool $isOwner): ?BookSwapRequest;
    
    /**
     * Retrieve request associated with a specific book
     * 
     * @param Book $book Book to find request for
     * @return BookSwapRequest|null Request object if found, null otherwise
     */
    public function getRequestByBookId(Book $book): ?BookSwapRequest;
    
    /**
     * Update an existing request with new data
     * 
     * @param BookSwapRequest $request Request object with updated data
     * @return void
     */
    public function updateRequest(BookSwapRequest $request): void;
    
    /**
     * Get request ID for a specific book owned by a user
     * 
     * @param Book $book Book to search for
     * @param User $user Owner of the book
     * @return int|null Request ID if found, null otherwise
     */
    public function getRequestIdByBookIdAndOwnerId(Book $book, User $user): ?int;
}