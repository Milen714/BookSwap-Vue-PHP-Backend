<?php 
namespace App\Services;
use App\Services\Interfaces\IPaymentService;
use App\Models\Book;
use App\Models\BookSwapRequest;

class PaymentService implements IPaymentService {

    public function __construct() {
    }

    public function stripeCheckout(BookSwapRequest $sessionSwapRequest): void {
        require_once '../config/secrets.php';

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
    'return_url' => $DOMAIN_URL . '/return?session_id={CHECKOUT_SESSION_ID}&requestId=' . $sessionSwapRequest->id,
]);

echo json_encode([
    'clientSecret' => $session->client_secret
]);
    }

}