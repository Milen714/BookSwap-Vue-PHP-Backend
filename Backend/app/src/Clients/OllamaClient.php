<?php
namespace App\Clients;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Client responsible for communicating with the local Ollama API
 * to generate text embeddings.
 */
class OllamaClient
{
    private Client $httpClient;
    private string $model;
    private string $endpoint;

    public function __construct(?string $model = null)
    {
        $this->httpClient = new Client([
            'timeout' => (float) (getenv('OLLAMA_TIMEOUT') ?: 120),
            'connect_timeout' => (float) (getenv('OLLAMA_CONNECT_TIMEOUT') ?: 10),
        ]);
        $this->model = $model ?: getenv('OLLAMA_MODEL') ?: 'qwen3-embedding:0.6b';
        $ollamaUrl = rtrim(getenv('OLLAMA_URL') ?: 'http://ollama:11434', '/');
        $this->endpoint = $ollamaUrl . '/api/embeddings';
    }

    /**
     * Generates a vector embedding for the given text.
     * @param string $text The text chunk to embed.
     * @return array<float> The resulting vector (array of floats).
     * @throws \Exception If the API call fails.
     */
    public function getEmbedding(string $text): array
    {
        try {
            $response = $this->httpClient->post($this->endpoint, [
                'json' => [
                    'model' => $this->model,
                    'prompt' => $text
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            if (!isset($data['embedding']) || !is_array($data['embedding'])) {
                throw new \Exception("Failed to retrieve embedding from Ollama.");
            }

            return $data['embedding'];

        } catch (GuzzleException $e) {
            // Handle network or API connection errors
            throw new \Exception("Ollama API Error: Could not connect or communicate with Ollama. " . $e->getMessage(), 0, $e);
        }
    }
}
