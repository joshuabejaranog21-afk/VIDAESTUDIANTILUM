# Sistema VRE - Vida Estudiantil
## Universidad de Montemorelos

---

## 📋 Índice

1. [Descripción General](#descripción-general)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Módulos Implementados](#módulos-implementados)
4. [Base de Datos](#base-de-datos)
5. [Sistema de Permisos](#sistema-de-permisos)
6. [APIs Implementadas](#apis-implementadas)
7. [Sistema de Galería Centralizada](#sistema-de-galería-centralizada)
8. [Instalación](#instalación)
9. [Guía de Uso](#guía-de-uso)
10. [Migraciones Realizadas](#migraciones-realizadas)

---

## 📖 Descripción General

El **Sistema VRE (Vida Estudiantil)** es una plataforma web integral desarrollada para la Universidad de Montemorelos que centraliza la gestión de todas las actividades estudiantiles, incluyendo clubes, ministerios, deportes, eventos y más.

### Características Principales

- ✅ **Sistema de permisos granular** por rol y módulo
- ✅ **Galería de imágenes centralizada** para todos los módulos
- ✅ **Panel de administración** completo con DataTables
- ✅ **Paneles de directores** (Mi Club / Mi Ministerio) con permisos limitados
- ✅ **Auditoría** completa de acciones
- ✅ **APIs RESTful** para todos los módulos
- ✅ **Responsive design** con Bootstrap 5

### Tecnologías Utilizadas

| Tecnología | Versión | Propósito |
|------------|---------|-----------|
| PHP | 7.4+ | Backend |
| MySQL | 8.0+ | Base de datos |
| Bootstrap | 5.x | Frontend framework |
| jQuery | 3.x | Manipulación DOM |
| DataTables | 1.13.x | Tablas interactivas |
| Acorn Icons | Latest | Iconografía |
| Acorn Template | Latest | Template admin |

---

## 🏗️ Arquitectura del Sistema

### Estructura de Directorios

```
vida-estudiantil_Hithan/
├── assets/
│   ├── API/                    # APIs RESTful
│   │   ├── clubes/
│   │   ├── ministerios/
│   │   ├── deportes/
│   │   ├── ligas/
│   │   ├── eventos/
│   │   ├── banners/
│   │   ├── mi-club/
│   │   ├── mi-ministerio/
│   │   └── upload.php
│   ├── php/
│   │   ├── template.php       # Clase principal del sistema
│   │   ├── ImageHelper.php    # Gestión de imágenes
│   │   └── db.php             # Conexión a BD
│   └── uploads/               # Imágenes subidas
├── pages/                     # Páginas del sistema
│   ├── clubes/
│   ├── ministerios/
│   ├── deportes/
│   ├── ligas/
│   ├── eventos/
│   ├── banners/
│   ├── mi-club/
│   └── mi-ministerio/
├── cpanel/                    # Panel de administración
├── index.php                  # Página principal
├── login.php                  # Inicio de sesión
├── SISTEMA_VRE_COMPLETO.sql   # Script de instalación
└── README.md                  # Este archivo
```

### Patrón de Diseño

El sistema sigue un **patrón MVC simplificado**:

- **Modelo**: Clases en `assets/php/` (Template, Conexion, ImageHelper)
- **Vista**: Archivos en `pages/` con PHP + HTML + Bootstrap
- **Controlador**: APIs en `assets/API/` que procesan JSON

---

## 🎯 Módulos Implementados

### 1. **Sistema de Usuarios y Permisos**

**Ubicación**: `assets/php/template.php`

**Funcionalidades**:
- Login y sesiones
- Roles: Superusuario, Administrador, Editores, Directores
- Permisos granulares: ver, crear, editar, eliminar
- Auditoría de acciones

**Roles Disponibles**:

| ID | Rol | Descripción |
|----|-----|-------------|
| 1 | SUPERUSUARIO | Acceso total |
| 2 | ADMINISTRADOR | Permisos amplios |
| 3 | EDITOR_INVOLUCRATE | Edita Clubes/Ministerios/Deportes |
| 4 | EDITOR_EVENTOS | Edita Eventos |
| 5 | VISUALIZADOR | Solo lectura |
| 8 | DIRECTOR_CLUB | Gestiona su club |
| 11 | DIRECTOR_MINISTERIO | Gestiona su ministerio |

### 2. **Clubes Estudiantiles**

**Ubicación**: `pages/clubes/`, `assets/API/clubes/`

**Funcionalidades**:
- CRUD completo de clubes
- Asignación de directores
- Gestión de directiva (8 cargos)
- Galería de imágenes
- Integración con sistema de permisos

**APIs**:
- `POST /assets/API/clubes/crear.php` - Crear club
- `POST /assets/API/clubes/editar.php` - Editar club
- `POST /assets/API/clubes/eliminar.php` - Eliminar club
- `GET /assets/API/clubes/listar.php` - Listar clubes

**Estructura de Directiva**:
- Director
- Subdirector
- Secretario
- Tesorero
- Capellán
- Consejero General
- Logística
- Media

### 3. **Ministerios**

**Ubicación**: `pages/ministerios/`, `assets/API/ministerios/`

**Funcionalidades**:
- CRUD completo de ministerios
- Asignación de directores
- Gestión de directiva (8 cargos)
- Galería de imágenes
- Sistema de permisos

**APIs**:
- `POST /assets/API/ministerios/crear.php` - Crear ministerio
- `POST /assets/API/ministerios/editar.php` - Editar ministerio
- `POST /assets/API/ministerios/eliminar.php` - Eliminar ministerio
- `GET /assets/API/ministerios/listar.php` - Listar ministerios

### 4. **Deportes y Ligas**

**Ubicación**: `pages/deportes/`, `pages/ligas/`, `assets/API/deportes/`, `assets/API/ligas/`

**Funcionalidades**:
- Gestión de deportes
- Gestión de ligas deportivas
- Asociación deporte-liga
- Responsables y contactos
- Galería de imágenes
- Estados de liga (EN_PREPARACION, EN_CURSO, PAUSADO, CANCELADO)

**APIs Deportes**:
- `GET /assets/API/deportes/listar.php` - Listar deportes

**APIs Ligas**:
- `POST /assets/API/ligas/crear.php` - Crear liga
- `POST /assets/API/ligas/actualizar.php` - Actualizar liga
- `POST /assets/API/ligas/eliminar.php` - Eliminar liga
- `GET /assets/API/ligas/listar.php` - Listar ligas

### 5. **Eventos**

**Ubicación**: `pages/eventos/`, `assets/API/eventos/`

**Funcionalidades**:
- Creación de eventos
- Enlaces multimedia (videos, imágenes, documentos)
- Categorización
- Destacados
- Gestión de cupos e inscripciones

**APIs**:
- `POST /assets/API/eventos/crear.php` - Crear evento
- `POST /assets/API/eventos/editar.php` - Editar evento
- `POST /assets/API/eventos/eliminar.php` - Eliminar evento
- `GET /assets/API/eventos/listar.php` - Listar eventos

### 6. **Banners**

**Ubicación**: `pages/banners/`, `assets/API/banners/`

**Funcionalidades**:
- Gestión de banners informativos
- Tipos: EVENTO, INFORMATIVO, URGENTE, PROMOCIONAL
- Ubicaciones: HOME, EVENTOS, CLUBES, GENERAL
- Fechas de inicio/fin
- Control de orden

**APIs**:
- `POST /assets/API/banners/crear.php` - Crear banner
- `POST /assets/API/banners/editar.php` - Editar banner
- `POST /assets/API/banners/eliminar.php` - Eliminar banner
- `GET /assets/API/banners/listar.php` - Listar banners

### 7. **Mi Club** (Panel Director)

**Ubicación**: `pages/mi-club/`

**Funcionalidades**:
- Vista del club asignado
- Edición de información (sin imágenes)
- Gestión de directiva
- **Solo lectura de imágenes** (administradas por admins)

**Permisos**:
- ✅ Ver información del club
- ✅ Editar datos básicos
- ✅ Gestionar directiva
- ❌ Editar imágenes (solo admins)

### 8. **Mi Ministerio** (Panel Director)

**Ubicación**: `pages/mi-ministerio/`

**Funcionalidades**:
- Vista del ministerio asignado
- Edición de información (sin imágenes)
- Gestión de directiva
- **Solo lectura de imágenes** (administradas por admins)

**Permisos**:
- ✅ Ver información del ministerio
- ✅ Editar datos básicos
- ✅ Gestionar directiva
- ❌ Editar imágenes (solo admins)

---

## 🗄️ Base de Datos

### Tablas del Sistema

#### **SYSTEM_CAT_USUARIOS** (Roles)
```sql
ID, NOMBRE, DESCRIPCION, ACTIVO, FECHA_CREACION
```

#### **SYSTEM_USUARIOS** (Usuarios)
```sql
ID, NOMBRE, PASS, EMAIL, ID_CAT, ID_CLUB_ASIGNADO,
ID_MINISTERIO_ASIGNADO, ACTIVO, TOKEN, ULTIMO_ACCESO, FECHA_CREACION
```

#### **SYSTEM_MODULOS** (Módulos)
```sql
ID, NOMBRE, SLUG, DESCRIPCION, ICONO, ORDEN, ACTIVO
```

#### **SYSTEM_PERMISOS** (Permisos)
```sql
ID, NOMBRE, SLUG, DESCRIPCION
```

#### **SYSTEM_ROL_MODULO_PERMISOS** (Relación Rol-Módulo-Permiso)
```sql
ID, ID_ROL, ID_MODULO, ID_PERMISO
```

#### **SYSTEM_AUDITORIA** (Auditoría)
```sql
ID, ID_USUARIO, MODULO, ACCION, DESCRIPCION, FECHA, IP
```

### Tablas de VRE (Vida Estudiantil)

#### **VRE_GALERIA** (Galería Centralizada) ⭐
```sql
ID, MODULO, ID_REGISTRO, TITULO, URL_IMAGEN, TIPO, ORDEN,
ACTIVO, SUBIDO_POR, FECHA_SUBIDA
```

**Tipos de imagen**:
- `principal`: Imagen principal/logo
- `galeria`: Imagen de galería
- `banner`: Imagen de banner
- `evento`: Imagen de evento

**Módulos soportados**:
- `clubes`
- `ministerios`
- `deportes`
- `ligas`
- `eventos`
- `banners`

#### **VRE_CLUBES**
```sql
ID, NOMBRE, DESCRIPCION, OBJETIVO, REQUISITOS, BENEFICIOS,
HORARIO, DIA_REUNION, LUGAR, CUPO_MAXIMO, CUPO_ACTUAL,
RESPONSABLE_NOMBRE, RESPONSABLE_CONTACTO, EMAIL, TELEFONO,
ID_DIRECTOR_USUARIO, REDES_SOCIALES, ACTIVO, ORDEN, FECHA_CREACION
```
**Nota**: ❌ Ya NO tiene campos `IMAGEN_URL` ni `GALERIA`

#### **VRE_MINISTERIOS**
```sql
ID, NOMBRE, DESCRIPCION, OBJETIVO, REQUISITOS, BENEFICIOS,
HORARIO, DIA_REUNION, LUGAR, CUPO_MAXIMO, CUPO_ACTUAL, TELEFONO,
ID_DIRECTOR_USUARIO, ACTIVO, FECHA_CREACION
```
**Nota**: ❌ Ya NO tiene campos `IMAGEN_URL` ni `GALERIA`

#### **VRE_DEPORTES**
```sql
ID, NOMBRE, DESCRIPCION, ACTIVO, ORDEN
```
**Nota**: ❌ Ya NO tiene campo `IMAGEN_URL`

#### **VRE_LIGAS**
```sql
ID, ID_DEPORTE, NOMBRE, FECHA_INICIO, DESCRIPCION, REQUISITOS,
RESPONSABLE_NOMBRE, RESPONSABLE_CONTACTO, FOTO_RESPONSABLE,
EMAIL, TELEFONO, ACTIVO, ESTADO, ORDEN, FECHA_CREACION
```
**Nota**: ❌ Ya NO tiene campos `IMAGEN_URL` ni `GALERIA`

#### **VRE_EVENTOS**
```sql
ID, TITULO, DESCRIPCION, FECHA, HORA, LUGAR, CATEGORIA,
ORGANIZADOR, CUPO_MAXIMO, REQUIERE_INSCRIPCION, ACTIVO,
DESTACADO, FECHA_CREACION
```

#### **VRE_BANNERS**
```sql
ID, TITULO, DESCRIPCION, IMAGEN_URL, ENLACE, TIPO, UBICACION,
FECHA_INICIO, FECHA_FIN, ACTIVO, ORDEN, FECHA_CREACION
```

### Diagrama Relacional Simplificado

```
SYSTEM_USUARIOS ─┬─> VRE_CLUBES (ID_DIRECTOR_USUARIO)
                 ├─> VRE_MINISTERIOS (ID_DIRECTOR_USUARIO)
                 └─> VRE_GALERIA (SUBIDO_POR)

VRE_CLUBES ─────> VRE_DIRECTIVA_CLUBES
VRE_MINISTERIOS ─> VRE_DIRECTIVA_MINISTERIOS
VRE_DEPORTES ───> VRE_LIGAS
VRE_EVENTOS ────> VRE_EVENTOS_ENLACES

VRE_GALERIA ─┬─> VRE_CLUBES (MODULO='clubes', ID_REGISTRO=ID)
             ├─> VRE_MINISTERIOS (MODULO='ministerios', ID_REGISTRO=ID)
             ├─> VRE_DEPORTES (MODULO='deportes', ID_REGISTRO=ID)
             ├─> VRE_LIGAS (MODULO='ligas', ID_REGISTRO=ID)
             ├─> VRE_EVENTOS (MODULO='eventos', ID_REGISTRO=ID)
             └─> VRE_BANNERS (MODULO='banners', ID_REGISTRO=ID)
```

---

## 🔐 Sistema de Permisos

### Arquitectura de Permisos

El sistema utiliza un **modelo de permisos granulares** basado en:
1. **Roles** (SYSTEM_CAT_USUARIOS)
2. **Módulos** (SYSTEM_MODULOS)
3. **Permisos** (SYSTEM_PERMISOS)
4. **Relaciones** (SYSTEM_ROL_MODULO_PERMISOS)

### Permisos Disponibles

| Permiso | Slug | Descripción |
|---------|------|-------------|
| Ver | `ver` | Visualizar información |
| Crear | `crear` | Crear nuevos registros |
| Editar | `editar` | Modificar registros |
| Eliminar | `eliminar` | Eliminar registros |

### Validación en Template.php

```php
// Verificar si el usuario tiene permiso
$temp->tiene_permiso('clubes', 'crear');  // true/false

// Validar sesión
$temp->validate_session();

// Registrar acción en auditoría
$temp->registrar_auditoria('CLUBES', 'CREAR', 'Club X creado');
```

---

## 🔌 APIs Implementadas

### Formato de Respuesta

Todas las APIs retornan JSON con el siguiente formato:

**Éxito**:
```json
{
    "success": 1,
    "message": "Operación exitosa",
    "data": { ... },
    "id": 123
}
```

**Error**:
```json
{
    "success": 0,
    "message": "Descripción del error",
    "error": "Detalles técnicos (opcional)"
}
```

### Listado Completo de APIs

#### Clubes
- `POST /assets/API/clubes/crear.php`
- `POST /assets/API/clubes/editar.php`
- `POST /assets/API/clubes/eliminar.php`
- `GET /assets/API/clubes/listar.php`

#### Ministerios
- `POST /assets/API/ministerios/crear.php`
- `POST /assets/API/ministerios/editar.php`
- `POST /assets/API/ministerios/eliminar.php`
- `GET /assets/API/ministerios/listar.php`

#### Deportes
- `GET /assets/API/deportes/listar.php`

#### Ligas
- `POST /assets/API/ligas/crear.php`
- `POST /assets/API/ligas/actualizar.php`
- `POST /assets/API/ligas/eliminar.php`
- `GET /assets/API/ligas/listar.php`
- `GET /assets/API/ligas/obtener.php`

#### Eventos
- `POST /assets/API/eventos/crear.php`
- `POST /assets/API/eventos/editar.php`
- `POST /assets/API/eventos/eliminar.php`
- `GET /assets/API/eventos/listar.php`

#### Banners
- `POST /assets/API/banners/crear.php`
- `POST /assets/API/banners/editar.php`
- `POST /assets/API/banners/eliminar.php`
- `GET /assets/API/banners/listar.php`

#### Upload
- `POST /assets/API/upload.php` - Subida general de imágenes

#### Mi-Club
- `POST /assets/API/mi-club/actualizar.php`
- `GET /assets/API/mi-club/listar-miembros.php`
- `POST /assets/API/mi-club/agregar-miembro.php`
- `POST /assets/API/mi-club/actualizar-miembro.php`
- `POST /assets/API/mi-club/eliminar-miembro.php`

#### Mi-Ministerio
- `POST /assets/API/mi-ministerio/actualizar.php`

---

## 🖼️ Sistema de Galería Centralizada

### Concepto

Antes cada módulo tenía sus propios campos de imagen (`IMAGEN_URL`, `GALERIA`). Ahora **todas las imágenes** se gestionan centralizadamente en la tabla `VRE_GALERIA`.

### Ventajas

✅ **Centralización**: Una sola tabla para todas las imágenes
✅ **Auditoría**: Se sabe quién subió cada imagen y cuándo
✅ **Flexibilidad**: Fácil agregar/eliminar imágenes sin modificar tablas
✅ **Consistencia**: Misma estructura para todos los módulos
✅ **Escalabilidad**: Fácil agregar nuevos tipos de imagen

### Estructura de VRE_GALERIA

```sql
CREATE TABLE VRE_GALERIA(
    ID INT PRIMARY KEY AUTO_INCREMENT,
    MODULO VARCHAR(50) NOT NULL,        -- 'clubes', 'ministerios', etc.
    ID_REGISTRO INT NOT NULL,            -- ID del club/ministerio/etc.
    TITULO VARCHAR(200),
    URL_IMAGEN VARCHAR(500) NOT NULL,
    TIPO ENUM('principal', 'galeria', 'banner', 'evento'),
    ORDEN INT DEFAULT 0,
    ACTIVO ENUM('S','N') DEFAULT 'S',
    SUBIDO_POR INT,                      -- ID del usuario que subió
    FECHA_SUBIDA DATETIME,
    INDEX idx_modulo_registro (MODULO, ID_REGISTRO)
)
```

### Ejemplo de Consulta

```php
// Obtener imágenes de un club
$imagenes_query = $db->query("
    SELECT URL_IMAGEN, TIPO, TITULO, ORDEN
    FROM VRE_GALERIA
    WHERE MODULO = 'clubes'
    AND ID_REGISTRO = 12
    AND ACTIVO = 'S'
    ORDER BY ORDEN ASC
");

while ($img = $imagenes_query->fetch_assoc()) {
    if ($img['TIPO'] == 'principal') {
        $imagen_principal = $img['URL_IMAGEN'];
    } else {
        $imagenes_galeria[] = $img;
    }
}
```

### Uso en APIs

**Crear Club con Imagen**:
```php
// 1. Insertar club
INSERT INTO VRE_CLUBES(NOMBRE, DESCRIPCION, ...) VALUES (...);
$club_id = $db->insert_id;

// 2. Insertar imagen en galería
INSERT INTO VRE_GALERIA(MODULO, ID_REGISTRO, URL_IMAGEN, TIPO, ORDEN)
VALUES ('clubes', $club_id, 'http://.../imagen.jpg', 'principal', 1);
```

**Listar Club con Imágenes**:
```php
// Retorna:
{
    "ID": 12,
    "NOMBRE": "Guias Mayores",
    "IMAGEN_PRINCIPAL": "http://.../logo.jpg",
    "IMAGENES": [
        {"URL_IMAGEN": "http://.../1.jpg", "TIPO": "galeria", "ORDEN": 2},
        {"URL_IMAGEN": "http://.../2.jpg", "TIPO": "galeria", "ORDEN": 3}
    ],
    "TOTAL_IMAGENES": 2
}
```

---

## 📥 Instalación

### Requisitos

- PHP 7.4 o superior
- MySQL 8.0 o superior
- Servidor web (Apache/Nginx)
- MAMP, XAMPP, o similar (para desarrollo)

### Pasos de Instalación

1. **Clonar/Copiar el proyecto**
   ```bash
   cd /ruta/servidor/htdocs/
   cp -r vida-estudiantil_Hithan ./
   ```

2. **Crear base de datos**
   ```bash
   mysql -u root -p < SISTEMA_VRE_COMPLETO.sql
   ```

   O desde MySQL Workbench/phpMyAdmin, importar `SISTEMA_VRE_COMPLETO.sql`

3. **Configurar conexión a BD**

   Editar `/assets/php/template.php` línea 4:
   ```php
   parent::__construct(
       'localhost',           // host
       'usuario_db',          // usuario
       'contraseña',          // password
       'pruebasumadmin',      // nombre_bd
       3306,                  // puerto
       '/ruta/mysql.sock'     // socket (opcional)
   );
   ```

4. **Configurar permisos de carpeta uploads**
   ```bash
   chmod 755 assets/uploads
   ```

5. **Acceder al sistema**
   ```
   http://localhost/vida-estudiantil_Hithan/login.php
   ```

### Usuarios por Defecto

| Usuario | Contraseña | Rol |
|---------|------------|-----|
| Suriel | 1234 | Superusuario |
| admin | hikari_1950 | Administrador |

**IMPORTANTE**: Cambiar contraseñas en producción.

---

## 📘 Guía de Uso

### Para Administradores

1. **Acceder al panel**
   - Login con usuario administrador
   - Dashboard muestra módulos disponibles

2. **Gestionar Clubes**
   - Ir a "Clubes"
   - Clic en "Nuevo Club"
   - Completar formulario
   - Opción: Asignar director (existente o crear nuevo)
   - Subir logo (guardado en VRE_GALERIA)
   - Guardar

3. **Gestionar Galería**
   - En formulario de edición, sección "Galería"
   - Clic en "Agregar Imagen a la Galería"
   - Seleccionar imagen
   - Se guarda automáticamente en VRE_GALERIA

4. **Ver Auditoría**
   - Todas las acciones quedan registradas en `SYSTEM_AUDITORIA`
   - Consultar con: `SELECT * FROM SYSTEM_AUDITORIA ORDER BY FECHA DESC`

### Para Directores de Club

1. **Acceder a Mi Club**
   - Login con usuario director de club
   - Automáticamente redirige a "Mi Club"

2. **Editar Información**
   - Solo puede editar: descripción, objetivo, horarios, etc.
   - **NO puede editar imágenes** (mensaje informativo)

3. **Gestionar Directiva**
   - Clic en "Gestionar Directiva"
   - Completar cargos disponibles
   - Guardar cambios

### Para Directores de Ministerio

Similar a directores de club, pero para ministerios.

---

## 🔄 Migraciones Realizadas

### Migración a VRE_GALERIA

Se migraron todas las imágenes de los módulos al sistema centralizado:

#### 1. **Ministerios**
- **Registros**: 1 ministerio
- **Imágenes migradas**: 1 imagen principal
- **Scripts**:
  - `migrar_imagenes_galeria.php` (EJECUTADO Y ELIMINADO)
- **Cambios en BD**:
  ```sql
  ALTER TABLE VRE_MINISTERIOS
    DROP COLUMN IMAGEN_URL,
    DROP COLUMN GALERIA;
  ```

#### 2. **Clubes**
- **Registros**: 1 club (Guias Mayores)
- **Imágenes migradas**: 4 imágenes (1 principal + 3 galería)
- **Scripts**:
  - `migrar_clubes_galeria.php` (EJECUTADO Y ELIMINADO)
- **Cambios en BD**:
  ```sql
  ALTER TABLE VRE_CLUBES
    DROP COLUMN IMAGEN_URL,
    DROP COLUMN GALERIA;
  ```

#### 3. **Deportes**
- **Registros**: 3 deportes
- **Imágenes migradas**: 0 (sin imágenes)
- **Scripts**:
  - `migrar_deportes_galeria.php` (EJECUTADO Y ELIMINADO)
- **Cambios en BD**:
  ```sql
  ALTER TABLE VRE_DEPORTES
    DROP COLUMN IMAGEN_URL;
  ```

#### 4. **Ligas**
- **Registros**: 1 liga
- **Imágenes migradas**: 2 imágenes (1 principal + 1 galería)
- **Scripts**:
  - `migrar_ligas_galeria.php` (EJECUTADO Y ELIMINADO)
- **Cambios en BD**:
  ```sql
  ALTER TABLE VRE_LIGAS
    DROP COLUMN IMAGEN_URL,
    DROP COLUMN GALERIA;
  ```

### Resumen de Migración

| Módulo | Imágenes Migradas | Estado |
|--------|-------------------|--------|
| Ministerios | 1 | ✅ Completo |
| Clubes | 4 | ✅ Completo |
| Deportes | 0 | ✅ Completo |
| Ligas | 2 | ✅ Completo |
| **TOTAL** | **7** | ✅ **100%** |

### Validación Post-Migración

```sql
-- Ver todas las imágenes migradas
SELECT
    MODULO,
    COUNT(*) as total_imagenes,
    SUM(CASE WHEN TIPO='principal' THEN 1 ELSE 0 END) as principales,
    SUM(CASE WHEN TIPO='galeria' THEN 1 ELSE 0 END) as galeria
FROM VRE_GALERIA
GROUP BY MODULO;

-- Resultado esperado:
-- clubes     | 4 | 1 | 3
-- ministerios | 1 | 1 | 0
-- ligas      | 2 | 1 | 1
```

---

## 🚀 Próximos Pasos (Sugerencias)

### Funcionalidades Pendientes

1. **Frontend Público**
   - Vista pública de clubes/ministerios/eventos
   - Sistema de inscripciones

2. **Notificaciones**
   - Email automático al crear director
   - Notificaciones de eventos

3. **Reportes**
   - Estadísticas de participación
   - Reportes de auditoría

4. **Módulos Adicionales**
   - Anuarios
   - Federación Estudiantil
   - Vida Campus (Amenidades)

5. **Mejoras Técnicas**
   - API RESTful completa con autenticación JWT
   - Caché de consultas frecuentes
   - Optimización de imágenes automática

---

## 📞 Soporte

Para soporte técnico o preguntas:
- **Desarrollador**: [Tu nombre]
- **Email**: [Tu email]
- **Universidad**: Universidad de Montemorelos
- **Departamento**: Vida Estudiantil

---

## 📄 Licencia

© 2025 Universidad de Montemorelos. Todos los derechos reservados.

Este sistema es propiedad de la Universidad de Montemorelos y fue desarrollado exclusivamente para uso interno de la institución.

---

**Última actualización**: Noviembre 2025
**Versión**: 1.0.0
**Estado**: Producción
