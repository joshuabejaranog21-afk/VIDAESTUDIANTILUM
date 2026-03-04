# Sistema de Autenticación de Estudiantes con WSO2 OAuth2

## 🎯 ¿Qué se implementó?

Se creó un **sistema completo de autenticación** para estudiantes usando la API OAuth2 de WSO2 Identity Server de la universidad. Los estudiantes pueden:

1. ✅ **Iniciar sesión** con sus credenciales de UM Móvil
2. ✅ **Ver solo su repositorio personal** de fotos
3. ✅ **Dar likes en anuarios** (autenticados)
4. ✅ **Cerrar sesión** de forma segura

---

## 📁 Archivos Creados

### 1. APIs de Autenticación

**Ubicación:** `assets/API/auth/`

- **`login-estudiante.php`** - Autentica contra WSO2 y crea sesión
- **`logout-estudiante.php`** - Cierra la sesión del estudiante
- **`verificar-sesion-estudiante.php`** - Verifica si hay sesión activa

### 2. Página de Login

**Ubicación:** `pages/login-estudiante.php`

- Interfaz moderna y responsive
- Valida credenciales contra WSO2
- Redirige al repositorio automáticamente

### 3. Modificaciones en Repositorio

**Ubicación:** `pages/repositorio/index.php`

- Valida que el estudiante esté logueado
- Solo muestra las fotos del estudiante autenticado
- Botón de cerrar sesión
- Indicador de sesión activa

### 4. Sistema de Likes Autenticado

**Archivos modificados:**
- `assets/API/anuarios/like.php`
- `assets/API/anuarios/unlike.php`
- `assets/API/anuarios/check-like.php`

---

## 🚀 Cómo Funciona

### Flujo de Autenticación

```
┌─────────────────┐
│  Estudiante     │
│  Ingresa datos  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ login-          │
│ estudiante.php  │
└────────┬────────┘
         │
         ▼
┌─────────────────────────┐
│  API WSO2               │
│  wso2is.um.edu.mx       │
│  OAuth2 Token           │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│  ✓ Credenciales válidas │
│  ✓ Token recibido       │
│  ✓ Sesión creada        │
│  ✓ Matrícula extraída   │
└────────┬────────────────┘
         │
         ▼
┌─────────────────────────┐
│  Redirigir a:           │
│  /pages/repositorio/    │
│                         │
│  - Autocargar fotos     │
│  - Mostrar nombre       │
│  - Bloquear otros repos │
└─────────────────────────┘
```

---

## 🔐 Credenciales de Estudiante

### Formato de Username

Los estudiantes pueden ingresar:
- **Solo matrícula:** `1220593`
- **Usuario completo:** `1220593@um.movil`

El sistema detecta automáticamente el formato y lo ajusta.

### Ejemplo de Credenciales
```
Usuario: 9801100
Password: tu_contraseña_um_movil
```

O:

```
Usuario: 9801100@um.movil
Password: tu_contraseña_um_movil
```

---

## 💾 Estructura de Sesión

Cuando un estudiante inicia sesión, se guardan estos datos en `$_SESSION`:

```php
$_SESSION['estudiante_logged'] = true;
$_SESSION['estudiante_id'] = 123;              // ID en VRE_ESTUDIANTES
$_SESSION['estudiante_matricula'] = '1220593'; // Matrícula del estudiante
$_SESSION['estudiante_nombre'] = 'Juan';
$_SESSION['estudiante_apellido'] = 'Pérez';
$_SESSION['estudiante_carrera'] = 'Ingeniería';
$_SESSION['access_token'] = 'eyJhbGci...';     // Token de WSO2
$_SESSION['token_expires'] = 1234567890;       // Timestamp de expiración
```

---

## 🛡️ Seguridad Implementada

### 1. Validación en el Repositorio

**Archivo:** `assets/API/repositorio/listar.php`

```php
// Solo el estudiante puede ver su propio repositorio
if ($_SESSION['estudiante_matricula'] !== $matricula) {
    echo json_encode([
        'success' => 0,
        'message' => 'No tienes permiso para ver este repositorio'
    ]);
    exit();
}
```

