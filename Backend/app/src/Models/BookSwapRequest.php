<?php 

namespace App\Models;

use DateTime;

class BookSwapRequest {
    public ?int $id;
    public User $requester;
    public User $owner;
    public Book $book;
    public BookSwapStatus $status;
    public ?DateTime $created_at = null;
    public ?DateTime $closed_at = null;
    public ?string $requester_action_token;
    public ?string $owner_action_token;
    public ?string $street;
    public ?string $post_code;
    public ?string $state;
    public ?string $country;
    public ?float $shipping_cost = null;

    public function __construct(){}

    

    public function map(User $requester, User $owner, Book $book, string $ownerActionToken, string $requesterActionToken, string $street, string $post_code, string $state, string $country): BookSwapRequest {
        $bookSwapRequest = new BookSwapRequest();
        $bookSwapRequest->book = $book;
        $bookSwapRequest->owner = $owner;
        $bookSwapRequest->requester = $requester;
        $bookSwapRequest->status = BookSwapStatus::PENDING;
        $bookSwapRequest->created_at = new DateTime();
        $bookSwapRequest->requester_action_token = $requesterActionToken;
        $bookSwapRequest->owner_action_token = $ownerActionToken;
        $bookSwapRequest->street = $street;
        $bookSwapRequest->post_code = $post_code;   
        $bookSwapRequest->state = $state;
        $bookSwapRequest->country = $country;
        return $bookSwapRequest;
    }
    public function mapOnBookCreation(User $owner, Book $book): BookSwapRequest {
        $bookSwapRequest = new BookSwapRequest();
        $bookSwapRequest->book = $book;
        $bookSwapRequest->owner = $owner;
        $bookSwapRequest->status = BookSwapStatus::PENDING;
        $bookSwapRequest->created_at = new DateTime();
        $bookSwapRequest->requester = new User();
        
        return $bookSwapRequest;
    }
}