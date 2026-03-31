<?php
namespace App\Services\Interfaces;
use App\Models\BookSwapRequest;
interface IPaymentService {
    public function stripeCheckout(BookSwapRequest $sessionSwapRequest): void;
    //public function stripeCheckoutStatus(string $sessionId): bool;
    public function verifyStripeSession($sessionId): \Stripe\Checkout\Session;
    public function completeSwapAfterPayment(int $requestId, int $userId): void;
}