<?php
namespace App\Controllers;

use Database;

class VerificarController
{
    private $db;

    public function __construct()
    {
        $this->db = new Database();
    }

    public function index()
    {
        $titulo = "Verificar Certificados - EDU360";
        require_once __DIR__ . '/../../views/verificar/verificar.php';
    }

    public function buscar()
    {
        $search = $_GET['q'] ?? '';
        $user = null;
        $certificates = [];

        if (!empty($search)) {
            // Buscar por hash_identidad o email_verificado
            $sql = "SELECT id_evolucionador, hash_identidad, nombre_completo, email_verificado 
                    FROM evolucionadores 
                    WHERE hash_identidad = '$search' OR email_verificado = '$search' 
                    LIMIT 1";
            $user = $this->db->row_sqlconector($sql);

            if ($user) {
                $hash = $user['hash_identidad'];
                $dir = __DIR__ . "/../../public/users/{$hash}/certificados/";
                
                if (is_dir($dir)) {
                    $files = scandir($dir);
                    foreach ($files as $file) {
                        if ($file !== '.' && $file !== '..' && str_ends_with($file, '.png')) {
                            $certificates[] = [
                                'name' => $file,
                                'url' => base_url("public/users/{$hash}/certificados/{$file}"),
                                'date' => date("Y-m-d H:i:s", filemtime($dir . $file))
                            ];
                        }
                    }
                }
            }
        }

        $titulo = "Certificados de " . ($user['nombre_completo'] ?? 'Usuario');
        require_once __DIR__ . '/../../views/verificar/certificados.php';
    }
}
