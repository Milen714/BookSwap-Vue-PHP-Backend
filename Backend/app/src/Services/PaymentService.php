<?php 
namespace App\Services;
use App\Services\Interfaces\IPaymentService;
use App\Models\Book;
use App\Models\BookSwapRequest;
use App\Models\Enums\BookSwapStatus;
use App\config\Secrets;
use App\Services\Interfaces\IUserService;
use App\Services\UserService;
use App\Repositories\Interfaces\IBookSwapRequestRepository;
use App\Repositories\BookSwapRequestRepository;
use App\Repositories\Interfaces\IBookRepository;
use App\Repositories\BookRepository;
use App\Services\Interfaces\IMailService;
use App\Services\MailService;
use App\Exceptions\NotFoundException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\ValidationException;

/**
 * Service handling Stripe checkout and swap completion after successful payment.
 */
class PaymentService implements IPaymentService {
    /**
     * User service for token/account operations.
     */
    private IUserService $userService;

    /**
     * Repository for swap request state changes.
     */
    private IBookSwapRequestRepository $bookSwapRequestRepository;

    /**
     * Repository for book listing state updates.
     */
    private IBookRepository $bookRepository;

    /**
     * Mail notifications service.
     */
    private IMailService $mailService;


    /**
     * Initialize payment workflow dependencies.
     */
    public function __construct() {
        $this->userService = new UserService();
        $this->bookSwapRequestRepository = new BookSwapRequestRepository();
        $this->bookRepository = new BookRepository();
        $this->mailService = new MailService();
    }

    /**
     * Create an embedded Stripe checkout session for request shipping cost.
     *
     * @param BookSwapRequest $sessionSwapRequest Swap request context.
     * @return void
     */
    public function stripeCheckout(BookSwapRequest $sessionSwapRequest): void {
        $stripeSecretKey = Secrets::$stripeSecretKey;
        $stripe = new \Stripe\StripeClient($stripeSecretKey);

        header('Content-Type: application/json');


        $session = $stripe->checkout->sessions->create([
            'ui_mode' => 'embedded',
            'mode' => 'payment',

            // Minimal line item (no dashboard setup needed)
            'line_items' => [[
            'price_data' => [
            'currency' => 'eur',
            'product_data' => [
                'name' => $sessionSwapRequest->book->title . ' Shipping Cost',
            ],
            'unit_amount' => $sessionSwapRequest->shipping_cost * 100, // amount in cents
        ],
        'quantity' => 1,
    ]],

    // Stripe will replace {CHECKOUT_SESSION_ID}
    'return_url' => Secrets::$frontendUrl . '/return?session_id={CHECKOUT_SESSION_ID}&requestId=' . $sessionSwapRequest->id,
]);

echo json_encode([
    'clientSecret' => $session->client_secret
]);
    }

    /**
     * Retrieve and verify an existing Stripe checkout session.
     *
     * @param mixed $sessionId Stripe session id.
     * @return \Stripe\Checkout\Session Retrieved session object.
     */
    public function verifyStripeSession($sessionId): \Stripe\Checkout\Session {
        $stripeSecretKey = Secrets::$stripeSecretKey;
        $stripe = new \Stripe\StripeClient($stripeSecretKey);

        return $stripe->checkout->sessions->retrieve($sessionId);
    }

    /**
     * Complete swap transaction after successful payment validation.
     *
     * @param int $requestId Swap request id.
     * @param int $userId Authenticated requester id.
     * @return void
     * @throws NotFoundException When request does not exist.
     * @throws ForbiddenException When user is not the request owner.
     * @throws ValidationException When requester has no available swap tokens.
     */
    public function completeSwapAfterPayment(int $requestId, int $userId): void {
        
        $swapRequest = $this->bookSwapRequestRepository->getRequestById($requestId);
        if (!$swapRequest) {
            throw new NotFoundException('Swap request not found');
        }
        
        if ($swapRequest->requester->id !== $userId) {
            throw new ForbiddenException('User is not the requester of this swap');
            }
            if ($swapRequest->status === BookSwapStatus::PENDING) {
            $this->bookSwapRequestRepository->updateRequestStatus($requestId, BookSwapStatus::SHIPPINGPAID->value);
            if (!$this->userService->deductSwapToken($userId)) {
                throw new ValidationException('Insufficient swap tokens for checkout');
            }
            $this->mailService->notifyRequester($swapRequest->requester->email, $swapRequest);
            $this->bookRepository->deactivateBookPost($swapRequest->book->id);
        }
    }

}