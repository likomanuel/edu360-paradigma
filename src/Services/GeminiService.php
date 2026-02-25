<?php
namespace App\Services;

include_once __DIR__ . '/../../vendor/autoload.php';
use Exception;
use Gemini;

class GeminiService
{
    /** @var \Gemini\Client */
    private $gemini;

    private $prompt_verificacion = "Actúa como un investigador de fuentes abiertas (OSINT). Realiza una búsqueda web exhaustiva sobre [Nombre Completo] en la localidad de [Ciudad/Estado]. Enfócate exclusivamente en información de dominio público como:
    Perfiles profesionales (LinkedIn, directorios gremiales).
    Participación en eventos públicos, conferencias o publicaciones académicas.
    Menciones en boletines oficiales, registros mercantiles o gacetas (siempre que se trate de cargos públicos o registros de empresas).
    Actividad en organizaciones civiles o deportivas.    
    ";

    private $prompt_maestro = <<<'PROMPT'
    Actúa como el Nodo Validador de EDU360 University Institute. Tu función es auditar la Densidad Cognitiva del Evolucionador mediante el Sistema SRAA. No eres un tutor; eres un auditor de soberanía intelectual.
    RECURSO FUNDACIONAL: https://github.com/likomanuel/base-conocimiento-edu360-paradigma/blob/main/paradigma_edu360
    
    PROTOCOLOS DE COMPORTAMIENTO:
    1. MAYÉUTICA RADICAL: Prohibido impartir lecciones. Si el Evolucionador ignora algo, no se lo expliques; indícale su "Punto de Fuga" y ordénale reconstruir su conocimiento. Evalúa la capacidad de síntesis y la aplicación práctica, no la repetición de conceptos.
    2. LÉXICO OBLIGATORIO: Tu lenguaje debe ser técnico y alineado al paradigma. Usa: Acuñación, UDV, SRAA, Legado Cognitivo, Evolucionador, Soberanía Intelectual, Rigor Federal, Humanismo Digital y Densidad Cognitiva.
    3. CRITERIO DE ACUÑACIÓN (SRAA):
    - 0.0 UDV: Respuesta superficial, sin sentido, circular o generada por otra IA.
    - 0.1 - 0.4 UDV: Comprensión teórica básica pero sin aplicación sistémica.
    - 0.5 - 0.9 UDV: Dominio funcional con capacidad de interconectar conceptos del paradigma.
    - 1.0 UDV: Dominio excepcional, propuesta original o resolución de alta complejidad.
    4. FILTRO DE INTEGRIDAD: Si detectas patrones de GPT, Claude u otras IAs (listas genéricas, exceso de cortesía, frases como "es importante recordar"), otorga 0.0 UDV, emite una 'Advertencia de Integridad' y bloquea el avance.    
    5. EVALUACIÓN ATÓMICA: Tu puntuación de `udv_otorgadas` debe basarse EXCLUSIVAMENTE en el contenido de la 'Nueva respuesta del Evolucionador'. No arrastres méritos de respuestas anteriores del historial. Si la respuesta actual es irrelevante, sin sentido o circular, otorga 0.0 UDV independientemente de la calidad histórica.
    
    ESTRUCTURA DE SALIDA (ESTRICTO JSON):
    {
        "mensaje": "Texto directo al Evolucionador. Si UDV < 0.4, define el 'Punto de Fuga' (ej: 'Falla en la comprensión de la irreversibilidad intelectual'). Incluye un 'Desafío Lógico' final para la siguiente interacción.",
        "udv_otorgadas": [float],
        "veredicto": "Elegir uno entre: Acuñado, En Desarrollo o Advertencia de Integridad",
        "analisis_tecnico": "Explicación breve para el sistema sobre por qué se asignó ese puntaje basado en la neuroplasticidad o rigor demostrado."
    }
    
    CONTEXTO OPERATIVO:
    - Meta: {{meta_nombre}}
    - Descripción: {{meta_descripcion}}
    - Objetivo: {{meta_objetivo}}
    - UDV Acumuladas en esta meta: {{udv_acumuladas}} / {{valor_udv_meta}}
    
    IMPORTANTE: Si las UDV totales (acumuladas + otorgadas) igualan o superan {{valor_udv_meta}}, el veredicto debe ser 'Acuñado' y debes felicitar al Evolucionador por consolidar un nuevo fragmento de su Soberanía Intelectual.
    PROMPT;
    
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

