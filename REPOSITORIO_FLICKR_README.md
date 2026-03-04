# Repositorio de Fotos con Flickr - Guía Completa

## ✨ ¿Qué se implementó?

El **módulo de Repositorio Personal de Fotos** ahora soporta **URLs de Flickr y BBCode** además de la subida de archivos tradicional.

---

## 🎯 Características Principales

✅ **Dos métodos de subida:**
- **Subir archivo local** (método tradicional)
- **Usar URL de Flickr** (NUEVO)

✅ **Acepta múltiples formatos:**
- URL directa de imagen: `https://live.staticflickr.com/.../foto.jpg`
- BBCode completo de Flickr: `[url=...][img]...[/img][/url]`

✅ **Extracción automática:** El sistema extrae automáticamente la URL de la imagen del BBCode

✅ **Vista previa:** Muestra la imagen antes de guardar

---

## 📁 Archivos Creados/Modificados

### 1. Nueva API para Flickr

**Archivo:** `assets/API/repositorio/upload-flickr.php`

Esta API maneja la subida mediante URLs de Flickr:
- Acepta URLs directas
- Acepta BBCode y extrae la URL automáticamente
- Valida que la URL sea válida
- No ocupa espacio en el servidor

### 2. Interfaz Actualizada

**Archivo:** `pages/repositorio/index.php` (modificado)

Cambios:
- Toggle entre "Subir Archivo" y "URL de Flickr"
- Campo de texto para pegar BBCode o URL
- Modal de ayuda explicando cómo obtener enlaces de Flickr
- Vista previa automática de la imagen de Flickr

---

## 🚀 Cómo Usar

### Método 1: Subir desde BBCode de Flickr (MÁS FÁCIL)

#### Paso 1: Obtener el BBCode
1. Abre la foto en Flickr
2. Busca el botón **"Share"** o el ícono de compartir
3. Selecciona **"BBCode"** o **"Grab the link"**
4. Copia TODO el código BBCode que aparece

**Ejemplo de BBCode:**
```
[url=https://flic.kr/p/2pLPh8f][img]https://live.staticflickr.com/65535/53672048540_c0d98ee61b_5k.jpg[/img][/url][url=https://flic.kr/p/2pLPh8f]Semana 15 Primavera 2024[/url] by [url=https://www.flickr.com/photos/universidaddemontemorelos/]Universidad de Montemorelos[/url], on Flickr
```

#### Paso 2: Pegar en el Sistema
1. Ve a: `http://localhost/vidaEstudiantil/pages/repositorio/`
2. Ingresa tu matrícula
3. Haz clic en **"Subir Foto"**
4. Selecciona **"URL de Flickr"**
5. Pega TODO el BBCode en el campo de texto
6. Completa los demás datos (título, descripción, etc.)
7. Haz clic en **"Subir Fotografía"**

El sistema automáticamente extraerá la URL de la imagen del BBCode:
```
De esto: [url=...][img]https://live.staticflickr.com/.../foto.jpg[/img][/url]...
A esto:   https://live.staticflickr.com/.../foto.jpg
```

---

### Método 2: Subir desde URL Directa

#### Paso 1: Obtener la URL Directa
1. Abre la foto en Flickr
2. Haz **clic derecho** sobre la imagen
3. Selecciona **"Copiar dirección de imagen"**

Obtendrás algo como:
```
https://live.staticflickr.com/65535/53672048540_c0d98ee61b_5k.jpg
```

#### Paso 2: Pegar en el Sistema
1. Ve a: `http://localhost/vidaEstudiantil/pages/repositorio/`
2. Ingresa tu matrícula
3. Haz clic en **"Subir Foto"**
4. Selecciona **"URL de Flickr"**
5. Pega la URL directa
6. Completa los demás datos
7. Haz clic en **"Subir Fotografía"**

---

### Método 3: Subir Archivo Local (Tradicional)

Si prefieres no usar Flickr:

1. Ve a: `http://localhost/vidaEstudiantil/pages/repositorio/`
2. Ingresa tu matrícula
3. Haz clic en **"Subir Foto"**
4. Deja seleccionado **"Subir Archivo"**
5. Selecciona un archivo de tu computadora (máx. 5MB)
6. Completa los demás datos
7. Haz clic en **"Subir Fotografía"**

---

## 📸 Ejemplo Real de Uso

### Tu BBCode de Flickr:
```
[url=https://flic.kr/p/2pLPh8f][img]https://live.staticflickr.com/65535/53672048540_c0d98ee61b_5k.jpg[/img][/url][url=https://flic.kr/p/2pLPh8f]Semana 15 Primavera 2024[/url] by [url=https://www.flickr.com/photos/universidaddemontemorelos/]Universidad de Montemorelos[/url], on Flickr
```

