<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/upload_errors.log');

include("../db.php");
header('Content-Type: application/json');

// Verificar que hay sesión iniciada
if (!security()) {
    echo json_encode(['success' => 0, 'message' => 'Sesión no válida. Debes iniciar sesión.']);
    exit();
}

$db = new Conexion();
$info = [];

// Capturar cualquier error
try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Validar que se haya enviado la matrícula
        if (!isset($_POST['matricula']) || empty($_POST['matricula'])) {
            echo json_encode(['success' => 0, 'message' => 'Matrícula es requerida', 'debug' => $_POST]);
            exit();
        }

        $matricula = $db->real_escape_string($_POST['matricula']);

    // Verificar que el estudiante existe, si no, crearlo
    $cad = "SELECT ID FROM VRE_ESTUDIANTES WHERE MATRICULA = '$matricula'";
    $sql = $db->query($cad);

    if ($db->rows($sql) == 0) {
        // Crear estudiante si no existe
        $nombre = isset($_POST['nombre']) ? $db->real_escape_string($_POST['nombre']) : '';
        $apellido = isset($_POST['apellido']) ? $db->real_escape_string($_POST['apellido']) : '';
        $carrera = isset($_POST['carrera']) ? $db->real_escape_string($_POST['carrera']) : '';
        $semestre = isset($_POST['semestre']) ? intval($_POST['semestre']) : 1;

        $cad = "INSERT INTO VRE_ESTUDIANTES(MATRICULA, NOMBRE, APELLIDO, CARRERA, SEMESTRE)
                VALUES ('$matricula', '$nombre', '$apellido', '$carrera', $semestre)";
        $db->query($cad);
        $id_estudiante = $db->insert_id;
    } else {
        $row = $db->recorrer($sql);
        $id_estudiante = $row['ID'];
    }

    // Validar que se haya subido un archivo
    if (!isset($_FILES['foto'])) {
        echo json_encode(['success' => 0, 'message' => 'No se recibió ningún archivo', 'debug_files' => $_FILES]);
        exit();
    }

    if ($_FILES['foto']['error'] != UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'El archivo excede upload_max_filesize en php.ini',
            UPLOAD_ERR_FORM_SIZE => 'El archivo excede MAX_FILE_SIZE del formulario',
            UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
            UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta carpeta temporal',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir en disco',
            UPLOAD_ERR_EXTENSION => 'Una extensión PHP detuvo la subida'
        ];
        $errorMsg = isset($errorMessages[$_FILES['foto']['error']])
            ? $errorMessages[$_FILES['foto']['error']]
            : 'Error desconocido: ' . $_FILES['foto']['error'];
        echo json_encode(['success' => 0, 'message' => $errorMsg, 'error_code' => $_FILES['foto']['error']]);
        exit();
    }

    // Validar tipo de archivo
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($_FILES['foto']['type'], $allowedTypes)) {
        echo json_encode(['success' => 0, 'message' => 'Solo se permiten archivos JPG, PNG o GIF']);
        exit();
    }

    // Validar tamaño (máximo 5MB)
    if ($_FILES['foto']['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success' => 0, 'message' => 'El archivo no debe superar los 5MB']);
        exit();
    }

    // Crear carpeta si no existe
    $uploadDir = "../../../uploads/repositorio/$matricula/";
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Generar nombre único para el archivo
    $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nombreArchivo = uniqid('foto_' . time() . '_') . '.' . $extension;
    $rutaCompleta = $uploadDir . $nombreArchivo;

    // Mover archivo
    if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaCompleta)) {
        // Guardar en base de datos
        $titulo = isset($_POST['titulo']) ? $db->real_escape_string($_POST['titulo']) : '';
        $descripcion = isset($_POST['descripcion']) ? $db->real_escape_string($_POST['descripcion']) : '';
        $tipo_foto = isset($_POST['tipo_foto']) ? $db->real_escape_string($_POST['tipo_foto']) : 'INDIVIDUAL';
        $fecha_foto = isset($_POST['fecha_foto']) ? $db->real_escape_string($_POST['fecha_foto']) : date('Y-m-d');
        $privada = isset($_POST['privada']) ? $db->real_escape_string($_POST['privada']) : 'N';

        $foto_url = "/vidaEstudiantil/uploads/repositorio/$matricula/$nombreArchivo";

        $cad = "INSERT INTO VRE_REPOSITORIO_FOTOS(
                    ID_ESTUDIANTE, MATRICULA, TITULO, DESCRIPCION,
                    FOTO_URL, TIPO_FOTO, FECHA_FOTO, PRIVADA
                ) VALUES (
                    $id_estudiante, '$matricula', '$titulo', '$descripcion',
                    '$foto_url', '$tipo_foto', '$fecha_foto', '$privada'
                )";

        if ($db->query($cad)) {
            $info['success'] = 1;
            $info['message'] = 'Fotografía subida exitosamente';
            $info['id'] = $db->insert_id;
            $info['url'] = $foto_url;
        } else {
            // Si falla el insert, eliminar el archivo
            unlink($rutaCompleta);
            $info['success'] = 0;
            $info['message'] = 'Error al guardar en base de datos: ' . $db->error;
        }
    } else {
        $info['success'] = 0;
        $info['message'] = 'Error al subir el archivo. Verifica permisos de la carpeta uploads/';
        $info['debug_upload_dir'] = $uploadDir;
        $info['debug_writable'] = is_writable($uploadDir);
    }
    } else {
        $info['success'] = 0;
        $info['message'] = 'Método no permitido';
    }
} catch (Exception $e) {
    $info['success'] = 0;
    $info['message'] = 'Error: ' . $e->getMessage();
    $info['trace'] = $e->getTraceAsString();
}

echo json_encode($info);
?>
