<?php
session_start();
require_once __DIR__ . '/../config/modulo.php';
require_once __DIR__ . '/../src/Services/GeminiService.php';

use App\Services\GeminiService;

header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no iniciada']);
    exit;
}

$modulo = new Modulo();
$modulo->ensureEvolucionadoresProfileCols();
$user = $modulo->getUser($_SESSION['email']);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

$ciudad = $_POST['ciudad'] ?? '';
$profesion = $_POST['profesion'] ?? '';
$empresa = $_POST['empresa'] ?? '';
$red_social = $_POST['red_social'] ?? '';

if (empty($ciudad) || empty($profesion)) {
    echo json_encode(['success' => false, 'message' => 'Ciudad y Profesión son obligatorios']);
    exit;
}

try {
    $gemini = new GeminiService();
    
    // Paso 1: Verificar el email y enriquecer perfil
    $scrapResult = $gemini->scrap($user['email_verificado']);

    if (!$scrapResult['success']) {
        echo json_encode(['success' => false, 'message' => 'Error al conectar con el servicio de verificación: ' . ($scrapResult['message'] ?? 'Error desconocido')]);
        exit;
    }

    // Validar estado de verificación del email
    $vData = $scrapResult['verification'] ?? null;
    error_log("[SNOV_RAW] " . json_encode($vData)); // Debug: siempre registrar estructura real

    // Extraer smtp_status desde cualquier nivel de la estructura de respuesta
    $smtpStatus = null;
    if (is_array($vData)) {
        // Estructura: data[0][result][smtp_status]
        if (isset($vData['data'][0]['result']['smtp_status'])) {
            $smtpStatus = $vData['data'][0]['result']['smtp_status'];
        }
        // Estructura alternativa: data[0][smtp_status]
        elseif (isset($vData['data'][0]['smtp_status'])) {
            $smtpStatus = $vData['data'][0]['smtp_status'];
        }
        // Estructura plana: result[smtp_status]
        elseif (isset($vData['result']['smtp_status'])) {
            $smtpStatus = $vData['result']['smtp_status'];
        }
        // Estructura plana directo
        elseif (isset($vData['smtp_status'])) {
            $smtpStatus = $vData['smtp_status'];
        }
    }

    if ($smtpStatus === 'valid') {
        $isEmailValid = true;
    } elseif ($smtpStatus !== null) {
        // El email existe pero no es válido (invalid, disposable, etc.)
        echo json_encode(['success' => false, 'message' => 'El correo electrónico no es válido según la auditoría (Snov.io). Status: ' . $smtpStatus]);
        exit;
    } elseif (isset($vData['status']) && $vData['status'] === 'in_progress') {
        echo json_encode(['success' => false, 'message' => 'La verificación del correo sigue en progreso tras varios reintentos. Espera unos segundos e intenta de nuevo.']);
        exit;
    } else {
        // Sin estructura reconocible — dejar pasar (benevolente) para no bloquear por falla de Snov.io
        error_log("[SNOV_WARN] Estructura desconocida, permitiendo paso. Raw: " . json_encode($vData));
        $isEmailValid = true;
    }


    // Paso 2: Auditoría OSINT con IA
    // Obtenemos el prompt base usando Reflection para acceder a la propiedad privada (o podrías hacerla pública)
    $reflection = new ReflectionClass($gemini);
    $property = $reflection->getProperty('prompt_verificacion');
    $property->setAccessible(true);
    $basePrompt = $property->getValue($gemini);

    $finalPrompt = str_replace(
        ['[Nombre Completo]', '[Ciudad/Estado]'],
        [$user['nombre_completo'], $ciudad],
        $basePrompt
    );

    $finalPrompt .= "\n\nInformación Adicional proporcionada por el usuario:\n";
    $finalPrompt .= "- Gremio/Profesión: $profesion\n";
    $finalPrompt .= "- Empresa/Institución: $empresa\n";
    $finalPrompt .= "- Red Social: $red_social\n";
    $finalPrompt .= "\nPor favor, responde exclusivamente con un JSON que contenga: \n";
    $finalPrompt .= "- 'verificado': (boolean) true si encontraste coherencia suficiente.\n";
    $finalPrompt .= "- 'puntos_clave': (array) con los hallazgos principales.\n";
    $finalPrompt .= "- 'resumen_ejecutivo': (string) una breve explicación de la auditoría.\n";

    $aiResponse = $gemini->generateResponse($finalPrompt);

    // Validar respuesta de IA
    if (isset($aiResponse['error']) && $aiResponse['error']) {
        echo json_encode(['success' => false, 'message' => 'Error en la auditoría de IA: ' . $aiResponse['message']]);
        exit;
    }

    // Paso 3: Actualizar Base de Datos si la IA dio el visto bueno
    if (isset($aiResponse['verificado']) && $aiResponse['verificado']) {
        $pdo = $modulo->getDb()->getConnection();
        $updateSql = "UPDATE evolucionadores 
                      SET verificado = 1, total_udv_acumuladas = total_udv_acumuladas + 3,
                          ciudad = :ciudad, profesion = :profesion, empresa = :empresa, red_social = :red_social
                      WHERE email_verificado = :email";
        $stmt = $pdo->prepare($updateSql);
        $stmt->execute([
            ':ciudad'     => $ciudad,
            ':profesion'  => $profesion,
            ':empresa'    => $empresa,
            ':red_social' => $red_social,
            ':email'      => $user['email_verificado'],
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Identidad verificada con éxito. Se han otorgado 3 UDV.',
            'ia_report' => $aiResponse
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'La auditoría de IA no pudo verificar tu identidad con la información proporcionada.',
            'ia_report' => $aiResponse
        ]);
    }

} catch (Exception $e) {
    error_log("[VERIFICACION_ERROR] " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Excepción interna: ' . $e->getMessage()]);
}
