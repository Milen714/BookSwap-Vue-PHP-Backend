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

class PaymentService implements IPaymentService {
    private IUserService $userService;
    private IBookSwapRequestRepository $bookSwapRequestRepository;
    private IBookRepository $bookRepository;
    private IMailService $mailService;


    public function __construct() {
        $this->userService = new UserService();
        $this->bookSwapRequestRepository = new BookSwapRequestRepository();
        $this->bookRepository = new BookRepository();
        $this->mailService = new MailService();
    }

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

    public function verifyStripeSession($sessionId): \Stripe\Checkout\Session {
        $stripeSecretKey = Secrets::$stripeSecretKey;
        $stripe = new \Stripe\StripeClient($stripeSecretKey);

        return $stripe->checkout->sessions->retrieve($sessionId);
    }

    public function completeSwapAfterPayment(int $requestId, int $userId): void {
        
        $swapRequest = $this->bookSwapRequestRepository->getRequestById($requestId);
        if (!$swapRequest) {
            throw new \Exception('Swap request not found');
        }
        
        if ($swapRequest->requester->id !== $userId) {
            throw new \Exception('User is not the requester of this swap');
            }
            if ($swapRequest->status === BookSwapStatus::PENDING) {
            $this->bookSwapRequestRepository->updateRequestStatus($requestId, BookSwapStatus::SHIPPINGPAID->value);
            $this->userService->deductSwapToken($userId);
            $this->mailService->notifyRequester($swapRequest->requester->email, $swapRequest);
            $this->bookRepository->deactivateBookPost($swapRequest->book->id);
        }
    }

}