        if (json_last_error() === JSON_ERROR_NONE && !empty($datos)) {
            return $datos;
        } else {
            return ['error' => true, 'message' => 'Error al procesar la respuesta de la IA.'];
        }
    }

    private function getSnovToken()
    {
        $clientId = $_ENV['SNOW_CLIENT_ID'] ?? '';
        $clientSecret = $_ENV['SNOW_CLIENT_SECRET'] ?? '';

        if (empty($clientId) || empty($clientSecret)) {
            return ['success' => false, 'message' => 'Faltan credenciales SNOW_CLIENT_ID / SNOW_CLIENT_SECRET'];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.snov.io/v1/oauth/access_token");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials'
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $responseRaw = curl_exec($ch);
        $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $data = json_decode($responseRaw, true);
        if ($httpStatus !== 200 || !isset($data['access_token'])) {
            return ['success' => false, 'message' => 'Error de autenticación con Snov.io', 'raw' => $data];
        }

        return ['success' => true, 'access_token' => $data['access_token']];
    }

    public function verifyEmail(string $email)
    {
        $tokenRes = $this->getSnovToken();
        if (!$tokenRes['success']) return $tokenRes;
        $token = $tokenRes['access_token'];

        // Snov.io v2 Email Verifier: Start
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.snov.io/v2/email-verification/start");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(['emails' => [$email]]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $resStart = json_decode(curl_exec($ch), true);
        curl_close($ch);

        if (!isset($resStart['data']['task_hash'])) {
            return ['success' => false, 'message' => 'No se pudo iniciar la verificación', 'raw' => $resStart];
        }

        $hash = $resStart['data']['task_hash'];

        // Polling: hasta 5 reintentos esperando 3 segundos cada uno (15 seg máx)
        $maxAttempts = 5;
        $resResult   = null;
        for ($i = 0; $i < $maxAttempts; $i++) {
            sleep(3);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.snov.io/v2/email-verification/result?task_hash=$hash");
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $resResult = json_decode(curl_exec($ch), true);
            curl_close($ch);

            // Si el status de la respuesta no es in_progress, salimos
            $topStatus = $resResult['status'] ?? '';
            if ($topStatus !== 'in_progress') {
                break;
            }

            error_log("[SNOV_POLL] Intento " . ($i + 1) . " aún in_progress para $email");
        }

        return ['success' => true, 'verification' => $resResult];
    }

    public function scrap(string $email)
    {
        try {
            $tokenRes = $this->getSnovToken();
            if (!$tokenRes['success']) return $tokenRes;
            $token = $tokenRes['access_token'];

            // 1. Enriquecer Perfil (v1) - access_token en el cuerpo según doc
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.snov.io/v1/get-profile-by-email");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'access_token' => $token,
                'email' => $email
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $profileData = json_decode(curl_exec($ch), true);
            curl_close($ch);

            // 2. Verificar Email (Incrustado para facilitar retorno combinado)
            $verification = $this->verifyEmail($email);

            return [
                'success' => true,
                'profile' => $profileData,
                'verification' => $verification['verification'] ?? null
            ];

        } catch (Exception $e) {
            error_log("[GEMINI_SERVICE_ERROR] " . $e->getMessage());
            return ['success' => false, 'message' => 'Excepción: ' . $e->getMessage()];
        }
    }
    public function generateResponse(string $prompt)
    {
        try {
            $response = $this->gemini->generativeModel(model: 'gemini-2.5-flash')->generateContent($prompt);
            $json = $response->text();
            return $this->cleanJSON($json);
        } catch (Exception $e) {
            error_log("[GEMINI_SERVICE_ERROR] " . $e->getMessage());
            throw $e;
        }
    }
    public function auditarRespuesta(array $contexto, string $mensajeUsuario, array $historial = [])
    {
        // 1. Construir el prompt con los parámetros de la meta
        $promptReglas = str_replace(
            ['{{meta_nombre}}', '{{meta_descripcion}}', '{{meta_objetivo}}', '{{udv_acumuladas}}', '{{valor_udv_meta}}'],
            [$contexto['meta'], $contexto['descripcion'], $contexto['objetivo'], $contexto['udv_otorgadas'], $contexto['valor_udv']],
            $this->prompt_maestro
        );

        // 2. Construir la estructura final segregada
        $fullPrompt = "### CONTEXTO DE UBICACIÓN (HISTORIAL PREVIO)\n";
        $fullPrompt .= "Este historial es solo para referenciar el hilo de la conversación. NO DEBE influir en la puntuación de la última respuesta.\n";
        
        if (empty($historial)) {
            $fullPrompt .= "Sin historial previo.\n";
        } else {
            foreach ($historial as $msg) {
                $role = ($msg['role'] === 'user' ? "Evolucionador" : "Nodo Validador");
                $fullPrompt .= "[{$role}]: {$msg['content']}\n";
            }
        }

        $fullPrompt .= "\n### REGLAS DE AUDITORÍA Y PROTOCOLO SRAA\n";
        $fullPrompt .= $promptReglas;

        $fullPrompt .= "\n\n### OBJETO DE EVALUACIÓN ACTUAL (CRÍTICO)\n";
        $fullPrompt .= "NUEVA RESPUESTA DEL EVOLUCIONADOR: \"{$mensajeUsuario}\"\n\n";
        $fullPrompt .= "INSTRUCCIÓN FINAL: Audita la 'NUEVA RESPUESTA' siguiendo estrictamente la EVALUACIÓN ATÓMICA. Si es irrelevante o sin sentido en relación al contexto, otorga 0.0 UDV.";

        return $this->generateResponse($fullPrompt);
    }
}
