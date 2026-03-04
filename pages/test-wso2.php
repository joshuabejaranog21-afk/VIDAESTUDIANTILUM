<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Conexión WSO2</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f5f5f5; }
        .test-card { background: white; border-radius: 10px; padding: 30px; margin-bottom: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">🔧 Diagnóstico de Conexión WSO2</h1>

        <?php
        // Test 1: PHP cURL habilitado
        echo '<div class="test-card">';
        echo '<h3>1. Verificar cURL en PHP</h3>';
        if (function_exists('curl_version')) {
            $version = curl_version();
            echo '<p class="success">✓ cURL está habilitado</p>';
            echo '<pre>';
            echo "Versión: " . $version['version'] . "\n";
            echo "SSL Version: " . $version['ssl_version'] . "\n";
            echo "Protocolos: " . implode(', ', $version['protocols']) . "\n";
            echo '</pre>';
        } else {
            echo '<p class="error">✗ cURL NO está habilitado en PHP</p>';
            echo '<p>Solución: Habilita la extensión php_curl.dll en php.ini</p>';
        }
        echo '</div>';

        // Test 2: Conectar al servidor WSO2
        echo '<div class="test-card">';
        echo '<h3>2. Test de Conexión a WSO2</h3>';

        $wso2_url = 'https://wso2is.um.edu.mx/t/um.movil/oauth2/token?scope=openid';

        echo '<p class="info">Intentando conectar a:</p>';
        echo '<pre>' . $wso2_url . '</pre>';

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $wso2_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'username=test@um.movil&password=test&grant_type=password',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic dGdmWWJ3d291cHFxXzlCbUZnd3BuZ3hOelRzYTpfcmxxZWVXdGdrUXpnRmtFeUMzdlQ2bVowc3dh'
            ),
            // Opciones SSL/TLS
            CURLOPT_SSL_VERIFYPEER => false,  // Desactivar verificación SSL (solo para desarrollo)
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_VERBOSE => true
        ));

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);
        $curl_errno = curl_errno($curl);
        $curl_info = curl_getinfo($curl);

        curl_close($curl);

        echo '<h5>Resultado:</h5>';

        if ($curl_errno) {
            echo '<p class="error">✗ Error de cURL: ' . $curl_error . ' (Código: ' . $curl_errno . ')</p>';

            // Diagnóstico específico por código de error
            echo '<div class="alert alert-warning">';
            echo '<strong>Posibles soluciones:</strong><br>';

            if ($curl_errno == 6) {
                echo '- Error 6: No se puede resolver el host. Verifica tu conexión a internet o DNS.<br>';
                echo '- ¿Estás dentro de la red de la universidad o usando VPN?<br>';
            } elseif ($curl_errno == 7) {
                echo '- Error 7: No se puede conectar al servidor. El servidor puede estar caído.<br>';
                echo '- Verifica firewall o antivirus que bloquee la conexión.<br>';
            } elseif ($curl_errno == 28) {
                echo '- Error 28: Timeout. El servidor tarda mucho en responder.<br>';
                echo '- Intenta aumentar el timeout o verifica tu conexión.<br>';
            } elseif ($curl_errno == 35 || $curl_errno == 60) {
                echo '- Error SSL/TLS. Problema con el certificado del servidor.<br>';
                echo '- Ya desactivamos la verificación SSL, pero puede ser un problema del servidor.<br>';
            }

            echo '</div>';
        } else {
            echo '<p class="success">✓ Conexión exitosa</p>';
            echo '<p>Código HTTP: <strong>' . $http_code . '</strong></p>';

            if ($http_code == 200) {
                echo '<p class="success">✓ El servidor respondió correctamente</p>';
            } elseif ($http_code == 400 || $http_code == 401) {
                echo '<p class="info">ℹ️ El servidor rechazó las credenciales de prueba (esto es normal)</p>';
                echo '<p class="success">✓ Pero la CONEXIÓN funciona correctamente</p>';
            }

            echo '<h5>Respuesta del servidor:</h5>';
            echo '<pre>' . htmlspecialchars($response) . '</pre>';
        }

        echo '<h5>Información de la conexión:</h5>';
        echo '<pre>';
        echo "URL: " . $curl_info['url'] . "\n";
        echo "Código HTTP: " . $curl_info['http_code'] . "\n";
        echo "Tiempo total: " . $curl_info['total_time'] . " segundos\n";
        echo "Tiempo de conexión: " . $curl_info['connect_time'] . " segundos\n";
        echo "IP del servidor: " . ($curl_info['primary_ip'] ?? 'N/A') . "\n";
        echo '</pre>';

        echo '</div>';

        // Test 3: Test con credenciales reales (opcional)
        echo '<div class="test-card">';
        echo '<h3>3. Test con Credenciales Reales (Opcional)</h3>';
        echo '<p>Ingresa tus credenciales de UM Móvil para probar:</p>';
        ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Matrícula:</label>
                <input type="text" class="form-control" name="test_username" placeholder="Ej: 9801100" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Contraseña:</label>
                <input type="password" class="form-control" name="test_password" required>
            </div>
            <button type="submit" class="btn btn-primary">Probar Autenticación</button>
        </form>

        <?php
        if (isset($_POST['test_username']) && isset($_POST['test_password'])) {
            echo '<hr><h5>Resultado del Test:</h5>';

            $username = trim($_POST['test_username']);
            $password = $_POST['test_password'];

            // Agregar @um.movil si no lo tiene
            if (strpos($username, '@') === false) {
                $username = $username . '@um.movil';
            }

            $curl2 = curl_init();

            curl_setopt_array($curl2, array(
                CURLOPT_URL => $wso2_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'username=' . urlencode($username) . '&password=' . urlencode($password) . '&grant_type=password',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Basic dGdmWWJ3d291cHFxXzlCbUZnd3BuZ3hOelRzYTpfcmxxZWVXdGdrUXpnRmtFeUMzdlQ2bVowc3dh'
                ),
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ));

            $response2 = curl_exec($curl2);
            $http_code2 = curl_getinfo($curl2, CURLINFO_HTTP_CODE);
            $curl_error2 = curl_error($curl2);

            curl_close($curl2);

            if ($curl_error2) {
                echo '<p class="error">✗ Error: ' . $curl_error2 . '</p>';
            } elseif ($http_code2 == 200) {
                $data = json_decode($response2, true);
                if (isset($data['access_token'])) {
                    echo '<p class="success">✓ ¡AUTENTICACIÓN EXITOSA!</p>';
                    echo '<p>Token recibido correctamente</p>';
                    echo '<pre>' . htmlspecialchars(json_encode($data, JSON_PRETTY_PRINT)) . '</pre>';
                } else {
                    echo '<p class="error">✗ No se recibió token</p>';
                    echo '<pre>' . htmlspecialchars($response2) . '</pre>';
                }
            } elseif ($http_code2 == 400 || $http_code2 == 401) {
                echo '<p class="error">✗ Credenciales incorrectas</p>';
                echo '<p>Respuesta del servidor:</p>';
                echo '<pre>' . htmlspecialchars($response2) . '</pre>';
            } else {
                echo '<p class="error">✗ Error HTTP: ' . $http_code2 . '</p>';
                echo '<pre>' . htmlspecialchars($response2) . '</pre>';
            }
        }

        echo '</div>';

        // Test 4: Verificar configuración de PHP
        echo '<div class="test-card">';
        echo '<h3>4. Configuración de PHP</h3>';
        echo '<pre>';
        echo "allow_url_fopen: " . (ini_get('allow_url_fopen') ? 'Sí' : 'No') . "\n";
        echo "max_execution_time: " . ini_get('max_execution_time') . " segundos\n";
        echo "default_socket_timeout: " . ini_get('default_socket_timeout') . " segundos\n";
        echo '</pre>';
        echo '</div>';

        // Resumen y recomendaciones
        echo '<div class="test-card">';
        echo '<h3>📋 Resumen y Recomendaciones</h3>';
        echo '<ol>';
        echo '<li>Si ves "Error 6 o 7": Verifica tu conexión a internet y acceso a wso2is.um.edu.mx</li>';
        echo '<li>Si necesitas VPN: Asegúrate de estar conectado a la VPN de la universidad</li>';
        echo '<li>Si el test de conexión funciona pero las credenciales fallan: Verifica usuario/contraseña en UM Móvil</li>';
        echo '<li>Si todo funciona aquí pero no en el login: Revisa los logs de Apache/PHP</li>';
        echo '</ol>';
        echo '</div>';
        ?>

        <a href="../mi-foto.php" class="btn btn-secondary">← Volver a Mi Foto</a>
    </div>
</body>
</html>
