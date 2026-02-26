<?php

namespace App\Controllers;

use Database;
use Modulo;

class RegaloController
{
    private $db;
    private $modulo;

    public function __construct()
    {
        $this->db = new Database();
        $this->modulo = new Modulo();
        $this->modulo->ensureTarjetasRegaloTable();
    }

    public function generar()
    {
        // View for sender to create the gift card
        require_once __DIR__ . '/../../views/regalo/generar.php';
    }

    public function guardar()
    {
        // Endpoint to handle the creation from the form
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['status' => false, 'message' => 'Método no permitido']);
            return;
        }
        
        $sender_email = $_POST['sender_email'] ?? '';
        $mensaje = $_POST['mensaje'] ?? '';
        $monto_cobrar = $_POST['monto_cobrar'] ?? 0;
        $destinatario_email = $_POST['destinatario_email'] ?? '';

        if (empty($sender_email) || empty($destinatario_email) || $monto_cobrar <= 0) {
            echo json_encode(['status' => false, 'message' => 'Datos incompletos o inválidos']);
            return;
        }

        $codigo = $this->generarCodigoUnico();
        
        $sql = "INSERT INTO tarjetas_regalo (sender_email, mensaje, monto_cobrar, destinatario_email, codigo) 
                VALUES (?, ?, ?, ?, ?)";
        
        $result = $this->db->sqlconector($sql, [$sender_email, $mensaje, $monto_cobrar, $destinatario_email, $codigo]);
        
        if ($result) {
            $link = base_url("regalo?code=" . $codigo);
            echo json_encode([
                'status' => true, 
                'message' => 'Tarjeta generada exitosamente.',
                'link' => $link,
                'codigo' => $codigo
            ]);
        } else {
            echo json_encode(['status' => false, 'message' => 'Error al guardar la tarjeta en la base de datos.']);
        }
    }

    public function ver()
    {
        $codigo = $_GET['code'] ?? null;
        if (!$codigo) {
            die("Código no proporcionado.");
        }

        $tarjeta = $this->db->row_sqlconector("SELECT * FROM tarjetas_regalo WHERE codigo = ?", [$codigo]);

        if (!$tarjeta) {
            die("Tarjeta de regalo no encontrada.");
        }

        // Si ya está reclamada, we show the view anyway but the view logic will disable the "Inicia Tu Evolución" button
        // Spectacular view for the recipient
        require_once __DIR__ . '/../../views/regalo/ver.php';
    }

    public function registro()
    {
        $codigo = $_GET['code'] ?? null;
        if (!$codigo) {
            die("Código no proporcionado.");
        }

        $tarjeta = $this->db->row_sqlconector("SELECT * FROM tarjetas_regalo WHERE codigo = ?", [$codigo]);

        if (!$tarjeta) {
            die("Tarjeta de regalo no encontrada.");
        }

        if ($tarjeta['estatus'] === 'Reclamada') {
            die("Esta tarjeta de regalo ya ha sido reclamada. El enlace ya no es válido por motivos de seguridad.");
        }

        // Registration form view
        require_once __DIR__ . '/../../views/regalo/registro.php';
    }

    public function pago()
    {
        $codigo = $_GET['code'] ?? null;
        if (!$codigo) {
            die("Código no proporcionado.");
        }

        $tarjeta = $this->db->row_sqlconector("SELECT * FROM tarjetas_regalo WHERE codigo = ?", [$codigo]);

        if (!$tarjeta) {
            die("Tarjeta de regalo no encontrada.");
        }

        if ($tarjeta['estatus'] === 'Reclamada') {
            die("Esta tarjeta de regalo ya ha sido reclamada. El enlace ya no es válido por motivos de seguridad.");
        }

        // Checkout view matching the gift card details
        require_once __DIR__ . '/../../views/regalo/pago.php';
    }

    private function generarCodigoUnico()
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $codigo = '';
        for ($i = 0; $i < 16; $i++) {
            $codigo .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }
        
        // Formato util X0X0-X0X0-X0X0-X0X0
        $codigo_formateado = substr($codigo, 0, 4) . '-' . substr($codigo, 4, 4) . '-' . substr($codigo, 8, 4) . '-' . substr($codigo, 12, 4);
        return $codigo_formateado;
    }
}
