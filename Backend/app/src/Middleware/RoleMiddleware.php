<?php 
namespace App\Middleware;

use ReflectionMethod;
use App\Models\UserRole;
use App\Models\User;
use App\Services\AuthService;

class RoleMiddleware{
    public function __construct(public AuthService $authService){}
    public function check(object $controller, string $methodName){
        header('Content-Type: application/json');
        try {
        $reflectionMethod = new ReflectionMethod($controller, $methodName);

        foreach ($reflectionMethod->getAttributes(RequireRole::class) as $attr) {
            /** @var \App\Middleware\RequireRole $requireRole */
            $requireRoleAttribute = $attr->newInstance();
            $requiredRoles = $requireRoleAttribute->roles;
            $user = $_SESSION['loggedInUser'] ?? null;

            if (!$user || !in_array($user->role, $requiredRoles)) {
                //$this->authService->logout("You do not have permission to access this resource. You have been logged out.");
                http_response_code(403);
                echo json_encode(['error' => 'You do not have permission to access this resource.']);
                exit();
            }
        }
    }
        catch (\ReflectionException $e) {
        // Handle the exception if the method does not exist
        http_response_code(500);
        echo json_encode(['error' => 'Internal Server Error: ' . $e->getMessage()]);  

    }
    catch (\Exception $e) {
        http_response_code(500);
        echo "500 Internal Server Error: " . $e->getMessage();      
    } 
}
}