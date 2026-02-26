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
    public function ifUsuarioExist($usuario):bool
    {
        $result = $this->db->row_sqlconector("select COUNT(*) AS TOTAL from evolucionadores where email_verificado='$usuario'");
        if (!$result) {
            error_log("\nError: Fallo en consulta ifUsuarioExist para usuario: " . $usuario, 3, self::LOG_PATH);
            return false;
        }
        return ($result['TOTAL'] == 1);
    }

    public function ifStagingUsuarioExist($usuario):bool
    {
        $result = $this->db->row_sqlconector("select COUNT(*) AS TOTAL from staging where usuario='$usuario'");
        if (!$result) {
            error_log("\nError: Fallo en consulta ifUsuarioExist para usuario: " . $usuario, 3, self::LOG_PATH);
            return false;
        }
        return ($result['TOTAL'] == 1);
    }

    public function getUser($usuario):array
    {
        $result = $this->db->row_sqlconector("select * from evolucionadores where email_verificado='$usuario'");
        if (!$result) {
            error_log("\nError: Fallo en consulta getUser para usuario: " . $usuario, 3, self::LOG_PATH);
            return [];
        }
        return $result;
    }

    public function updateUserPhoto($email, $photoFileName): bool
    {
        $sql = "UPDATE evolucionadores SET foto = '$photoFileName' WHERE email_verificado = '$email'";
        $result = $this->db->sqlconector($sql);
        if (!$result) {
            error_log("\nError: Fallo al actualizar foto para usuario: " . $email, 3, self::LOG_PATH);
            return false;
        }
        return true;
    }
    
    public function createUser($email, $password, $hash, $nombre_completo, $estatus_soberania): bool
    {
        // Encrypt the password before storing
        $encryptedPassword = $this->encryptApiKey($password, self::ENCRYPTION_KEY);
        
        // Define the base path for user folders (adjust this path according to your hosting structure)
        $basePath = __DIR__ . '/../public/users'; // You can change this to your desired location
        
        // Create the main hash folder
        $hashFolderPath = $basePath . '/' . $hash;
        
        // Create the folder structure
        if (!file_exists($hashFolderPath)) {
            if (!mkdir($hashFolderPath, 0777, true)) {
                error_log("\nError: No se pudo crear la carpeta principal: " . $hashFolderPath, 3, self::LOG_PATH);
                return false;
            }
            chmod($hashFolderPath, 0777);
        }
        
        // Create subdirectories: perfil, certificados, logros
        $subdirectories = ['perfil', 'certificados', 'logros'];
        
        foreach ($subdirectories as $subdir) {
            $subdirPath = $hashFolderPath . '/' . $subdir;
            
            if (!file_exists($subdirPath)) {
                if (!mkdir($subdirPath, 0777, true)) {
                    error_log("\nError: No se pudo crear la subcarpeta: " . $subdirPath, 3, self::LOG_PATH);
                    return false;
                }
                chmod($subdirPath, 0777);
            }
        }
        
        // Prepare the SQL insert statement
        $sql = "INSERT INTO evolucionadores (email_verificado, password, hash_identidad, nombre_completo, estatus_soberania) 
                VALUES ('$email', '$encryptedPassword', '$hash', '$nombre_completo', '$estatus_soberania')";
        
        // Execute the insert
        $result = $this->db->sqlconector($sql);
        
        if (!$result) {
            error_log("\nError: Fallo al crear usuario: " . $email, 3, self::LOG_PATH);
            return false;
        }
        
        error_log("\nUsuario creado exitosamente: " . $email . " | Carpeta: " . $hashFolderPath, 3, self::LOG_PATH);
        return true;
    }

    public function getPassword($usuario):string
    {        
        $row = $this->db->row_sqlconector("select password from evolucionadores where email_verificado='$usuario'");
        if (!$row || !isset($row['password'])) {
            error_log("\nError: No se encontró contraseña para el usuario: " . $usuario, 3, self::LOG_PATH);
            return "";
        }
        
        $encryptedData = trim($row['password']);
        $decrypted = $this->decryptApiKey($encryptedData, self::ENCRYPTION_KEY);        
        if ($decrypted === false || $decrypted === null) {
            error_log("\nError: Fallo la desencriptación para el usuario: " . $usuario . " | Data length: " . strlen($encryptedData), 3, self::LOG_PATH);
            return "";
        }
        
        return $decrypted;
    }

    public function getStagingPassword($usuario):string
    {        
        $row = $this->db->row_sqlconector("select password from staging where usuario='$usuario'");
        if (!$row || !isset($row['password'])) {
            error_log("\nError: No se encontró contraseña para el usuario: " . $usuario, 3, self::LOG_PATH);
            return "";
        }
        
        $encryptedData = trim($row['password']);
        $decrypted = $this->decryptApiKey($encryptedData, self::ENCRYPTION_KEY);        
        if ($decrypted === false || $decrypted === null) {
            error_log("\nError: Fallo la desencriptación para el usuario: " . $usuario . " | Data length: " . strlen($encryptedData), 3, self::LOG_PATH);
            return "";
        }
        
        return $decrypted;
    }


    public function encryptApiKey($apiKey, $encryptionKey): string
    {
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = openssl_random_pseudo_bytes($ivLength);
        // Usamos OPENSSL_RAW_DATA para obtener binario directamente y evitar el "doble base64"
        $encrypted = openssl_encrypt($apiKey, 'aes-256-cbc', $encryptionKey, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $encrypted);
    }

    public function decryptApiKey($encryptedApiKey, $encryptionKey)
    {
        $data = base64_decode($encryptedApiKey);
        if ($data === false) {
            return null;
        }

        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        if (strlen($data) <= $ivLength) {
            return null;
        }

        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);

        // Usamos OPENSSL_RAW_DATA porque ahora $encrypted es binario puro
        $result = openssl_decrypt($encrypted, 'aes-256-cbc', $encryptionKey, OPENSSL_RAW_DATA, $iv);
        return $result;
    }

    /**
     * Verifica si la tabla nodos_activos existe, si no existe la crea
     * @return bool True si la tabla existe o fue creada exitosamente
     */
    public function ensureNodosActivosTable(): bool
    {
        try {
            // Verificar si la tabla existe
            $checkTable = $this->db->row_sqlconector(
                "SELECT COUNT(*) as count 
                 FROM information_schema.tables 
                 WHERE table_schema = DATABASE() 
                 AND table_name = 'nodos_activos'"
            );

            if ($checkTable && $checkTable['count'] > 0) {
                // La tabla ya existe
                error_log("\nTabla 'nodos_activos' ya existe en la base de datos.", 3, self::LOG_PATH);
                return true;
            }

            // La tabla no existe, crearla
            $createTableSQL = "
                CREATE TABLE `nodos_activos` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `id_evolucionador` INT(11) NOT NULL,
                    `stripe_session_id` VARCHAR(255) NOT NULL,
                    `monto` DECIMAL(10,2) NOT NULL,
                    `estatus` VARCHAR(50) NOT NULL DEFAULT 'Activado',
                    `tipo_nodo` VARCHAR(50) NOT NULL DEFAULT 'Omega',
                    `fecha_activacion` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    `fecha_expiracion` TIMESTAMP NULL DEFAULT NULL,
                    `id_artefacto` INT(11) DEFAULT 1,
                    `certificado_generado` TINYINT(1) NOT NULL DEFAULT 0,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `unique_session` (`stripe_session_id`),
                    KEY `idx_evolucionador` (`id_evolucionador`),
                    CONSTRAINT `fk_nodos_evolucionador` 
                        FOREIGN KEY (`id_evolucionador`) 
                        REFERENCES `evolucionadores` (`id_evolucionador`) 
                        ON DELETE CASCADE 
                        ON UPDATE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";

            $result = $this->db->sqlconector($createTableSQL);

            if ($result) {
                error_log("\nTabla 'nodos_activos' creada exitosamente.", 3, self::LOG_PATH);
                return true;
            } else {
                error_log("\nError: No se pudo crear la tabla 'nodos_activos'.", 3, self::LOG_PATH);
                return false;
            }

        } catch (Exception $e) {
            error_log("\nError al verificar/crear tabla 'nodos_activos': " . $e->getMessage(), 3, self::LOG_PATH);
            return false;
        }
    }

    /**
     * Verifica si la tabla LINKS existe, si no existe la crea
     * @return bool True si la tabla existe o fue creada exitosamente
     */
    public function ensureLinksTable(): bool
    {
        try {
            // Verificar si la tabla existe
            $checkTable = $this->db->row_sqlconector(
                "SELECT COUNT(*) as count 
                 FROM information_schema.tables 
                 WHERE table_schema = DATABASE() 
                 AND table_name = 'LINKS'"
            );

            if ($checkTable && $checkTable['count'] > 0) {
                // La tabla ya existe
                return true;
            }

            // La tabla no existe, crearla
            $createTableSQL = "
                CREATE TABLE IF NOT EXISTS `LINKS` (
                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                  `FECHA` timestamp NOT NULL DEFAULT current_timestamp(),
                  `LINK` varchar(255) DEFAULT NULL,
                  `CORREO` varchar(255) DEFAULT NULL,
                  `BLOQUEADO` int(11) NOT NULL DEFAULT 0,
                  PRIMARY KEY (`ID`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";

            $result = $this->db->sqlconector($createTableSQL);

            if ($result) {
                error_log("\nTabla 'LINKS' creada exitosamente.", 3, self::LOG_PATH);
                return true;
            } else {
                error_log("\nError: No se pudo crear la tabla 'LINKS'.", 3, self::LOG_PATH);
                return false;
            }

        } catch (Exception $e) {
            error_log("\nError al verificar/crear tabla 'LINKS': " . $e->getMessage(), 3, self::LOG_PATH);
            return false;
        }
    }

    /**
     * Verifica y añade las columnas de perfil (ciudad, profesion, empresa, red_social)
     * a la tabla evolucionadores si no existen (auto-migración).
     * @return bool True si las columnas existen o fueron creadas exitosamente
     */
    public function ensureEvolucionadoresProfileCols(): bool
    {
        $columns = [
            'ciudad'     => "ALTER TABLE `evolucionadores` ADD COLUMN `ciudad` VARCHAR(150) NULL DEFAULT NULL",
            'profesion'  => "ALTER TABLE `evolucionadores` ADD COLUMN `profesion` VARCHAR(150) NULL DEFAULT NULL",
            'empresa'    => "ALTER TABLE `evolucionadores` ADD COLUMN `empresa` VARCHAR(150) NULL DEFAULT NULL",
            'red_social' => "ALTER TABLE `evolucionadores` ADD COLUMN `red_social` VARCHAR(255) NULL DEFAULT NULL",
        ];

        try {
            foreach ($columns as $colName => $alterSql) {
                $exists = $this->db->row_sqlconector(
                    "SELECT COUNT(*) as count
                     FROM information_schema.columns
                     WHERE table_schema = DATABASE()
                       AND table_name = 'evolucionadores'
                       AND column_name = '$colName'"
                );

                if (!$exists || $exists['count'] == 0) {
                    $this->db->sqlconector($alterSql);
                    error_log("\nColumna '$colName' añadida a la tabla 'evolucionadores'.", 3, self::LOG_PATH);
                }
            }
            return true;
        } catch (Exception $e) {
            error_log("\nError en ensureEvolucionadoresProfileCols: " . $e->getMessage(), 3, self::LOG_PATH);
            return false;
        }
    }

    /**
     * Verifica si la tabla tarjetas_regalo existe, si no existe la crea
     * @return bool True si la tabla existe o fue creada exitosamente
     */
    public function ensureTarjetasRegaloTable(): bool
    {
        try {
            $checkTable = $this->db->row_sqlconector(
                "SELECT COUNT(*) as count 
                 FROM information_schema.tables 
                 WHERE table_schema = DATABASE() 
                 AND table_name = 'tarjetas_regalo'"
            );

            if ($checkTable && $checkTable['count'] > 0) {
                return true;
            }

            $createTableSQL = "
                CREATE TABLE IF NOT EXISTS `tarjetas_regalo` (
                  `id` int(11) NOT NULL AUTO_INCREMENT,
                  `sender_email` varchar(150) NOT NULL,
                  `mensaje` text DEFAULT NULL,
                  `monto_cobrar` decimal(10,2) NOT NULL,
                  `destinatario_email` varchar(150) NOT NULL,
                  `codigo` varchar(50) NOT NULL,
                  `estatus` varchar(50) NOT NULL DEFAULT 'Pendiente',
                  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
                  PRIMARY KEY (`id`),
                  UNIQUE KEY `unique_codigo` (`codigo`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ";

            $result = $this->db->sqlconector($createTableSQL);

            if ($result) {
                error_log("\nTabla 'tarjetas_regalo' creada exitosamente.", 3, self::LOG_PATH);
                return true;
            } else {
                error_log("\nError: No se pudo crear la tabla 'tarjetas_regalo'.", 3, self::LOG_PATH);
                return false;
            }

        } catch (Exception $e) {
            error_log("\nError al verificar/crear tabla 'tarjetas_regalo': " . $e->getMessage(), 3, self::LOG_PATH);
            return false;
        }
    }

}