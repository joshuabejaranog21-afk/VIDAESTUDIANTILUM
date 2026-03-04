<?php
class ImageHelper {
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private $maxSize = 5 * 1024 * 1024; // 5MB
    
    /**
     * Subir imagen con validaciones y redimensionamiento
     * @param array $file Array $_FILES del archivo
     * @param string $folder Carpeta de destino (ej: 'clubes', 'directivas')
     * @param string $prefix Prefijo para el nombre del archivo
     * @param int $maxWidth Ancho máximo (default: 800)
     * @param int $maxHeight Alto máximo (default: 800)
     * @return array ['success' => bool, 'message' => string, 'url' => string]
     */
    public function uploadImage($file, $folder, $prefix = '', $maxWidth = 800, $maxHeight = 800) {
        try {
            // Validar que se subió el archivo
            if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
                return ['success' => false, 'message' => 'No se seleccionó ningún archivo'];
            }
            
            // Validar errores de subida
            if ($file['error'] !== UPLOAD_ERR_OK) {
                return ['success' => false, 'message' => 'Error al subir el archivo: ' . $this->getUploadError($file['error'])];
            }
            
            // Validar tipo de archivo
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file['tmp_name']);
            finfo_close($finfo);
            
            if (!in_array($mimeType, $this->allowedTypes)) {
                return ['success' => false, 'message' => 'Tipo de archivo no permitido. Solo se permiten: JPG, PNG, GIF, WebP'];
            }
            
            // Validar tamaño
            if ($file['size'] > $this->maxSize) {
                return ['success' => false, 'message' => 'El archivo es muy grande. Máximo 5MB'];
            }
            
            // Crear directorio si no existe
            // Guardar TODO directamente en /uploads/ sin subcarpetas (como upload.php)
            $uploadDir = __DIR__ . "/../../uploads/";
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generar nombre único (incluir folder en el nombre del archivo)
            $extension = $this->getExtensionFromMime($mimeType);
            $filename = 'liga_' . $folder . '_' . $prefix . '_' . uniqid() . '_' . time() . '.' . $extension;
            $filepath = $uploadDir . $filename;
            
            // Procesar y redimensionar imagen
            if ($this->processImage($file['tmp_name'], $filepath, $mimeType, $maxWidth, $maxHeight)) {
                // Generar URL completa como lo hace upload.php
                $url_relativa = '/vida-estudiantil_Hithan/uploads/' . $filename;
                $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
                $host = $_SERVER['HTTP_HOST'];
                $url = $protocol . '://' . $host . $url_relativa;

                return ['success' => true, 'message' => 'Imagen subida exitosamente', 'url' => $url];
            } else {
                return ['success' => false, 'message' => 'Error al procesar la imagen'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error interno: ' . $e->getMessage()];
        }
    }
    
    /**
     * Procesar y redimensionar imagen
     */
    private function processImage($source, $destination, $mimeType, $maxWidth, $maxHeight) {
        // Crear imagen desde el archivo fuente
        switch ($mimeType) {
            case 'image/jpeg':
                $image = imagecreatefromjpeg($source);
                break;
            case 'image/png':
                $image = imagecreatefrompng($source);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($source);
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($source);
                break;
            default:
                return false;
        }
        
        if (!$image) {
            return false;
        }
        
        // Obtener dimensiones originales
        $originalWidth = imagesx($image);
        $originalHeight = imagesy($image);
        
        // Calcular nuevas dimensiones manteniendo proporción
        $ratio = min($maxWidth / $originalWidth, $maxHeight / $originalHeight);
        
        // Si la imagen ya es más pequeña, no redimensionar
        if ($ratio >= 1) {
            $newWidth = $originalWidth;
            $newHeight = $originalHeight;
        } else {
            $newWidth = round($originalWidth * $ratio);
            $newHeight = round($originalHeight * $ratio);
        }
        
        // Crear nueva imagen redimensionada
        $newImage = imagecreatetruecolor($newWidth, $newHeight);
        
        // Preservar transparencia para PNG y GIF
        if ($mimeType == 'image/png' || $mimeType == 'image/gif') {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }
        
        // Redimensionar
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
        
        // Guardar imagen procesada
        $result = false;
        switch ($mimeType) {
            case 'image/jpeg':
                $result = imagejpeg($newImage, $destination, 90);
                break;
            case 'image/png':
                $result = imagepng($newImage, $destination, 8);
                break;
            case 'image/gif':
                $result = imagegif($newImage, $destination);
                break;
            case 'image/webp':
                $result = imagewebp($newImage, $destination, 90);
                break;
        }
        
        // Limpiar memoria
        imagedestroy($image);
        imagedestroy($newImage);
        
        return $result;
    }
    
    /**
     * Obtener extensión desde MIME type
     */
    private function getExtensionFromMime($mimeType) {
        switch ($mimeType) {
            case 'image/jpeg':
                return 'jpg';
            case 'image/png':
                return 'png';
            case 'image/gif':
                return 'gif';
            case 'image/webp':
                return 'webp';
            default:
                return 'jpg';
        }
    }
    
    /**
     * Obtener mensaje de error de subida
     */
    private function getUploadError($errorCode) {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'El archivo es muy grande (límite del servidor)';
            case UPLOAD_ERR_FORM_SIZE:
                return 'El archivo es muy grande (límite del formulario)';
            case UPLOAD_ERR_PARTIAL:
                return 'El archivo se subió parcialmente';
            case UPLOAD_ERR_NO_FILE:
                return 'No se subió ningún archivo';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Falta carpeta temporal';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Error al escribir archivo';
            case UPLOAD_ERR_EXTENSION:
                return 'Extensión de archivo no permitida';
            default:
                return 'Error desconocido';
        }
    }
    
    /**
     * Eliminar imagen del servidor
     */
    public function deleteImage($url) {
        if (empty($url)) return false;

        // Convertir URL a ruta física
        // URL puede venir como:
        // - http://localhost:8888/vida-estudiantil_Hithan/uploads/clubes/imagen.png (URL completa)
        // - /vida-estudiantil_Hithan/uploads/clubes/imagen.png (URL relativa)
        // - /uploads/clubes/imagen.png (URL relativa corta)

        // Extraer solo la parte de uploads/...
        if (strpos($url, '/vida-estudiantil_Hithan/uploads/') !== false) {
            preg_match('/\/vida-estudiantil_Hithan\/(uploads\/.+)$/', $url, $matches);
            if (isset($matches[1])) {
                $relativePath = $matches[1];
            } else {
                return false;
            }
        } else {
            $relativePath = ltrim($url, '/');
        }

        // Desde /assets/php/ subir 2 niveles
        $fullPath = __DIR__ . '/../../' . $relativePath;

        if (file_exists($fullPath)) {
            return unlink($fullPath);
        }

        return false;
    }
}
?>