<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/helpers.php';
require_once __DIR__ . '/../src/Services/GeminiService.php';

use App\Services\GeminiService;

// Test email - Puedes cambiarlo por uno real para probar
$test_email = "marthafiguera2011@gmail.com"; 

echo "--- Probando GeminiService::scrap ---\n";
echo "Email: $test_email\n";

try {
    $geminiService = new GeminiService();
    echo "Servicio instanciado correctamente.\n";

    echo "Iniciando scrap...\n";
    $result = $geminiService->scrap($test_email);

    echo "Resultado:\n";
    if (is_array($result)) {
        if (isset($result['success']) && $result['success']) {
            echo "¡OPERACIÓN CARGADA!\n";
            echo "\n--- DATOS DE PERFIL (Enrichment) ---\n";
            if (isset($result['profile']['success']) && $result['profile']['success']) {
                print_r($result['profile']);
            } else {
                echo "No se encontró perfil en la base de datos de Snov.io.\n";
            }

            echo "\n--- VERIFICACIÓN DE EMAIL (Verifier) ---\n";
            if (isset($result['verification'])) {
                print_r($result['verification']);
            } else {
                echo "Error al obtener datos de verificación.\n";
            }
        } else {
            echo "FALLO CRÍTICO: " . ($result['message'] ?? 'Error desconocido') . "\n";
            if (isset($result['raw'])) {
                print_r($result['raw']);
            }
        }
    } else {
        echo "Resultado inesperado (no es array): $result\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
echo "--- Fin del test ---\n";
