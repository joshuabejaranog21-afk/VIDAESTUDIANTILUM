# 🔐 Sistema de Gestión de Permisos por Módulos

## 📋 Descripción

Este sistema permite al **Super Administrador** gestionar usuarios y asignar permisos específicos por módulos del sistema, con credenciales temporales y cambio obligatorio de contraseña en el primer login.

---

## 🚀 Instalación

### 1. Ejecutar el Script SQL

Primero, ejecuta el script SQL para agregar el campo `PRIMER_LOGIN` a la tabla de usuarios:

```bash
Archivo: /assets/db/AGREGAR_PRIMER_LOGIN.sql
```

**Ejecuta este script en phpMyAdmin o MySQL:**

```sql
USE pruebasumadmin;

ALTER TABLE SYSTEM_USUARIOS
ADD COLUMN IF NOT EXISTS PRIMER_LOGIN ENUM('S','N') DEFAULT 'S' AFTER ACTIVO;

UPDATE SYSTEM_USUARIOS SET PRIMER_LOGIN = 'N' WHERE ID IN (1, 2);
```

---

## 📌 Funcionalidades Implementadas

### ✅ 1. Gestión de Permisos por Módulo

**Ubicación:** `/cpanel/pages/configuracion/permisos/`

El Super Admin puede:
- Asignar permisos específicos a cada **rol** (no a usuarios individuales)
- Controlar acceso por módulo: Clubes, Ministerios, Deportes, Eventos, etc.
- Asignar permisos granulares: **Ver**, **Crear**, **Editar**, **Eliminar**

**Cómo usar:**
1. Accede a **Configuración → Permisos**
2. Selecciona un **rol** (ej: EDITOR_EVENTOS)
3. Activa/desactiva los permisos por módulo haciendo clic en las etiquetas
4. Haz clic en **Guardar Cambios**

---

### ✅ 2. Creación de Usuarios con Credenciales Temporales

**Ubicación:** `/cpanel/pages/configuracion/usuarios/`

**Proceso:**
1. El Super Admin crea un usuario
2. Asigna una **contraseña temporal** (ej: `1234`)
3. Asigna un **rol** con permisos específicos
4. El usuario se crea automáticamente con `PRIMER_LOGIN = 'S'`

**Ejemplo:**
- Usuario: `juan.perez`
- Contraseña temporal: `temporal123`
- Rol: `EDITOR_CLUBES`

---

### ✅ 3. Cambio Obligatorio de Contraseña (Primer Login)

**Ubicación:** `/cpanel/pages/configuracion/primer-login/`

**Flujo automático:**
1. Usuario ingresa con credenciales temporales
2. El sistema detecta `PRIMER_LOGIN = 'S'`
3. Redirige **automáticamente** a la página de cambio de contraseña
4. El usuario **NO puede acceder** al sistema hasta cambiar su contraseña
5. Al cambiar la contraseña, se marca `PRIMER_LOGIN = 'N'`

---

## 🎯 Roles Predefinidos

| Rol | Descripción | Acceso |
|-----|-------------|--------|
| **SUPERUSUARIO** | Acceso total | Todos los módulos y permisos |
| **ADMINISTRADOR** | Admin general | Todos los módulos (configurable) |
| **EDITOR_INVOLUCRATE** | Editor de Clubes/Ministerios/Deportes | Solo módulos asignados |
| **EDITOR_EVENTOS** | Editor de eventos | Solo Eventos y multimedia |
| **VISUALIZADOR** | Solo lectura | Solo permiso "Ver" |
| **DIRECTOR_CLUB** | Director de club | Su club asignado únicamente |
| **DIRECTOR_MINISTERIO** | Director de ministerio | Su ministerio asignado |

---

## 🔧 Módulos del Sistema

Los módulos disponibles son:

- 📊 **Dashboard** - Panel principal
- 👥 **Usuarios** - Gestión de usuarios
- ⭐ **Clubes** - Clubes estudiantiles
- ❤️ **Ministerios** - Ministerios
- 🏃 **Deportes** - Gestión deportiva
- 🏆 **Ligas** - Ligas deportivas
- 🏢 **Instalaciones** - Instalaciones deportivas
- 🎓 **Co-Curriculares** - Servicios co-curriculares
- 📅 **Eventos** - Eventos y multimedia
- 🖼️ **Banners** - Banners informativos
- 📸 **Galería** - Galería de imágenes

---

## 🛡️ Seguridad Implementada

### 1. Control de Acceso por Rol
```php
// En cada página PHP
if (!$temp->tiene_permiso('clubes', 'editar')) {
    header('Location: ' . $temp->siteURL);
    exit();
}
```

