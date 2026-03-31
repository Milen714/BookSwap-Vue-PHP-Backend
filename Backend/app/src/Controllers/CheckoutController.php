<?php 
namespace App\Controllers;
use Exception;
use App\Framework\Controller;
use App\Models\BookSwapRequest;
use App\Models\Book;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Services\BookService;
use App\Repositories\BookRepository;
use App\Middleware\RequireRole;
use App\Models\Enums\UserRole;
use App\Repositories\BookAPI;
use App\Models\Enums\BookSwapStatus;
use App\Services\AuthService;
use App\Repositories\BookSwapRequestRepository;
use App\Services\BookRequestService;
use App\Services\MailService;
use App\Services\Interfaces\IPaymentService;
use App\Services\PaymentService;
use App\config\Secrets;
use App\Middleware\JWTMiddleware;

class CheckoutController extends Controller{

     private UserService $userService;
     private UserRepository $userRepository;
    private BookService $bookService;
    private BookRepository $bookRepository;
    private AuthService $authService;
    private BookRequestService $bookRequestService;
    private BookSwapRequestRepository $bookSwapRequestRepository;
    private MailService $mailService;
    private IPaymentService $paymentService;

    
    public function __construct() {
        $this->userRepository = new UserRepository();
        $this->userService = new UserService();
        $this->bookRepository = new BookRepository();
        $this->bookService = new BookService($this->bookRepository);
        $this->authService = new AuthService();
        $this->bookSwapRequestRepository = new BookSwapRequestRepository();
        $this->bookRequestService = new BookRequestService();
        $this->mailService = new MailService();
        $this->paymentService = new PaymentService();
    }


    public function createCheckoutSession($vars = []){
        try {
            // Get requestId from query parameter or session
            $requestId = $_GET['requestId'] ?? $_SESSION['currentBookRequestId'] ?? null;
            
            if (!$requestId) {
                $this->sendErrorResponse('No request ID provided', 400);
                return;
            }
            
            // Convert to int
            $requestId = (int)$requestId;
            
            $currentBookRequest = $this->bookRequestService->getRequestById($requestId);
            
            if (!$currentBookRequest) {
                $this->sendErrorResponse('Book request not found', 404);
                return;
            }
            
            $this->paymentService->stripeCheckout($currentBookRequest);
        } catch (Exception $e) {
            $this->sendErrorResponse('An error occurred while creating the checkout session: ' . $e->getMessage(), 500);
        }
    } 
    
    public function checkoutStatus($vars = []){
        try {
          $data = $this->getPostData();
          
          if (!isset($data['sessionId']) && !isset($data['requestId'])) {
             $this->sendErrorResponse('Session ID or Request ID is required', 400);
             return;
          }
      
          $session = $this->paymentService->verifyStripeSession($data['sessionId']);

          $requestId = (int)$data['requestId'];
          $userId = JWTMiddleware::getUserIdFromToken();
          if ($session->payment_status === 'paid') {
            $this->paymentService->completeSwapAfterPayment($requestId, $userId);
            }
      
          $this->sendSuccessResponse([
            'success' => true,
            'status' => $session->status,
            'customer_email' => $session->customer_details->email,
            'amount_total' => $session->amount_total
          ], 200);
        } catch (Exception $e) {
          $this->sendErrorResponse('An error occurred while retrieving the checkout status: ' . $e->getMessage(), 500);
        }

    }

}