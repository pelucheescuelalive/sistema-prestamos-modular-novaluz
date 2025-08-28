<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Método no permitido']);
    exit;
}

try {
    // Verificar si se subió un archivo
    if (!isset($_FILES['foto']) || $_FILES['foto']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('No se subió ningún archivo o hubo un error');
    }

    $archivo = $_FILES['foto'];
    $clienteId = $_POST['cliente_id'] ?? null;

    if (!$clienteId) {
        throw new Exception('ID de cliente requerido');
    }

    // Validar tipo de archivo
    $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($archivo['type'], $tiposPermitidos)) {
        throw new Exception('Tipo de archivo no permitido. Solo se permiten: JPG, PNG, GIF, WEBP');
    }

    // Validar tamaño (máximo 5MB)
    $maxSize = 5 * 1024 * 1024; // 5MB
    if ($archivo['size'] > $maxSize) {
        throw new Exception('El archivo es demasiado grande. Máximo 5MB');
    }

    // Crear directorio si no existe
    $directorioUploads = __DIR__ . '/uploads/';
    if (!is_dir($directorioUploads)) {
        mkdir($directorioUploads, 0755, true);
    }

    // Generar nombre único para el archivo
    $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
    $nombreArchivo = 'cliente_' . $clienteId . '_' . time() . '.' . $extension;
    $rutaDestino = $directorioUploads . $nombreArchivo;

    // Mover archivo
    if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
        throw new Exception('Error al guardar el archivo');
    }

    // Redimensionar imagen para optimizar
    $imagenRedimensionada = redimensionarImagen($rutaDestino, 300, 300);
    if ($imagenRedimensionada) {
        // Guardar imagen redimensionada
        file_put_contents($rutaDestino, $imagenRedimensionada);
    }

    // Devolver respuesta exitosa
    echo json_encode([
        'success' => true,
        'mensaje' => 'Foto subida correctamente',
        'archivo' => $nombreArchivo,
        'url' => 'uploads/' . $nombreArchivo
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function redimensionarImagen($rutaArchivo, $anchoMax, $altoMax) {
    try {
        $info = getimagesize($rutaArchivo);
        if (!$info) return false;

        $tipoMime = $info['mime'];
        
        // Crear imagen desde archivo
        switch ($tipoMime) {
            case 'image/jpeg':
                $imagen = imagecreatefromjpeg($rutaArchivo);
                break;
            case 'image/png':
                $imagen = imagecreatefrompng($rutaArchivo);
                break;
            case 'image/gif':
                $imagen = imagecreatefromgif($rutaArchivo);
                break;
            case 'image/webp':
                $imagen = imagecreatefromwebp($rutaArchivo);
                break;
            default:
                return false;
        }

        if (!$imagen) return false;

        $anchoOriginal = imagesx($imagen);
        $altoOriginal = imagesy($imagen);

        // Calcular nuevas dimensiones manteniendo proporción
        $ratio = min($anchoMax / $anchoOriginal, $altoMax / $altoOriginal);
        $nuevoAncho = round($anchoOriginal * $ratio);
        $nuevoAlto = round($altoOriginal * $ratio);

        // Crear nueva imagen redimensionada
        $imagenRedimensionada = imagecreatetruecolor($nuevoAncho, $nuevoAlto);
        
        // Preservar transparencia para PNG y GIF
        if ($tipoMime == 'image/png' || $tipoMime == 'image/gif') {
            imagealphablending($imagenRedimensionada, false);
            imagesavealpha($imagenRedimensionada, true);
            $transparente = imagecolorallocatealpha($imagenRedimensionada, 255, 255, 255, 127);
            imagefill($imagenRedimensionada, 0, 0, $transparente);
        }

        // Redimensionar
        imagecopyresampled(
            $imagenRedimensionada, $imagen,
            0, 0, 0, 0,
            $nuevoAncho, $nuevoAlto,
            $anchoOriginal, $altoOriginal
        );

        // Capturar output buffer
        ob_start();
        switch ($tipoMime) {
            case 'image/jpeg':
                imagejpeg($imagenRedimensionada, null, 90);
                break;
            case 'image/png':
                imagepng($imagenRedimensionada);
                break;
            case 'image/gif':
                imagegif($imagenRedimensionada);
                break;
            case 'image/webp':
                imagewebp($imagenRedimensionada, null, 90);
                break;
        }
        $contenido = ob_get_contents();
        ob_end_clean();

        // Limpiar memoria
        imagedestroy($imagen);
        imagedestroy($imagenRedimensionada);

        return $contenido;

    } catch (Exception $e) {
        return false;
    }
}
?>