### Lo que el Sistema Extrae Automáticamente:
```
https://live.staticflickr.com/65535/53672048540_c0d98ee61b_5k.jpg
```

### Lo que se Guarda en la Base de Datos:
```sql
INSERT INTO VRE_REPOSITORIO_FOTOS (
    MATRICULA,
    FOTO_URL,
    TITULO,
    DESCRIPCION
) VALUES (
    '1220593',
    'https://live.staticflickr.com/65535/53672048540_c0d98ee61b_5k.jpg',
    'Semana 15 Primavera 2024',
    'Foto del evento'
);
```

---

## 🎨 Interfaz del Usuario

### Toggle de Métodos

Cuando el usuario hace clic en "Subir Foto", ve dos opciones:

```
┌─────────────────┬──────────────────┐
│ 📤 Subir Archivo │ 🔗 URL de Flickr │
└─────────────────┴──────────────────┘
```

### Vista con "Subir Archivo" seleccionado:
```
┌─────────────────────────────────────┐
│ Fotografía (Máx. 5MB - JPG, PNG)   │
│ [Seleccionar archivo...]            │
└─────────────────────────────────────┘
```

### Vista con "URL de Flickr" seleccionado:
```
┌─────────────────────────────────────────────┐
│ URL de Flickr o BBCode                      │
│ ┌─────────────────────────────────────────┐ │
│ │ Pega aquí el enlace directo o BBCode   │ │
│ │                                         │ │
│ │ Ejemplos:                               │ │
│ │ 1. URL: https://live.staticflickr...   │ │
│ │ 2. BBCode: [url=...][img]...[/img]...  │ │
│ └─────────────────────────────────────────┘ │
│ Tip: Puedes pegar el BBCode completo       │
│ ¿Cómo obtener esto?                         │
└─────────────────────────────────────────────┘
```

---

## 🔧 Estructura de la Base de Datos

La tabla `VRE_REPOSITORIO_FOTOS` almacena las fotos:

```sql
CREATE TABLE VRE_REPOSITORIO_FOTOS(
    ID INT PRIMARY KEY AUTO_INCREMENT,
    ID_ESTUDIANTE INT,
    MATRICULA VARCHAR(7),
    TITULO VARCHAR(200),
    DESCRIPCION TEXT,
    FOTO_URL VARCHAR(500),  -- Aquí se guarda la URL de Flickr
    TIPO_FOTO ENUM('INDIVIDUAL','GRUPAL','EVENTO','ACADEMICA','OTRA'),
    FECHA_FOTO DATE,
    PRIVADA ENUM('S','N'),
    FECHA_SUBIDA DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

**Nota:** El campo `FOTO_URL` puede contener:
- Ruta local: `/vidaEstudiantil/uploads/repositorio/1220593/foto.jpg`
- URL de Flickr: `https://live.staticflickr.com/65535/...jpg`

---

## 🛠️ API Reference

### Subir Foto con Archivo (Original)

```
POST /assets/API/repositorio/upload.php
```

**Parámetros (FormData):**
- `matricula` (required) - Matrícula del estudiante
- `foto` (required) - Archivo de imagen
- `titulo` - Título de la foto
- `descripcion` - Descripción
- `tipo_foto` - Tipo (INDIVIDUAL, GRUPAL, etc.)
- `fecha_foto` - Fecha de la foto
- `privada` - S/N
- `nombre`, `apellido`, `carrera`, `semestre` - Datos del estudiante (si es nuevo)

**Response:**
```json
{
    "success": 1,
    "message": "Fotografía subida exitosamente",
    "id": 123,
    "url": "/vidaEstudiantil/uploads/repositorio/1220593/foto.jpg"
}
```

### Subir Foto con URL de Flickr (NUEVO)

```
POST /assets/API/repositorio/upload-flickr.php
```

**Parámetros (POST):**
- `matricula` (required) - Matrícula del estudiante
- `foto_url` (required) - URL directa o BBCode de Flickr
- `titulo` - Título de la foto
- `descripcion` - Descripción
- `tipo_foto` - Tipo (INDIVIDUAL, GRUPAL, etc.)
- `fecha_foto` - Fecha de la foto
- `privada` - S/N
- `nombre`, `apellido`, `carrera`, `semestre` - Datos del estudiante (si es nuevo)

