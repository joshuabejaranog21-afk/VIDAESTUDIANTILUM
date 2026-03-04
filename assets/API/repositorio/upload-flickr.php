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
        $cad = "SELECT ID, NOMBRE, APELLIDO, CARRERA, SEMESTRE FROM VRE_ESTUDIANTES WHERE MATRICULA = '$matricula'";
        $sql = $db->query($cad);

        if ($db->rows($sql) == 0) {
            // Estudiante nuevo - validar que se proporcionen los datos básicos
            if (empty($_POST['nombre']) || empty($_POST['apellido'])) {
                echo json_encode(['success' => 0, 'message' => 'Para estudiantes nuevos, Nombre y Apellido son requeridos']);
                exit();
            }

            $nombre = $db->real_escape_string($_POST['nombre']);
            $apellido = $db->real_escape_string($_POST['apellido']);
            $carrera = isset($_POST['carrera']) ? $db->real_escape_string($_POST['carrera']) : '';
            $semestre = isset($_POST['semestre']) ? intval($_POST['semestre']) : 1;

            $cad = "INSERT INTO VRE_ESTUDIANTES(MATRICULA, NOMBRE, APELLIDO, CARRERA, SEMESTRE)
                    VALUES ('$matricula', '$nombre', '$apellido', '$carrera', $semestre)";
            $db->query($cad);
            $id_estudiante = $db->insert_id;
        } else {
            // Estudiante existente - usar datos existentes (NO actualizar)
            $row = $db->recorrer($sql);
            $id_estudiante = $row['ID'];
        }

        // Obtener URL de Flickr (ya sea directa o desde BBCode)
        $foto_url = '';
        $flickr_page_url = '';

        if (isset($_POST['foto_url']) && !empty($_POST['foto_url'])) {
            $input = trim($_POST['foto_url']);

            // Detectar si es BBCode de Flickr
            if (strpos($input, '[img]') !== false || strpos($input, '[url=') !== false) {
                // Extraer URL de la imagen del BBCode
                // Formato: [url=https://flic.kr/p/2pLPh8f][img]URL_IMAGEN[/img][/url]
                preg_match('/\[img\](https?:\/\/[^\[]+)\[\/img\]/', $input, $matches);
                if (isset($matches[1])) {
                    $foto_url = $matches[1];
                }

                // Extraer URL de la página de Flickr del BBCode
                preg_match('/\[url=(https?:\/\/[^\]]+)\]/', $input, $page_matches);
                if (isset($page_matches[1])) {
                    $flickr_page_url = $page_matches[1];
                }
            } else {
                // Es una URL directa
                $foto_url = $input;
                // No hay URL de página en este caso
            }

            // Validar que sea una URL válida
            if (!filter_var($foto_url, FILTER_VALIDATE_URL)) {
                echo json_encode(['success' => 0, 'message' => 'La URL proporcionada no es válida']);
                exit();
            }

            // Validar que sea una URL de imagen
            if (!preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $foto_url) &&
                !strpos($foto_url, 'flickr')) {
                echo json_encode(['success' => 0, 'message' => 'La URL debe ser de una imagen (jpg, png, gif) o de Flickr']);
                exit();
            }

            $foto_url = $db->real_escape_string($foto_url);
            $flickr_page_url = $flickr_page_url ? $db->real_escape_string($flickr_page_url) : '';
        } else {
            echo json_encode(['success' => 0, 'message' => 'Debes proporcionar una URL de Flickr o BBCode']);
            exit();
        }

        // Guardar en base de datos
        $titulo = isset($_POST['titulo']) ? $db->real_escape_string($_POST['titulo']) : '';
        $descripcion = isset($_POST['descripcion']) ? $db->real_escape_string($_POST['descripcion']) : '';
        $tipo_foto = isset($_POST['tipo_foto']) ? $db->real_escape_string($_POST['tipo_foto']) : 'INDIVIDUAL';
        $ciclo_escolar = isset($_POST['ciclo_escolar']) ? $db->real_escape_string($_POST['ciclo_escolar']) : '';
        $release_date = isset($_POST['release_date']) ? $db->real_escape_string($_POST['release_date']) : date('Y-m-d H:i:s');

        // Validar ciclo escolar
        if (empty($ciclo_escolar)) {
            echo json_encode(['success' => 0, 'message' => 'Ciclo escolar es requerido']);
            exit();
        }

        $cad = "INSERT INTO VRE_REPOSITORIO_FOTOS(
                    ID_ESTUDIANTE, MATRICULA, TITULO, DESCRIPCION,
                    FOTO_URL, FLICKR_PAGE_URL, TIPO_FOTO, CICLO_ESCOLAR, RELEASE_DATE
                ) VALUES (
                    $id_estudiante, '$matricula', '$titulo', '$descripcion',
                    '$foto_url', '$flickr_page_url', '$tipo_foto', '$ciclo_escolar', '$release_date'
                )";

        if ($db->query($cad)) {
            $info['success'] = 1;
            $info['message'] = 'Fotografía de Flickr agregada exitosamente';
            $info['id'] = $db->insert_id;
            $info['url'] = $foto_url;
        } else {
            $info['success'] = 0;
            $info['message'] = 'Error al guardar en base de datos: ' . $db->error;
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
