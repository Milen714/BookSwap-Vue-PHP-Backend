<?php 
namespace App\Middleware;

use ReflectionMethod;
use App\Models\Enums\UserRole;
use App\Models\User;
use App\Services\AuthService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\config\Secrets;

class RoleMiddleware{
    public function __construct(public AuthService $authService){}
    public function check(object $controller, string $methodName){
        try {
            $reflectionMethod = new ReflectionMethod($controller, $methodName);

            foreach ($reflectionMethod->getAttributes(RequireRole::class) as $attr) {
                /** @var \App\Middleware\RequireRole $requireRole */
                $requireRoleAttribute = $attr->newInstance();
                $requiredRoles = $requireRoleAttribute->roles;
                
                $userRole = null;
                
                try {
                    // Try to get role from JWT token
                    $token = JWTMiddleware::getTokenFromHeader();
                    if ($token) {
                        $decoded = JWT::decode($token, new Key(Secrets::$secretKey, Secrets::JWT_ALGORITHM));
                        $userRole = $decoded->data->role ?? null;
                        error_log("DEBUG: JWT decoded. userRole: " . json_encode($userRole) . ", requiredRoles: " . json_encode($requiredRoles));
                    } else {
                        error_log("DEBUG: No token found in header");
                    }
                } catch (\Exception $e) {
                    error_log("DEBUG: JWT decode failed: " . $e->getMessage());
                    // Fall back to session if JWT fails
                    $userRole = $_SESSION['loggedInUser']->role ?? null;
                }

                // Check if user has required role
                // Convert enum to string for comparison if needed
                $userRoleValue = $userRole instanceof UserRole ? $userRole->value : (string)$userRole;
                $hasRole = false;
                
                foreach ($requiredRoles as $required) {
                    $requiredValue = $required instanceof UserRole ? $required->value : (string)$required;
                    if ($userRoleValue === $requiredValue) {
                        $hasRole = true;
                        break;
                    }
                }
                
                error_log("DEBUG: Role check - userRole: $userRoleValue, hasRole: $hasRole, requiredRoles: " . json_encode($requiredRoles));
                
                if (!$hasRole) {
                    http_response_code(403);
                    header('Content-Type: application/json');
                    echo json_encode(['error' => 'You do not have permission to access this resource. Required roles: ' . json_encode($requiredRoles) . ', user role: ' . $userRoleValue]);
                    exit();
                }
            }
        }
        catch (\ReflectionException $e) {
            // Handle the exception if the method does not exist
            http_response_code(500);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Internal Server Error: ' . $e->getMessage()]);  
        }
        catch (\Exception $e) {
            http_response_code(500);
            echo "500 Internal Server Error: " . $e->getMessage();      
        } 
    }
}