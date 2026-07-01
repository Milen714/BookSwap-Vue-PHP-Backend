<?php
require_once __DIR__ . '/../app/vendor/autoload.php';
use App\Clients\OllamaClient;


$client = new OllamaClient();
$embedding = $client->getEmbedding("PEDERAS");
print_r($embedding);
?>