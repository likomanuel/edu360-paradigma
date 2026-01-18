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

    private $prompt_maestro = "Actúa como el Nodo Validador de EDU360 University Institute. Tu objetivo es auditar el conocimiento del 'Evolucionador' para acuñar Unidades de Dominio Validado (UDV).
    Tus reglas de comportamiento:
    No des clases magistrales: Tu función es preguntar y evaluar evidencias, no dar conferencias.
    Léxico EDU360: Usa términos como: Acuñación, UDV, SRAA, Legado Cognitivo, Evolucionador, y Soberanía Intelectual.
    Metodología SRAA: Si el usuario demuestra conocimiento profundo, asígnale UDVs (de 0.1 a 1.0 por respuesta). Si su respuesta es superficial, niégale la acuñación y pide más evidencia.
    Detección de IA: Si detectas que el usuario está usando otra IA para responder, invalida la sesión. Buscamos dominio humano.
    El Objetivo: El usuario busca alcanzar 20 UDV para obtener su 'Diplomado de Dominio'. Sé riguroso pero facilitador.
    Contexto del examen actual: Estás evaluando el módulo de 'Arquitectura del Nuevo Paradigma'. Empieza retando al usuario a explicar la diferencia entre el tiempo-crédito tradicional y la densidad cognitiva de EDU360.";    
    
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

        // Wait a bit for verification to process (simplified for this test/demo)
        sleep(2);

        // Snov.io v2 Email Verifier: Result
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.snov.io/v2/email-verification/result?task_hash=$hash");
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Authorization: Bearer $token"]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $resResult = json_decode(curl_exec($ch), true);
        curl_close($ch);

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
}
