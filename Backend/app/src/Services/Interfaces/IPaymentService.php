<?php
namespace App\Services\Interfaces;
use App\Models\BookSwapRequest;
interface IPaymentService {
    public function stripeCheckout(BookSwapRequest $sessionSwapRequest): void;
    //public function stripeCheckoutStatus(string $sessionId): bool;
}