### 2. Validación de Primer Login
- El sistema detecta automáticamente si es el primer ingreso
- Redirige a cambio de contraseña obligatorio
- No permite navegación hacia atrás

### 3. Contraseñas Seguras
- Encriptadas con MD5 (considera mejorar a bcrypt/argon2)
- Validación de coincidencia
- Mínimo 6 caracteres

---

## 📂 Archivos Creados/Modificados

### ✅ Archivos Nuevos

**Base de Datos:**
- `/assets/db/AGREGAR_PRIMER_LOGIN.sql`

**APIs:**
- `/assets/API/permisos/obtener-permisos-usuario.php`
- `/assets/API/permisos/actualizar-permisos-usuario.php`
- `/assets/API/usuarios/cambiarPasswordPrimerLogin.php`

**Páginas:**
- `/pages/configuracion/permisos/index.php`
- `/pages/configuracion/primer-login/index.php`

### ✏️ Archivos Modificados

- `/assets/API/sesion/iniciar.php` - Detecta primer login
- `/login.php` - Redirige si es primer login

---

## 🎮 Casos de Uso

### Caso 1: Crear un Editor de Clubes

**Super Admin:**
1. Va a **Configuración → Usuarios**
2. Click en **Nuevo Usuario**
3. Llena el formulario:
   - Nombre: `editor.clubes`
   - Contraseña: `temp123`
   - Rol: `EDITOR_INVOLUCRATE`
4. Guarda

**Asignar permisos:**
1. Va a **Configuración → Permisos**
2. Selecciona rol `EDITOR_INVOLUCRATE`
3. En el módulo **Clubes**, activa: Ver, Crear, Editar
4. Guarda cambios

**Usuario final:**
1. Ingresa con `editor.clubes` / `temp123`
2. El sistema lo redirige a cambiar contraseña
3. Cambia a una contraseña personal
4. Accede solo al módulo de **Clubes** con los permisos asignados

---

### Caso 2: Crear un Visualizador General

**Super Admin:**
1. Crea usuario con rol `VISUALIZADOR`
2. En Permisos, activa solo **Ver** en todos los módulos
3. Usuario puede ver todo pero no modificar nada

---

## 🔍 Validación de Permisos en el Código

### Método 1: En las páginas PHP

```php
// Verificar si tiene permiso para editar clubes
if (!$temp->tiene_permiso('clubes', 'editar')) {
    header('Location: ' . $temp->siteURL);
    exit();
}
```

### Método 2: En los botones del frontend

```php
<?php if ($temp->tiene_permiso('clubes', 'crear')): ?>
    <button class="btn btn-primary" onclick="crear()">
        <i class="fa fa-plus"></i> Crear Club
    </button>
<?php endif; ?>
```

### Método 3: SUPERUSUARIO siempre tiene acceso

```php
// El SUPERUSUARIO (categoria = 1) tiene acceso automático a todo
if ($temp->usuario_categoria == 1) {
    // Acceso total
}
```

---

## ⚠️ Notas Importantes

1. **Solo el SUPERUSUARIO** puede acceder a la gestión de permisos
2. Los permisos se asignan a **ROLES**, no a usuarios individuales
3. Un usuario hereda los permisos de su rol asignado
4. El cambio de contraseña en primer login es **obligatorio**
5. La contraseña temporal debe ser comunicada al usuario de forma segura

---

## 🔄 Flujo Completo

```
Super Admin crea usuario con contraseña temporal
           ↓
Usuario ingresa por primera vez
           ↓
Sistema detecta PRIMER_LOGIN = 'S'
           ↓
Redirige a /configuracion/primer-login/
           ↓
Usuario cambia contraseña
           ↓
PRIMER_LOGIN se marca como 'N'
           ↓
Usuario accede al sistema con permisos de su rol
```

---

## 📞 Soporte

Si tienes dudas sobre el sistema de permisos, consulta:
- Este documento
- Archivo: `/DOCUMENTACION_SISTEMA_VRE.md`
- Código fuente en `/assets/php/template.php` (métodos de permisos)

---

## ✨ Mejoras Futuras Sugeridas

1. **Permisos por usuario individual** (actualmente solo por rol)
2. **Auditoría de cambios** de permisos
3. **Contraseñas más seguras** (bcrypt en lugar de MD5)
4. **Expiración de contraseñas** temporales
5. **Historial de accesos** por usuario
6. **Notificaciones** al crear usuarios nuevos

---

**Sistema desarrollado para Universidad de Montemorelos - Vida Estudiantil**
