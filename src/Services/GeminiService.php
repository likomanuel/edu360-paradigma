<?php
namespace App\Services;

include_once __DIR__ . '/../../vendor/autoload.php';
use Exception;
use Gemini;

class GeminiService
{
    /** @var \Gemini\Client */
    private $gemini;

    public function __construct(?string $secretKey = null)
    {
        $key = $secretKey ?? $_ENV['GEMINI_KEY'] ?? '';
        $this->gemini = Gemini::client($key);
    }

    public function cleanJSON($json)
    {
        preg_match('/\{.*\}/s', $json, $coincidencias);
        if ($coincidencias && isset($coincidencias[0])) {
            $datos = json_decode($coincidencias[0], true);
        }

        if (json_last_error() === JSON_ERROR_NONE && isset($datos['sentimiento'])) {
            return $datos;
        } else {
            return ['error' => true, 'message' => 'Error al procesar la respuesta de la IA.'];
        }
    }

    public function generateResponse(string $prompt)
    {
        try {
            $response = $this->gemini->generativeModel(model: 'gemini-1.5-flash')->generateContent($prompt);
            $json = $response->text();
            return $this->cleanJSON($json);
        } catch (Exception $e) {
            error_log("[GEMINI_SERVICE_ERROR] " . $e->getMessage());
            throw $e;
        }
    }
}