### 2. Validación en Likes

Los likes se registran con la matrícula del estudiante:

```sql
INSERT INTO VRE_ANUARIOS_LIKES (ID_ANUARIO, MATRICULA, IP)
VALUES (123, '1220593', '192.168.1.1');
```

Esto permite:
- ✅ Evitar likes duplicados
- ✅ Rastrear quién dio like
- ✅ Estadísticas por estudiante

### 3. Expiración del Token

El token de WSO2 expira automáticamente. Si expira:
```php
if (time() > $_SESSION['token_expires']) {
    // Cerrar sesión automáticamente
    unset($_SESSION['estudiante_logged']);
}
```

---

## 📊 Base de Datos

### Tabla VRE_ESTUDIANTES

```sql
CREATE TABLE VRE_ESTUDIANTES(
    ID INT PRIMARY KEY AUTO_INCREMENT,
    MATRICULA VARCHAR(7) UNIQUE NOT NULL,
    NOMBRE VARCHAR(100),
    APELLIDO VARCHAR(100),
    CARRERA VARCHAR(200),
    SEMESTRE INT,
    EMAIL VARCHAR(200),
    ACTIVO ENUM('S','N') DEFAULT 'S',
    FECHA_REGISTRO DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

Cuando un estudiante inicia sesión por primera vez, se crea automáticamente en esta tabla.

### Tabla VRE_ANUARIOS_LIKES (modificada)

```sql
CREATE TABLE VRE_ANUARIOS_LIKES(
    ID INT PRIMARY KEY AUTO_INCREMENT,
    ID_ANUARIO INT NOT NULL,
    ID_USUARIO INT,           -- Para admins
    MATRICULA VARCHAR(50),    -- Para estudiantes ✓ NUEVO
    IP VARCHAR(50),
    FECHA DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_like (ID_ANUARIO, MATRICULA)
);
```

---

## 🔧 APIs Disponibles

### 1. Login de Estudiante

```
POST /assets/API/auth/login-estudiante.php
```

**Parámetros:**
- `username` (required) - Matrícula o usuario completo
- `password` (required) - Contraseña de UM Móvil

**Response exitoso:**
```json
{
    "success": true,
    "message": "Inicio de sesión exitoso",
    "estudiante": {
        "id": 123,
        "matricula": "1220593",
        "nombre": "Juan",
        "apellido": "Pérez",
        "carrera": "Ingeniería en Sistemas",
        "nombre_completo": "Juan Pérez"
    },
    "redirect": "../../../pages/repositorio/"
}
```

**Response error:**
```json
{
    "success": false,
    "message": "Usuario o contraseña incorrectos"
}
```

### 2. Logout de Estudiante

```
POST /assets/API/auth/logout-estudiante.php
```

**Response:**
```json
{
    "success": true,
    "message": "Sesión cerrada exitosamente"
}
```

### 3. Verificar Sesión

```
GET /assets/API/auth/verificar-sesion-estudiante.php
```

**Response (logueado):**
```json
{
    "logged": true,
    "estudiante": {
        "id": 123,
        "matricula": "1220593",
        "nombre": "Juan",
        "apellido": "Pérez",
        "carrera": "Ingeniería",
        "nombre_completo": "Juan Pérez"
    }
}
```

**Response (no logueado):**
```json
{
    "logged": false,
    "message": "No hay sesión activa"
}
```

### 4. Dar Like a Anuario

```
POST /assets/API/anuarios/like.php
```

**Parámetros:**
- `id` (required) - ID del anuario

**Response:**
```json
{
    "success": true,
    "likes": 42,
    "message": "¡Gracias por tu like!"
}
```

**Error (no autenticado):**
```json
{
    "success": false,
    "message": "Debes iniciar sesión para dar like"
}
```

**Error (ya dio like):**
```json
{
    "success": false,
    "message": "Ya has dado like a este anuario"
}
```

### 5. Quitar Like

```
POST /assets/API/anuarios/unlike.php
```

**Parámetros:**
- `id` (required) - ID del anuario

**Response:**
```json
{
    "success": true,
    "likes": 41
}
```

### 6. Verificar si dio Like

```
GET /assets/API/anuarios/check-like.php?id=123
```

**Response:**
```json
{
    "liked": true,
    "logged": true
}
```

---

## 💻 Uso desde el Frontend

### Login

```javascript
$.ajax({
    url: '../assets/API/auth/login-estudiante.php',
    type: 'POST',
    data: {
        username: '1220593',
        password: 'mi_password'
    },
    success: function(response) {
        if (response.success) {
            window.location.href = response.redirect;
        } else {
            alert(response.message);
        }
    }
});
```

### Dar Like

```javascript
$.ajax({
    url: '../assets/API/anuarios/like.php',
    type: 'POST',
    data: { id: 123 },
    success: function(response) {
        if (response.success) {
            $('#likeCount').text(response.likes);
            alert(response.message);
        } else {
            alert(response.message);
        }
    }
});
```

### Cerrar Sesión

```javascript
$.ajax({
    url: '../assets/API/auth/logout-estudiante.php',
    type: 'POST',
    success: function(response) {
        window.location.href = '../login-estudiante.php';
    }
});
```

---

## 🎨 Interfaz del Repositorio

### Vista con Estudiante Logueado

```
┌───────────────────────────────────────────────────────┐
│  Mi Repositorio de Fotos                              │
├───────────────────────────────────────────────────────┤
│                                                       │
│  ✓ Sesión activa: Juan Pérez (1220593)  [Cerrar]    │
│                                                       │
│  ┌─────────────────────────────────────────────┐     │
│  │  Matrícula: 1220593 (bloqueada)            │     │
│  │  [Buscar]  [Subir Foto]                    │     │
│  └─────────────────────────────────────────────┘     │
│                                                       │
│  📸 Tus Fotografías (12)                              │
│  ┌──────┐ ┌──────┐ ┌──────┐                         │
│  │Foto1 │ │Foto2 │ │Foto3 │                         │
│  └──────┘ └──────┘ └──────┘                         │
└───────────────────────────────────────────────────────┘
```

### Características Cuando Está Logueado

1. **Campo de matrícula bloqueado** - No puede ver otros repositorios
2. **Autocargar fotos** - Se cargan automáticamente sus fotos
3. **Nombre visible** - Muestra su nombre en la interfaz
4. **Botón cerrar sesión** - Puede salir cuando quiera

---

## 🔐 Integración con WSO2

### URL del Servidor WSO2

```
https://wso2is.um.edu.mx/t/um.movil/oauth2/token?scope=openid
```

### Authorization Header

```
Authorization: Basic dGdmWWJ3d291cHFxXzlCbUZnd3BuZ3hOelRzYTpfcmxxZWVXdGdrUXpnRmtFeUMzdlQ2bVowc3dh
```

Este es el token de la aplicación UM Móvil (ya está configurado en el código).

### Extracción de Matrícula

Del username `9801100@um.movil`, se extrae la matrícula `9801100`:

```php
$matricula = str_replace('@um.movil', '', $username_wso2);
```

---

## 🚀 URLs de Acceso

### Para Estudiantes:
```
Login:      http://localhost/vidaEstudiantil/pages/login-estudiante.php
Repositorio: http://localhost/vidaEstudiantil/pages/repositorio/
```

### APIs:
```
POST /assets/API/auth/login-estudiante.php
POST /assets/API/auth/logout-estudiante.php
GET  /assets/API/auth/verificar-sesion-estudiante.php
POST /assets/API/anuarios/like.php
POST /assets/API/anuarios/unlike.php
GET  /assets/API/anuarios/check-like.php
```

---

## 🆘 Solución de Problemas

### "Usuario o contraseña incorrectos"

**Causas posibles:**
- Credenciales incorrectas de UM Móvil
- Servidor WSO2 no disponible
- Estudiante no tiene acceso a UM Móvil

**Solución:** Verificar credenciales en la app UM Móvil primero.

### "No tienes permiso para ver este repositorio"

**Causa:** El estudiante intenta ver fotos de otra matrícula

**Solución:** Solo puede ver su propio repositorio. Es por seguridad.

### "Error al conectar con el servidor de autenticación"

**Causa:** No se puede conectar a WSO2

**Solución:** Verificar conexión a internet y que `wso2is.um.edu.mx` esté accesible.

### "Sesión expirada"

**Causa:** El token de WSO2 expiró (después de ~1 hora)

**Solución:** Cerrar sesión y volver a iniciar sesión.

---

## 🔄 Diferencias: Estudiante vs Admin

| Característica | Estudiante | Admin |
|---------------|------------|-------|
| **Login** | `/pages/login-estudiante.php` | `/login/` |
| **Autenticación** | WSO2 OAuth2 | Base de datos local |
| **Repositorio** | Solo el suyo | Puede ver todos |
| **Likes** | Guarda matrícula | Guarda ID de usuario |
| **Sesión** | `$_SESSION['estudiante_logged']` | `$_SESSION['sessionAdmin']` |

---

## ✨ Características de Seguridad

### ✅ Implementadas

1. **Autenticación OAuth2** - Usa el servidor oficial de la universidad
2. **Validación de sesión** - Verifica token en cada request
3. **Aislamiento de datos** - Cada estudiante solo ve sus fotos
4. **Tokens con expiración** - Las sesiones expiran automáticamente
5. **Protección SQL Injection** - Uso de `real_escape_string()`
6. **HTTPS recomendado** - Para producción, usar HTTPS

### 🔒 Recomendaciones Adicionales

1. **Activar HTTPS** en producción
2. **Configurar CORS** adecuadamente
3. **Rate limiting** para evitar ataques de fuerza bruta
4. **Logs de auditoría** para monitorear intentos de login

---

## 📝 Ejemplo de Uso Completo

### 1. Estudiante inicia sesión

```
1. Abre: /pages/login-estudiante.php
2. Ingresa: 1220593 y su password
3. Sistema valida con WSO2
4. ✓ Redirige a /pages/repositorio/
```

### 2. Ve su repositorio

```
1. El sistema detecta que está logueado
2. Autocarga sus fotos (matrícula 1220593)
3. Campo de matrícula está bloqueado
4. Solo puede subir/ver/eliminar SUS fotos
```

### 3. Ve un anuario y da like

```
1. Va a /pages/anuarios/
2. Hace clic en "Me gusta"
3. Sistema verifica que está logueado
4. Registra like con su matrícula
5. ✓ Like guardado exitosamente
```

### 4. Cierra sesión

```
1. Hace clic en "Cerrar Sesión"
2. Sistema limpia $_SESSION
3. Redirige a /pages/login-estudiante.php
```

---

## 📊 Estadísticas Disponibles

Con este sistema puedes obtener:

- **Estudiantes activos:** Cuenta de sesiones únicas
- **Likes por estudiante:** Consulta `VRE_ANUARIOS_LIKES` por matrícula
- **Anuarios más populares:** Los que tienen más likes
- **Fotos por estudiante:** Total de fotos en repositorio

```sql
-- Estudiante con más likes
SELECT MATRICULA, COUNT(*) as total_likes
FROM VRE_ANUARIOS_LIKES
WHERE MATRICULA IS NOT NULL
GROUP BY MATRICULA
ORDER BY total_likes DESC;

-- Anuario más popular
SELECT a.TITULO, COUNT(l.ID) as total_likes
FROM VRE_ANUARIOS a
LEFT JOIN VRE_ANUARIOS_LIKES l ON a.ID = l.ID_ANUARIO
GROUP BY a.ID
ORDER BY total_likes DESC;
```

---

**Implementado:** 2025
**Versión:** 1.0
**Base de datos:** pruebasumadmin
**Servidor OAuth:** WSO2 Identity Server (wso2is.um.edu.mx)
