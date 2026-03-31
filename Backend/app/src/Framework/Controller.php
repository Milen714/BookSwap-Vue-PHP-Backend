<?php

namespace App\Framework;

/**
 * Controller
 * 
 * Base controller class providing common utilities for all application controllers.
 * Includes methods for view rendering, JSON response handling, and POST data processing.
 */
class Controller
{
    /**
     * Render a view file with optional layout template
     * 
     * Loads a view file with provided variables, captures output, and renders
     * within a layout template to create the final HTML output.
     * 
     * @param string $viewName The name of the view file (without .php extension)
     * @param array $vars Variables to pass to the view
     * @param string $layout The layout template file path
     * @return void
     */
    protected function view($viewName, $vars = [], $layout = 'layouts/mainLayout')
    {
        // Load the view and capture output
        ob_start();
        extract($vars);
        
        require __DIR__ . '/../../Views/' . $viewName . '.php';
        // ob_get_clean(); prevent double output and turns the inside of the require ^^ into a string which is stored in $content
        $content = ob_get_clean();
        
        // Load the layout with the content
        extract(array_merge($vars, ['content' => $content]));
        require __DIR__ . '/../../Views/' . $layout . '.php';
        // The layout will use the $content variable to display the view content in the main tag 
        //Layout is foooter and header around the content
    }
    /**
     * Send a successful JSON response to the client
     * 
     * Sets Content-Type header and HTTP status code, then encodes and echoes
     * the provided data as JSON.
     * 
     * @param array $data The response data to encode as JSON
     * @param int $code HTTP response code (default: 200)
     * @return void
     */
    protected function sendSuccessResponse($data = [], $code = 200)
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data, JSON_PRETTY_PRINT);
    }

    /**
     * Send an error JSON response to the client
     * 
     * Sets Content-Type header and HTTP status code, then encodes and echoes
     * an error object with the provided message.
     * 
     * @param string|array $message The error message or error data
     * @param int $code HTTP error code (default: 500)
     * @return void
     */
    protected function sendErrorResponse($message, $code = 500)
    {
        header('Content-Type: application/json; charset=utf-8');
        http_response_code($code);
        echo json_encode(['error' => $message], JSON_PRETTY_PRINT);
    }

    /**
     * Gets and decodes JSON data from the request body
     * 
     * @return array|null Returns decoded JSON data as array or null if invalid
     */
    protected function getPostData(): ?array
    {
        $input = file_get_contents('php://input');
        return json_decode($input, true);
    }

    /**
     * Maps POST data (JSON) to an instance of the specified class
     * 
     * @param string $className The fully qualified class name
     * @return object|null Returns an instance of the class or null if data is invalid
     */
    protected function mapPostDataToClass(string $className): ?object
    {
        $data = $this->getPostData();

        $instance = new $className();
        
        foreach ($data as $key => $value) {
            if (property_exists($instance, $key)) {
                $instance->$key = $value;
            }
        }

        return $instance;
    }
}