**Response:**
```json
{
    "success": 1,
    "message": "Fotografía de Flickr agregada exitosamente",
    "id": 124,
    "url": "https://live.staticflickr.com/65535/53672048540_c0d98ee61b_5k.jpg"
}
```

---

## 🎯 Ventajas de Usar Flickr

### Para el Administrador:
✅ **Cero espacio en servidor** - Las fotos están en Flickr
✅ **Sin límites de tamaño** - Flickr maneja archivos grandes
✅ **Backups automáticos** - Flickr respalda tus fotos
✅ **CDN global** - Carga rápida desde cualquier parte del mundo
✅ **Fácil de actualizar** - Solo cambias la URL

### Para el Estudiante:
✅ **Carga más rápida** - CDN de Flickr
✅ **Alta calidad** - Fotos en resolución completa
✅ **Siempre disponible** - Respaldo de Flickr

---

## 🔍 Diferencia entre Repositorio y Anuarios

### Repositorio Personal (este módulo):
- **Cada estudiante** sube sus propias fotos
- Fotos personales, eventos, actividades
- Pueden ser **privadas** (solo el dueño las ve)
- Organizadas por matrícula

### Anuarios (módulo anterior):
- **Administradores** suben fotos oficiales
- Foto oficial del estudiante para el anuario
- Siempre **públicas**
- Organizadas por año de anuario

---

## ❓ Preguntas Frecuentes

### ¿Puedo usar ambos métodos (archivo + Flickr)?
Sí, puedes usar ambos. Algunas fotos pueden ser archivos locales y otras URLs de Flickr.

### ¿Qué pasa si pego el BBCode completo?
El sistema automáticamente extrae la URL de la imagen del BBCode. No hay problema.

### ¿Funciona con otros servicios aparte de Flickr?
Sí, puedes usar cualquier URL de imagen pública (Imgur, Dropbox, Google Photos, etc.)

### ¿Las fotos de Flickr ocupan espacio en mi servidor?
No, solo se guarda la URL (unos pocos bytes). La imagen está en Flickr.

### ¿Qué pasa si borro la foto de Flickr?
La imagen dejará de aparecer en tu repositorio ya que solo guardamos el enlace.

### ¿Puedo cambiar de archivo local a Flickr después?
Sí, pero deberás eliminar la foto actual y volver a subirla con la URL de Flickr.

---

## 🔒 Seguridad

### Validaciones Implementadas:
- ✅ Validación de formato de URL
- ✅ Verificación de que sea una imagen válida
- ✅ Sanitización de entradas SQL
- ✅ Autenticación requerida

### Recomendaciones:
- Usa solo fotos públicas de Flickr o de tu cuenta oficial
- No uses URLs de fotos privadas (no funcionarán)
- Verifica que la URL funcione antes de guardar

---

## 📊 Métricas del Sistema

### Comparación de Uso de Espacio:

**Método Tradicional (Archivo):**
- 500 fotos × 2MB promedio = **1 GB de espacio**

**Método Flickr (URL):**
- 500 URLs × 200 bytes = **100 KB de espacio**

**Ahorro:** 99.99% de espacio en servidor 🎉

---

## 🚀 URLs de Acceso

### Para Estudiantes:
```
http://localhost/vidaEstudiantil/pages/repositorio/
```

### APIs:
```
POST /assets/API/repositorio/upload.php         (subida de archivos)
POST /assets/API/repositorio/upload-flickr.php  (URLs de Flickr)
GET  /assets/API/repositorio/listar.php?matricula=1220593
POST /assets/API/repositorio/eliminar.php
```

---

## 🆘 Solución de Problemas

### "La URL proporcionada no es válida"
**Causa:** La URL no tiene formato correcto
**Solución:** Verifica que la URL comience con `http://` o `https://`

### "No se recibió la URL"
**Causa:** El campo está vacío
**Solución:** Pega la URL o BBCode en el campo de texto

### La vista previa no se muestra
**Causa:** La URL puede estar mal o la imagen no es pública
**Solución:** Verifica el enlace en una pestaña nueva del navegador

### "Error al guardar en base de datos"
**Causa:** Problema con la conexión a la BD
**Solución:** Revisa la consola del navegador (F12) para más detalles

---

## 📝 Notas Finales

- Este sistema es compatible con el módulo de Anuarios que creamos anteriormente
- Ambos usan la misma estrategia: guardar URLs en lugar de archivos
- Los estudiantes tienen control total sobre su repositorio personal
- Los administradores gestionan las fotos oficiales de anuarios

---

**Implementado:** 2025
**Versión:** 1.0
**Base de datos:** pruebasumadmin
**Módulo:** Repositorio Personal de Fotos con Flickr
