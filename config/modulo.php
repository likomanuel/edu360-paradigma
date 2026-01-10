<?php
include_once 'DataBase.php';
class Modulo
{
    const LOG_PATH       = __DIR__ . '/modulo.log';
    const DATA_PATH      = __DIR__ . '/modulo.json';
    const STATUS_PATH    = __DIR__ . '/modulo.status';
    const ENCRYPTION_KEY = "dd77b701661c5b55";

    private $db;
    public function __construct()
    {
        $this->db = new Database();
    }

    public function getDb()
    {
        return $this->db;
    }

    public function readJsonFile(string $filePath): ?array
    {
        // 1. Verificar si el archivo existe y es legible
        if (!file_exists($filePath)) {
            error_log("\nError: El archivo no existe en la ruta: " . $filePath, 3, self::LOG_PATH);
            return null;
        }

        if (!is_readable($filePath)) {
            error_log("\nError: El archivo no es legible debido a problemas de permisos: " . $filePath, 3, self::LOG_PATH);
            return null;
        }

        // 2. Leer el contenido del archivo
        $jsonContent = file_get_contents($filePath);

        if ($jsonContent === false) {
            error_log("\nError: No se pudo leer el contenido del archivo: " . $filePath, 3, self::LOG_PATH);
            return null;
        }

        // 3. Decodificar la cadena JSON a un array PHP
        // true en el segundo parámetro convierte el JSON en un array asociativo
        $data = json_decode($jsonContent, true);

        // 4. Verificar si la decodificación fue exitosa
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("\nError al decodificar el JSON del archivo '{$filePath}': " . json_last_error_msg(), 3, self::LOG_PATH);
            return null;
        }

            return $data;
    }

    public function createOrReplaceJsonFile(string $filePath, array $data): bool
    {
        $jsonContent = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (file_put_contents($filePath, $jsonContent) === false) {
            error_log("\nError: No se pudo escribir en el archivo: " . $filePath, 3, self::LOG_PATH);
            return false;
        }
        return true;
    }
    public function enviarPost($url, $datos)
    {
    // Inicializar cURL
    $ch = curl_init($url);

    // Convertir los datos a formato JSON
    $payload = json_encode($datos);

    // Configurar las opciones de cURL
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($payload)
    ]);

    // Ejecutar la solicitud y obtener la respuesta
    $respuesta = curl_exec($ch);
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        error_log("\nError en la solicitud POST a {$url}: {$error_msg}", 3, self::LOG_PATH);
        return 'Curl error: ' . $error_msg;
    }
    return $respuesta;
    }

    public function enviarGet($baseUrl, $datos)
    {
    // Convierte el array de datos en una cadena de consulta (ej: "tema=petroleo&limite=5")
    $queryString = http_build_query($datos);

    // Construye la URL completa con los parámetros
    $urlCompleta = $baseUrl . '?' . $queryString;

    // Inicializar cURL con la URL completa
    $ch = curl_init($urlCompleta);

    // Configurar la opción para que cURL devuelva el resultado como una cadena
    // en lugar de imprimirlo directamente.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // Ejecutar la solicitud y obtener la respuesta
    $respuesta = curl_exec($ch);

    return $respuesta;
    }

    public function latinFecha($fecha)
    {
    $date = date_create($fecha);
    return date_format($date, "d/M h:ia");
    }
    public function ifUsuarioExist($usuario)
    {
    if ($this->db->row_sqlconector("select COUNT(*) AS TOTAL from staging where usuario='$usuario'")['TOTAL'] == 1)
        return TRUE;
    return FALSE;
    }

    public function getPassword($usuario)
    {
    return $this->decryptApiKey($this->db->row_sqlconector("select password from staging where usuario='$usuario'")['password'],self::ENCRYPTION_KEY);
    }

    public  function encryptApiKey($apiKey, $encryptionKey)
    {
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = openssl_encrypt($apiKey, 'aes-256-cbc', $encryptionKey, 0, $iv);
    return base64_encode($iv . $encrypted);
    }

    public function decryptApiKey($encryptedApiKey, $encryptionKey)
    {
    $data = base64_decode($encryptedApiKey);
    $iv = substr($data, 0, openssl_cipher_iv_length('aes-256-cbc'));
    $encrypted = substr($data, openssl_cipher_iv_length('aes-256-cbc'));
    return openssl_decrypt($encrypted, 'aes-256-cbc', $encryptionKey, 0, $iv);
    }

}