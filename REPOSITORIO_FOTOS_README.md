# Módulo de Repositorio de Fotografías Personales

## Descripción

Módulo de repositorio de imágenes personales vinculadas a la matrícula del estudiante. Los usuarios registrados solo pueden ver y gestionar las fotografías vinculadas a su propia matrícula.

## Características

### Primera Etapa (Implementado)
- ✅ Vinculación de fotografías a matrícula de estudiante (7 dígitos)
- ✅ Datos del estudiante: nombre, apellido, carrera, semestre
- ✅ Subida de fotografías con metadatos (título, descripción, tipo, fecha)
- ✅ Visualización del repositorio personal
- ✅ Eliminación de fotografías propias
- ✅ Tipos de fotografías: Individual, Grupal, Evento, Académica, Otra
- ✅ Control de privacidad (fotos privadas/públicas)
- ✅ Solo accesible con sesión iniciada

### Segunda Etapa (Preparado en BD)
- 🔄 Referencias de alumnos en fotos grupales
- 🔄 Sistema de etiquetado con coordenadas (tags en fotos)
- 🔄 Confirmación de referencias por parte del estudiante etiquetado

## Instalación

### 1. Base de Datos

Ejecuta el script SQL actualizado en tu base de datos:

```bash
mysql -u root -p pruebasumadmin < assets/db/db.sql
```

O ejecuta las siguientes tablas manualmente:

**Tablas creadas:**
- `VRE_ESTUDIANTES` - Información de estudiantes
- `VRE_REPOSITORIO_FOTOS` - Fotografías del repositorio
- `VRE_REPOSITORIO_REFERENCIAS` - Referencias en fotos grupales (segunda etapa)

### 2. Estructura de Archivos

El módulo ha creado los siguientes archivos:

**Base de Datos:**
- `assets/db/db.sql` - Script SQL actualizado con las nuevas tablas

**APIs:**
- `assets/API/repositorio/upload.php` - Subir fotografías
- `assets/API/repositorio/listar.php` - Listar fotos del estudiante
- `assets/API/repositorio/eliminar.php` - Eliminar fotografías
- `assets/API/repositorio/actualizar-estudiante.php` - Actualizar datos del estudiante

**Interfaz:**
- `pages/repositorio/index.php` - Página principal del repositorio

**Navegación:**
- `assets/php/template.php` - Actualizado con enlace al módulo

### 3. Permisos de Carpetas

Asegúrate de crear y dar permisos a la carpeta de uploads:

```bash
mkdir -p uploads/repositorio
chmod 777 uploads/repositorio
```

## Uso

### Para Estudiantes

1. **Acceder al módulo:**
   - Inicia sesión en el sistema
   - Ve al menú "Repositorio de Fotos"

2. **Ver tu repositorio:**
   - Ingresa tu matrícula de 7 dígitos (ejemplo: 1220593)
   - Haz clic en "Buscar"
   - Verás todas tus fotografías

3. **Subir una fotografía:**
   - Haz clic en "Subir Foto"
   - Completa el formulario:
     - Matrícula (requerido)
     - Tipo de foto (requerido)
     - Nombre y apellido (opcional, se guardan la primera vez)
     - Carrera y semestre (opcional)
     - Título y descripción (opcional)
     - Fecha de la foto (opcional)
     - Fotografía (requerido, máx. 5MB)
     - Privada (opcional)

4. **Eliminar una fotografía:**
   - Haz clic en el botón "Eliminar" en la tarjeta de la foto
   - Confirma la acción

### Validaciones

- Matrícula: 7 dígitos numéricos
- Formatos de imagen: JPG, PNG, GIF
- Tamaño máximo: 5MB
- Solo se pueden eliminar fotos propias

## API Endpoints

### 1. Listar Fotografías
```
GET assets/API/repositorio/listar.php?matricula=1220593
```

**Respuesta:**
```json
{
  "success": 1,
  "estudiante": {
    "id": 1,
    "matricula": "1220593",
    "nombre": "Usuario",
    "apellido": "Ejemplo",
    "carrera": "Ingeniería en Sistemas",
    "semestre": 5
  },
  "fotos": [...],
  "total": 10
}
```

### 2. Subir Fotografía
```
POST assets/API/repositorio/upload.php
```

