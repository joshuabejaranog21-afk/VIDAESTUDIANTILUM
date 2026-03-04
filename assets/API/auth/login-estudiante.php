<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

include("../db.php");

$db = new Conexion();
$response = [];

try {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Obtener credenciales
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        // Validar que se enviaron credenciales
        if (empty($username) || empty($password)) {
            echo json_encode([
                'success' => false,
                'message' => 'Usuario y contraseña son requeridos'
            ]);
            exit();
        }

        // Preparar datos para WSO2
        // El username debe tener formato: MATRICULA@um.movil
        // Si el usuario solo pone la matrícula, agregarle @um.movil
        if (strpos($username, '@') === false) {
            $username_wso2 = $username . '@um.movil';
        } else {
            $username_wso2 = $username;
        }

        // Extraer matrícula (quitar @um.movil)
        $matricula = str_replace('@um.movil', '', $username_wso2);

        // Llamar a la API de WSO2
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://wso2is.um.edu.mx/t/um.movil/oauth2/token?scope=openid',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'username=' . urlencode($username_wso2) . '&password=' . urlencode($password) . '&grant_type=password',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic dGdmWWJ3d291cHFxXzlCbUZnd3BuZ3hOelRzYTpfcmxxZWVXdGdrUXpnRmtFeUMzdlQ2bVowc3dh'
            ),
            // Opciones SSL/TLS para manejar certificados
            CURLOPT_SSL_VERIFYPEER => false,  // Desactivar verificación del certificado (solo para desarrollo)
            CURLOPT_SSL_VERIFYHOST => false,  // Desactivar verificación del host
        ));

        $wso2_response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);
        $curl_errno = curl_errno($curl);
        curl_close($curl);

        // Verificar si hubo error de cURL
        if ($curl_errno) {
            $response = [
                'success' => false,
                'message' => 'Error de conexión con el servidor de autenticación',
                'details' => $curl_error,
                'error_code' => $curl_errno,
                'help' => 'Intenta acceder desde: ' . $_SERVER['HTTP_HOST'] . '/vidaEstudiantil/pages/test-wso2.php'
            ];
            echo json_encode($response);
            exit();
        }

        // Verificar respuesta de WSO2
        if ($http_code == 200) {
            $wso2_data = json_decode($wso2_response, true);

            if (isset($wso2_data['access_token'])) {
                // Autenticación exitosa

                // Buscar o crear estudiante en la BD
                $matricula_safe = $db->real_escape_string($matricula);
                $query = "SELECT * FROM VRE_ESTUDIANTES WHERE MATRICULA = '$matricula_safe'";
                $result = $db->query($query);

                if ($db->rows($result) > 0) {
                    // Estudiante existe
                    $estudiante = $result->fetch_assoc();
                } else {
                    // Crear estudiante nuevo
                    $insert = "INSERT INTO VRE_ESTUDIANTES(MATRICULA, NOMBRE, APELLIDO, ACTIVO)
                               VALUES ('$matricula_safe', '', '', 'S')";
                    $db->query($insert);
                    $estudiante = [
                        'ID' => $db->insert_id,
                        'MATRICULA' => $matricula,
                        'NOMBRE' => '',
                        'APELLIDO' => '',
                        'CARRERA' => '',
                        'SEMESTRE' => null
                    ];
                }

                // Crear sesión
                $_SESSION['estudiante_logged'] = true;
                $_SESSION['estudiante_id'] = $estudiante['ID'];
                $_SESSION['estudiante_matricula'] = $matricula;
                $_SESSION['estudiante_nombre'] = $estudiante['NOMBRE'];
                $_SESSION['estudiante_apellido'] = $estudiante['APELLIDO'];
                $_SESSION['estudiante_carrera'] = $estudiante['CARRERA'];
                $_SESSION['access_token'] = $wso2_data['access_token'];
                $_SESSION['token_expires'] = time() + ($wso2_data['expires_in'] ?? 3600);

                // Actualizar último acceso
                $update = "UPDATE VRE_ESTUDIANTES SET
                           FECHA_REGISTRO = NOW()
                           WHERE MATRICULA = '$matricula_safe'";
                $db->query($update);

                $response = [
                    'success' => true,
                    'message' => 'Inicio de sesión exitoso',
                    'estudiante' => [
                        'id' => $estudiante['ID'],
                        'matricula' => $matricula,
                        'nombre' => $estudiante['NOMBRE'],
                        'apellido' => $estudiante['APELLIDO'],
                        'carrera' => $estudiante['CARRERA'],
                        'nombre_completo' => trim($estudiante['NOMBRE'] . ' ' . $estudiante['APELLIDO'])
                    ],
                    'redirect' => '/vidaEstudiantil/pages/repositorio/'
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Error al obtener token de autenticación'
                ];
            }
        } else {
            // Error de autenticación
            $error_data = json_decode($wso2_response, true);

            if ($http_code == 400 || $http_code == 401) {
                $response = [
                    'success' => false,
                    'message' => 'Usuario o contraseña incorrectos'
                ];
            } else {
                $response = [
                    'success' => false,
                    'message' => 'Error al conectar con el servidor de autenticación',
                    'code' => $http_code
                ];
            }
        }
    } else {
        $response = [
            'success' => false,
            'message' => 'Método no permitido'
        ];
    }
} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Error interno: ' . $e->getMessage()
    ];
}

echo json_encode($response);
?>
