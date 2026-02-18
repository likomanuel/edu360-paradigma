<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/helpers.php';
require_once(__DIR__ . '/../bin/phpqrcode/qrlib.php');

function generarCertificadoEdu360($nombreAlumno, $nombreCertificado, $hashTransaccion, $qrContent, $rutaDestino, $guardarEnDisco = true) {
    // 1. Configuración de archivos
    $rutaPlantilla = img_path('edu360_template.png');
    $rutaFuente = fonts_path('orbitron-bold.otf');
    
    // Validar existencia de archivos críticos
    if (!file_exists($rutaPlantilla)) {
        error_log("Error: Plantilla no encontrada en $rutaPlantilla");
        return false;
    }
    if (!file_exists($rutaFuente)) {
        error_log("Error: Fuente no encontrada en $rutaFuente");
        return false;
    }

    // El archivo QR se crea temporalmente en el mismo directorio que el certificado
    $archivoQR = dirname($rutaDestino) . '/temp_qr_' . time() . '.png';
    
    // Asegurar que el directorio existe para poder guardar el QR temporal
    $directorio = dirname($rutaDestino);
    if (!file_exists($directorio)) {
        mkdir($directorio, 0777, true);
    }
    
    // 2. Crear el QR con el contenido proporcionado (URL o Email)
    QRcode::png($qrContent, $archivoQR, QR_ECLEVEL_L, 4, 2);

    // 3. Cargar la imagen base
    $img = @imagecreatefrompng($rutaPlantilla);
    if (!$img) {
        error_log("Error: No se pudo crear la imagen desde la plantilla $rutaPlantilla");
        if (file_exists($archivoQR)) unlink($archivoQR);
        return false;
    }
    
    // 4. Definir colores (RGB)
    $colorTexto = imagecolorallocate($img, 255, 255, 255); // Blanco puro
    $colorCian = imagecolorallocate($img, 0, 255, 255);  // Cian neón para el Hash

    if ($colorTexto === false || $colorCian === false) {
        error_log("Error: No se pudo asignar colores a la imagen");
        imagedestroy($img);
        if (file_exists($archivoQR)) unlink($archivoQR);
        return false;
    }

    // 5. Insertar el NOMBRE del Alumno
    imagettftext($img, 28, 0, 480, 580, $colorTexto, $rutaFuente, strtoupper($nombreAlumno));

    // 5.1 Insertar el NOMBRE del Certificado (Módulo/Diplomado)
    imagettftext($img, 20, 0, 380, 650, $colorCian, $rutaFuente, strtoupper($nombreCertificado));

    // 6. Insertar el HASH de validación
    imagettftext($img, 18, 0, 460, 700, $colorCian, $rutaFuente, $hashTransaccion);

    // 7. Superponer el código QR
    if (file_exists($archivoQR)) {
        $qrImg = @imagecreatefrompng($archivoQR);
        if ($qrImg) {
            $qrSize = getimagesize($archivoQR);
            imagecopy($img, $qrImg, 980, 450, 0, 0, $qrSize[0], $qrSize[1]);
            imagedestroy($qrImg);
        }
        unlink($archivoQR); // Borra el QR temporal inmediatamente después de usarlo
    }

    // 8. Salida del certificado
    if ($guardarEnDisco) {
        $success = imagepng($img, $rutaDestino);
        imagedestroy($img);
        return $success ? $rutaDestino : false;
    } else {
        header('Content-Type: image/png');
        imagepng($img);
        imagedestroy($img);
    }
}

//prueba    
// Si se corre por CLI, podemos probar directamente
if (php_sapi_name() === 'cli') {
    $resultado = generarCertificadoEdu360('Juan Pérez', 'Diplomado en Marketing Digital', 'HASH123', 'juan@example.com', __DIR__ . '/certificado.png', true);
    if ($resultado) {
        echo "Certificado generado con éxito en: $resultado\n";
    } else {
        echo "Error al generar el certificado. Revisa php_errors.log\n";
    }
}
?>
