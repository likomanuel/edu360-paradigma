<?php
session_start();
require_once __DIR__ . '/../config/modulo.php';

if (!isset($_SESSION['email'])) {
    die(json_encode(['success' => false, 'message' => 'Sesión no iniciada']));
}

$modulo = new Modulo();
$user = $modulo->getUser($_SESSION['email']);

if (!$user) {
    die(json_encode(['success' => false, 'message' => 'Usuario no encontrado']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $file = $_FILES['foto'];
    
    // Validaciones básicas
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
    $fileInfo = pathinfo($file['name']);
    $extension = strtolower($fileInfo['extension']);
    
    if (!in_array($extension, $allowedExtensions)) {
        die(json_encode(['success' => false, 'message' => 'Formato no permitido']));
    }
    
    // Ruta de destino (ahora dentro de public)
    $hash = $user['hash_identidad'];
    $targetDir = __DIR__ . "/users/$hash/perfil/";
    
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $newFileName = "perfil_" . time() . "." . $extension;
    $targetPath = $targetDir . $newFileName;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Borrar foto anterior si existe
        if (!empty($user['foto']) && file_exists($targetDir . $user['foto'])) {
            unlink($targetDir . $user['foto']);
        }
        
        // Actualizar BD
        if ($modulo->updateUserPhoto($user['email_verificado'], $newFileName)) {
            echo json_encode(['success' => true, 'foto' => $newFileName]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar base de datos']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al mover el archivo']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
}
