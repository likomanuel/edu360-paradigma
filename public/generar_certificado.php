<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/helpers.php';
require_once(__DIR__ . '/../bin/phpqrcode/qrlib.php');

function generarCertificadoEdu360($nombreAlumno, $hashTransaccion, $emailAlumno, $guardarEnDisco = false) {
    // 1. Configuración de archivos
    $rutaPlantilla = img('edu360_template.png');
    $rutaFuente = fonts('Orbitron-Bold.ttf');
    $archivoQR = 'temp_qr.png';
    
    // 2. Crear el QR con el email
    // QR_ECLEVEL_L (Calidad), 4 (Tamaño), 2 (Margen)
    QRcode::png($emailAlumno, $archivoQR, QR_ECLEVEL_L, 4, 2);

    // 3. Cargar la imagen base
    $img = imagecreatefrompng($rutaPlantilla);
    
    // 4. Definir colores (RGB)
    $colorTexto = imagecolorallocate($img, 255, 255, 255); // Blanco puro
    $colorCian = imagecolorallocate($img, 0, 255, 255);  // Cian neón para el Hash

    // 5. Insertar el NOMBRE del Alumno
    // imagettftext($imagen, $tamaño, $angulo, $x, $y, $color, $fuente, $texto)
    imagettftext($img, 28, 0, 480, 740, $colorTexto, $rutaFuente, strtoupper($nombreAlumno));

    // 6. Insertar el HASH de validación
    imagettftext($img, 18, 0, 360, 885, $colorCian, $rutaFuente, $hashTransaccion);

    // 7. Superponer el código QR
    $qrImg = imagecreatefrompng($archivoQR);
    $qrSize = getimagesize($archivoQR);
    
    // imagecopy(fondo, qr, destino_x, destino_y, src_x, src_y, ancho, alto)
    // Coordenadas para el cuadro derecho del QR
    imagecopy($img, $qrImg, 720, 420, 0, 0, $qrSize[0], $qrSize[1]);

    // 8. Salida del certificado
    if ($guardarEnDisco) {
        // Si $guardarEnDisco es un string, lo usamos como ruta completa
        // Si es booleano true, usamos la ruta por defecto
        $rutaSalida = is_string($guardarEnDisco) ? $guardarEnDisco : __DIR__ . "/../certificados/{$hashTransaccion}.png";
        
        // Asegurar que el directorio existe
        $directorio = dirname($rutaSalida);
        if (!file_exists($directorio)) {
            mkdir($directorio, 0777, true);
        }

        imagepng($img, $rutaSalida); // guarda en disco
        return $rutaSalida;
    } else {
        header('Content-Type: image/png');
        imagepng($img); // envía al navegador
    }
    
    // Limpieza de memoria
    unset($img);
    unset($qrImg);
    unlink($archivoQR); // Borra el QR temporal
}

// Ejemplo de uso:
// generarCertificadoEdu360("JUAN PEREZ", "0x842e1a9...c72b", "alumno@edu360.com");

?>