**Parámetros (FormData):**
- `matricula` (requerido)
- `foto` (requerido - archivo)
- `nombre` (opcional)
- `apellido` (opcional)
- `carrera` (opcional)
- `semestre` (opcional)
- `titulo` (opcional)
- `descripcion` (opcional)
- `tipo_foto` (opcional: INDIVIDUAL, GRUPAL, EVENTO, ACADEMICA, OTRA)
- `fecha_foto` (opcional)
- `privada` (opcional: S/N)

### 3. Eliminar Fotografía
```
POST assets/API/repositorio/eliminar.php
```

**Parámetros:**
```json
{
  "id": 123
}
```

### 4. Actualizar Datos del Estudiante
```
POST assets/API/repositorio/actualizar-estudiante.php
```

**Parámetros:**
```json
{
  "matricula": "1220593",
  "nombre": "Nuevo Nombre",
  "apellido": "Nuevo Apellido",
  "carrera": "Nueva Carrera",
  "semestre": 6
}
```

## Estructura de Base de Datos

### VRE_ESTUDIANTES
- `ID` - ID autoincremental
- `MATRICULA` - Matrícula de 7 dígitos (único)
- `NOMBRE` - Nombre del estudiante
- `APELLIDO` - Apellido del estudiante
- `CARRERA` - Carrera del estudiante
- `SEMESTRE` - Semestre actual
- `EMAIL` - Email del estudiante
- `ACTIVO` - Estado (S/N)
- `FECHA_REGISTRO` - Fecha de registro

### VRE_REPOSITORIO_FOTOS
- `ID` - ID autoincremental
- `ID_ESTUDIANTE` - FK a VRE_ESTUDIANTES
- `MATRICULA` - Matrícula del estudiante
- `TITULO` - Título de la foto
- `DESCRIPCION` - Descripción de la foto
- `FOTO_URL` - URL de la imagen
- `TIPO_FOTO` - Tipo (INDIVIDUAL, GRUPAL, EVENTO, ACADEMICA, OTRA)
- `FECHA_FOTO` - Fecha cuando se tomó la foto
- `TAGS` - JSON con etiquetas
- `ORDEN` - Orden de visualización
- `PRIVADA` - Si es privada (S/N)
- `ACTIVO` - Estado (S/N)
- `FECHA_SUBIDA` - Fecha de subida

### VRE_REPOSITORIO_REFERENCIAS (Segunda Etapa)
- `ID` - ID autoincremental
- `ID_FOTO` - FK a VRE_REPOSITORIO_FOTOS
- `MATRICULA_REFERENCIADA` - Matrícula del estudiante etiquetado
- `NOMBRE_REFERENCIADO` - Nombre del estudiante etiquetado
- `POSICION_X` - Coordenada X del tag (%)
- `POSICION_Y` - Coordenada Y del tag (%)
- `CONFIRMADO` - Si fue confirmado por el estudiante (S/N)
- `FECHA_REFERENCIA` - Fecha de creación de la referencia

## Ejemplo de Matrícula

Se ha incluido un estudiante de ejemplo con la matrícula **1220593**:
- Nombre: Usuario Ejemplo
- Carrera: Ingeniería en Sistemas
- Semestre: 5

## Seguridad

- ✅ Solo usuarios con sesión iniciada pueden acceder
- ✅ Validación de sesión en todas las APIs
- ✅ Validación de tipos de archivo
- ✅ Validación de tamaño de archivo
- ✅ Protección SQL injection con `real_escape_string`
- ✅ Nombres de archivo únicos para evitar sobrescritura
- ✅ Eliminación de archivo físico al eliminar registro

## Próximas Características (Segunda Etapa)

1. **Sistema de Etiquetado:**
   - Etiquetar personas en fotos grupales
   - Coordenadas X,Y del tag en la imagen
   - Notificaciones a estudiantes etiquetados

2. **Confirmación de Referencias:**
   - Estudiantes pueden confirmar que son ellos en la foto
   - Ver todas las fotos donde están etiquetados

3. **Búsqueda Avanzada:**
   - Buscar por tipo de foto
   - Buscar por rango de fechas
   - Buscar por tags

## Soporte

Para reportar problemas o sugerencias, contacta al administrador del sistema.

---

**Versión:** 1.0
**Fecha:** 2025
**Autor:** Sistema VRE - Vida Estudiantil
