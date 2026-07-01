<?php



// CORS headers for localhost requests
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (preg_match('/^https?:\/\/(localhost|127\.0\.0\.1|::1)(:\d+)?$/', $origin)) {
    header('Access-Control-Allow-Origin: ' . $origin);
    // Specifies which HTTP methods are allowed when accessing the resource from the origin
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    // Specifies which HTTP headers can be used when making the actual request
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    // Allows cookies and authentication credentials to be sent with cross-origin requests
    header('Access-Control-Allow-Credentials: true');
    // Specifies how long (in seconds) the browser can cache the preflight response (24 hours)
    header('Access-Control-Max-Age: 86400');
}

// Handle preflight OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../config/config.php';

use FastRoute\RouteCollector;
use function FastRoute\simpleDispatcher;
use App\Services\AuthService;
use App\Middleware\RoleMiddleware;

/**
 * Define the routes for the application.
 */
$dispatcher = simpleDispatcher(function (RouteCollector $r) {
    // Auth routes
    $r->addRoute('POST', '/login', ['App\Controllers\AuthController', 'login']);
    $r->addRoute('POST', '/logout', ['App\Controllers\AuthController', 'logout']);
    $r->addRoute('POST', '/signUp', ['App\Controllers\AuthController', 'signUp']);
    $r->addRoute('GET', '/getLoggedInUser', ['App\Controllers\AuthController', 'getLoggedInUser']);
    $r->addRoute('POST', '/forgot-password', ['App\Controllers\AuthController', 'forgotPasswordPost']);
    $r->addRoute('GET', '/reset-password', ['App\Controllers\AuthController', 'resetPassword']);
    $r->addRoute('POST', '/reset-password', ['App\Controllers\AuthController', 'resetPasswordPost']);

    // Theme / preferences routes
    $r->addRoute('PUT', '/setTheme', ['App\Controllers\HomeController', 'setTheme']);

    // Book routes
    $r->addRoute('POST', '/fetchBookPreview', ['App\Controllers\BookController', 'fetchBookPreview']);
    $r->addRoute('POST', '/addBook', ['App\Controllers\BookController', 'addBookPost']);
    $r->addRoute('GET', '/scanBook/{isbn}', ['App\Controllers\BookController', 'scanBook']);
    $r->addRoute('GET', '/getAllBooks', ['App\Controllers\BookController', 'getAllBooks']);
    $r->addRoute('GET', '/getAllGenres', ['App\Controllers\BookController', 'getAllGenres']);
    $r->addRoute('GET', '/getBookDetails', ['App\Controllers\BookController', 'getBookDetails']);
    $r->addRoute('GET', '/getUserBooks/{userId}', ['App\Controllers\BookController', 'getUserBooks']);
    $r->addRoute('GET', '/embedBooks', ['App\Controllers\BookController', 'embedBooks']);

    // Book request routes
    $r->addRoute('POST', '/createBookRequest', ['App\Controllers\BookRequestController', 'requestBookSwap']);
    $r->addRoute('PUT', '/updateRequest', ['App\Controllers\BookRequestController', 'updateRequestStatus']);
    $r->addRoute('GET', '/getBookSwapStatusses', ['App\Controllers\BookRequestController', 'getBookSwapStatusses']);
    $r->addRoute('GET', '/getBookRequestById', ['App\Controllers\BookRequestController', 'getRequestById']);
    $r->addRoute('GET', '/getMyBookRequests', ['App\Controllers\BookRequestController', 'getMyBookRequests']);
    $r->addRoute('GET', '/getMyBookListings', ['App\Controllers\BookRequestController', 'getMyListings']);

    // Checkout routes
    $r->addRoute('GET', '/create-checkout-session', ['App\Controllers\CheckoutController', 'createCheckoutSession']);
    $r->addRoute('POST', '/checkout-status', ['App\Controllers\CheckoutController', 'checkoutStatus']);

    // User / profile routes
    $r->addRoute('GET', '/getProfileAddress/{id}', ['App\Controllers\UserController', 'getProfileAddress']);
    $r->addRoute('GET', '/getUserInfo', ['App\Controllers\UserController', 'getUserInfo']);
    $r->addRoute('GET', '/getUserTokens', ['App\Controllers\UserController', 'getUserTokens']);
    $r->addRoute('PUT', '/updateProfile', ['App\Controllers\UserController', 'updateProfile']);
    $r->addRoute('PUT', '/changePassword', ['App\Controllers\UserController', 'changePassword']);
    $r->addRoute('PUT', '/updateAddress', ['App\Controllers\UserController', 'updateAddress']);
    $r->addRoute('GET', '/getUser/{userId}', ['App\Controllers\UserController', 'getUserInfoById']);

    // Admin routes
    $r->addRoute('GET', '/getAllUsers', ['App\Controllers\UserController', 'getAllUsers']);
    $r->addRoute('GET', '/getAdminAnalytics', ['App\Controllers\UserController', 'getAdminAnalytics']);
    $r->addRoute('PUT', '/toggleUserStatus', ['App\Controllers\UserController', 'toggleUserStatus']);

    // Chat routes
    $r->addRoute('GET', '/getChatMessages', ['App\Controllers\ChatController', 'getChatMessages']);
    $r->addRoute('POST', '/sendDirectMessage', ['App\Controllers\ChatController', 'sendDirectMessage']);
    $r->addRoute('GET', '/getChatPartners', ['App\Controllers\ChatController', 'getChatPartners']);
});


/**
 * Get the request method and URI from the server variables and invoke the dispatcher.
 */
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = strtok($_SERVER['REQUEST_URI'], '?');
$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

/**
 * Switch on the dispatcher result and call the appropriate controller method if found.
 */
switch ($routeInfo[0]) {
    // Handle not found routes
    case FastRoute\Dispatcher::NOT_FOUND:
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
    // Handle routes that were invoked with the wrong HTTP method
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        header('Content-Type: application/json');
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        break;
    // Handle found routes
    case FastRoute\Dispatcher::FOUND:
        /**
         * $routeInfo contains the data about the matched route.
         * 
         * $routeInfo[1] is the whatever we define as the third argument the `$r->addRoute` method.
         *  For instance for: `$r->addRoute('GET', '/hello/{name}', ['App\Controllers\HelloController', 'greet']);`
         *  $routeInfo[1] will be `['App\Controllers\HelloController', 'greet']`
         * 
         * Hint: we can use class strings like `App\Controllers\HelloController` to create new instances of that class.
         * Hint: in PHP we can use a string to call a class method dynamically, like this: `$instance->$methodName($args);`
         */
            
        // TODO: invoke the controller and method using the data in $routeInfo[1]

        /**
         * $route[2] contains any dynamic parameters parsed from the URL.
         * For instance, if we add a route like:
         *  $r->addRoute('GET', '/hello/{name}', ['App\Controllers\HelloController', 'greet']);
         * and the URL is `/hello/dan-the-man`, then `$routeInfo[2][name]` will be `dan-the-man`.
         */

        // TODO: pass the dynamic route data to the controller method
        // When done, visiting `http://localhost/hello/dan-the-man` should output "Hi, dan-the-man!"

        $controller = new $routeInfo[1][0]();
        $method = $routeInfo[1][1];
        $params = $routeInfo[2];
        
    //     if (session_status() === PHP_SESSION_NONE) {
    //     session_start();
    // }
        

        $authService = new AuthService();
        $roleMiddleware = new RoleMiddleware($authService);

        // Run the middleware check
        $roleMiddleware->check($controller, $method);
        $controller->$method($params);

        break;
}