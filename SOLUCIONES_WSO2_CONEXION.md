# Soluciones al Error de Conexión con WSO2

## ❌ Error: "Error al conectar con el servidor de autenticación"

---

## 🔧 Herramienta de Diagnóstico

**PRIMERO:** Ejecuta la herramienta de diagnóstico:

```
http://localhost/vidaEstudiantil/pages/test-wso2.php
```

Esta herramienta te dirá exactamente cuál es el problema.

---

## 🚨 Problemas Comunes y Soluciones

### 1. cURL no está habilitado en PHP

**Error:** "cURL NO está habilitado en PHP"

**Solución:**

#### Para WAMP:
1. Abre **WAMP Manager** (ícono en la bandeja del sistema)
2. Clic en **PHP** → **PHP Extensions**
3. Busca y activa: **php_curl**
4. Reinicia WAMP

#### Manual (php.ini):
1. Abre `c:\wamp64\bin\php\php.ini`
2. Busca: `;extension=curl`
3. Quita el punto y coma: `extension=curl`
4. Guarda y reinicia Apache

---

### 2. Error 6: Could not resolve host

**Error:** "Error de cURL: 6"

**Problema:** Tu PC no puede resolver el nombre `wso2is.um.edu.mx`

**Soluciones:**

#### A. Verifica conexión a internet
```cmd
ping wso2is.um.edu.mx
```

Si no responde:

#### B. Usa DNS de Google
1. Panel de Control → Redes → Propiedades del adaptador
2. IPv4 → Propiedades
3. DNS: `8.8.8.8` y `8.8.4.4`

#### C. ¿Estás en la universidad?
- El servidor puede estar solo accesible desde la red interna de la UM
- **Necesitas VPN** si estás fuera del campus

---

### 3. Error 7: Failed to connect

**Error:** "Error de cURL: 7"

**Problema:** No se puede conectar al puerto 443 (HTTPS)

**Soluciones:**

#### A. Firewall bloqueando
- Revisa Windows Defender Firewall
- Agrega excepción para Apache/PHP

#### B. Antivirus bloqueando
- Algunos antivirus bloquean conexiones HTTPS
- Temporalmente desactiva para probar

#### C. Puerto 443 bloqueado
```cmd
telnet wso2is.um.edu.mx 443
```

Si falla, tu ISP o red bloquea el puerto.

---

### 4. Error SSL/TLS (35, 60, 77)

**Error:** "SSL certificate problem" o "Error de cURL: 60"

**Solución:** Ya está implementada en el código

El código ya incluye:
```php
CURLOPT_SSL_VERIFYPEER => false
CURLOPT_SSL_VERIFYHOST => false
```

Esto desactiva la verificación SSL (solo para desarrollo).

---

### 5. Timeout (Error 28)

**Error:** "Operation timed out" o "Error de cURL: 28"

**Soluciones:**

#### A. Aumentar timeout
Ya está configurado a 30 segundos en el código.

#### B. Internet lento
- Verifica tu conexión a internet
- Intenta en otro momento

#### C. Servidor saturado
- El servidor WSO2 puede estar saturado
- Intenta más tarde

---

### 6. Necesitas VPN de la Universidad

Si estás **fuera del campus**, es probable que necesites:

1. **VPN de la UM** configurada
2. Conexión activa a la VPN
3. Luego intenta el login

**Contacta a TI de la universidad** para obtener acceso VPN.

---

## ✅ Verificación Paso a Paso

### Paso 1: Verifica cURL
```php
http://localhost/vidaEstudiantil/pages/test-wso2.php
```
Debe decir: "✓ cURL está habilitado"

### Paso 2: Verifica conectividad
```cmd
ping wso2is.um.edu.mx
```
Debe responder con una IP.

### Paso 3: Prueba con credenciales reales
En la herramienta de diagnóstico, ingresa tus credenciales de UM Móvil.

### Paso 4: Lee el error específico
El sistema ahora muestra:
- Código de error de cURL
- Detalles del error
- Botón a herramienta de diagnóstico

---

## 🔐 Si Ya Funciona la Conexión pero Falla Login

### Verificar credenciales:
1. Abre la app **UM Móvil** en tu teléfono
2. Cierra sesión y vuelve a iniciar
3. Si funciona en UM Móvil, usa las mismas credenciales

### Formato de usuario:
Puedes usar:
- `9801100` (solo matrícula)
- `9801100@um.movil` (completo)

Ambos funcionan.

---

## 📱 Contactar Soporte

Si nada funciona, contacta:

**Soporte TI - Universidad de Montemorelos**
- Pregunta por: Acceso a WSO2 Identity Server
- Menciona: "Necesito acceso al endpoint OAuth2 de UM Móvil"

Información que necesitarán:
- Tu matrícula
- Error específico (copia del error de la herramienta)
- Si estás dentro o fuera del campus

---

## 🛠️ Solución Temporal (Solo Desarrollo)

Si estás desarrollando y no puedes conectarte a WSO2:

### Opción: Mock de autenticación

Crea este archivo temporal:
```php
// assets/API/auth/login-estudiante-mock.php
<?php
session_start();
header('Content-Type: application/json');

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

// Solo para desarrollo - QUITAR EN PRODUCCIÓN
$_SESSION['estudiante_logged'] = true;
$_SESSION['estudiante_id'] = 1;
$_SESSION['estudiante_matricula'] = str_replace('@um.movil', '', $username);
$_SESSION['estudiante_nombre'] = 'Test';
$_SESSION['estudiante_apellido'] = 'Usuario';

echo json_encode([
    'success' => true,
    'message' => 'Login simulado (MOCK)',
    'redirect' => '../../../pages/repositorio/'
]);
?>
```

**ADVERTENCIA:** Esto es SOLO para desarrollo. Quítalo en producción.

---

## 📊 Códigos de Error cURL Comunes

| Código | Significado | Solución |
|--------|-------------|----------|
| 6 | No se puede resolver host | Verifica DNS/VPN |
| 7 | No se puede conectar | Firewall/Puerto bloqueado |
| 28 | Timeout | Internet lento/Servidor saturado |
| 35 | SSL Error | Ya está solucionado en código |
| 60 | SSL Certificate problem | Ya está solucionado en código |

---

## ✨ Cambios Implementados

Para solucionar el error, se implementó:

1. ✅ **Desactivación de verificación SSL** (desarrollo)
   ```php
   CURLOPT_SSL_VERIFYPEER => false
   CURLOPT_SSL_VERIFYHOST => false
   ```

2. ✅ **Timeout aumentado** a 30 segundos
   ```php
   CURLOPT_TIMEOUT => 30
   ```

3. ✅ **Herramienta de diagnóstico** completa
   - Test de cURL
   - Test de conexión
   - Test con credenciales reales

4. ✅ **Mensajes de error detallados**
   - Código de error
   - Descripción
   - Enlace a herramienta

---

## 🎯 Próximos Pasos

1. **Ejecuta:** `http://localhost/vidaEstudiantil/pages/test-wso2.php`
2. **Lee el diagnóstico** completo
3. **Aplica la solución** específica a tu error
4. **Prueba el login** nuevamente

---

**¿Aún tienes problemas?**

Envía captura de pantalla de:
1. Herramienta de diagnóstico completa
2. Error en el login
3. Console del navegador (F12)
