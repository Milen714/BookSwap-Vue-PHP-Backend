<?php
namespace App\Services\Interfaces;
use App\Models\BookSwapRequest;

/**
 * IPaymentService
 * 
 * Payment service interface defining contract for handling payment processing
 * and checkout operations via Stripe integration.
 */
interface IPaymentService {
    /**
     * Create Stripe checkout session for book swap payment
     * 
     * @param BookSwapRequest $sessionSwapRequest Book request to process payment for
     * @return void
     */
    public function stripeCheckout(BookSwapRequest $sessionSwapRequest): void;
    
    /**
     * Verify Stripe checkout session status and details
     * 
     * @param string $sessionId Stripe session ID
     * @return \Stripe\Checkout\Session Stripe session object with status information
     */
    public function verifyStripeSession($sessionId): \Stripe\Checkout\Session;
    
    /**
     * Complete book swap after successful payment
     * 
     * @param int $requestId Book request ID
     * @param int $userId User ID completing the swap
     * @return void
     */
    public function completeSwapAfterPayment(int $requestId, int $userId): void;
}