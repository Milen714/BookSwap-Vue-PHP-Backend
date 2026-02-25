<?php 
namespace App\Controllers;
use Exception;
use App\Models\BookSwapRequest;
use App\Models\Book;
use App\Models\User;
use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Services\BookService;
use App\Repositories\BookRepository;
use App\Middleware\RequireRole;
use App\Models\UserRole;
use App\Repositories\BookAPI;
use App\Models\BookSwapStatus;
use App\Services\AuthService;
use App\Repositories\BookSwapRequestRepository;
use App\Services\BookRequestService;
use App\Services\MailService;
use App\Services\Interfaces\IPaymentService;
use App\Services\PaymentService;

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
        $this->userService = new UserService($this->userRepository);
        $this->bookRepository = new BookRepository();
        $this->bookService = new BookService($this->bookRepository);
        $this->authService = new AuthService();
        $this->bookSwapRequestRepository = new BookSwapRequestRepository();
        $this->bookRequestService = new BookRequestService($this->bookSwapRequestRepository);
        $this->mailService = new MailService();
        $this->paymentService = new PaymentService();
    }

    public function checkout($vars = []){
        $currentBookRequest = $this->bookRequestService->getRequestById($_SESSION['currentBookRequestId'] ?? null);
        if(isset($_GET['requestId']) && isset($currentBookRequest)){
            if($_GET['requestId'] != $currentBookRequest->id 
            &&  $currentBookRequest->requester->id != $_SESSION['loggedInUser']->id){
                header("Location: /error/Invalid%20book%20request%20session.");
                exit();
            }
            $swapRequestId =  isset($_GET['requestId']) ? $_GET['requestId'] : null;
            $sessionSwapRequest = isset($_SESSION['currentBookRequest']) ? $_SESSION['currentBookRequest'] : null;

            $this->view('Checkout/Checkout', [
                'message' => "Checkout Page", 
                'title' => 'Checkout'
            ] );

        }
        else{
            die("Invalid book request session.");
            exit();
        }
        
    }
    public function createCheckoutSession($vars = []){
        try {
        $currentBookRequest = $this->bookRequestService->getRequestById($_SESSION['currentBookRequestId'] ?? null);
        $this->paymentService->stripeCheckout($currentBookRequest);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'An error occurred while creating the checkout session.']);
        }
        //require '../payment/checkout.php';  
    } 
    public function return($vars = []){
        $requestId = $_GET['requestId'] ?? null;
        
        if (!$requestId) {
            die("Invalid request ID.");
        }
        
        $currentBookRequest = $this->bookRequestService->getRequestById((int)$requestId);
        
        if (!$currentBookRequest) {
            die("Request not found.");
        }
        
        $this->bookRequestService->updateRequestStatus($requestId, BookSwapStatus::SHIPPINGPAID->value);
        
        // Clear the session variable
        //deduct swap token from requester
        $this->userService->deductSwapToken($_SESSION['loggedInUser']->id);
        //send email notification to requester about successful payment
        $this->mailService->notifyRequester($_SESSION['loggedInUser']->email, $currentBookRequest);
        $this->bookService->deactivateBookPost($currentBookRequest->book->id);

        $_SESSION['currentBookRequestId'] = null;
        session_write_close();

        // Get Stripe session info for the view
        require __DIR__ . '/../../config/secrets.php';
        $stripe = new \Stripe\StripeClient($stripeSecretKey);
        
        $sessionId = $_GET['session_id'] ?? null;
        $session = $sessionId ? $stripe->checkout->sessions->retrieve($sessionId) : null;
        $isPaid = $session && $session->payment_status === 'paid';

        $this->view('Checkout/Return', [
            'message' => "Return Page", 
            'title' => 'Return',
            'sessionId' => $sessionId,
            'session' => $session,
            'isPaid' => $isPaid
        ]);
    }
    public function checkoutStatus($vars = []){
        require_once __DIR__ . '/../../config/secrets.php';

        $stripe = new \Stripe\StripeClient($stripeSecretKey);
        header('Content-Type: application/json');

        try {
          // retrieve JSON from POST body
          $jsonStr = file_get_contents('php://input');
          $jsonObj = json_decode($jsonStr);
      
          $session = $stripe->checkout->sessions->retrieve($jsonObj->session_id);
      
          echo json_encode(['status' => $session->status, 'customer_email' => $session->customer_details->email]);
          http_response_code(200);
        } catch (Exception $e) {
          http_response_code(500);
          echo json_encode(['error' => $e->getMessage()]);
        }

    }

}