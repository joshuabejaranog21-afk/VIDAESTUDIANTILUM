<?php
class Conexion extends mysqli {
    public function __construct(){
        parent::__construct('localhost','root','','pruebasumadmin');
        if ($this->connect_errno) {
            die(' Error con la conexión: '.$this->connect_error);
        }
    }
    public function recorrer($y){
        return mysqli_fetch_array($y);
    }
    public function rows($y){
        return mysqli_num_rows($y);
    }
    
}
class Template {
    public $siteURL = "/cpanel/cpanel_Hithan-main/cpanel/";
    public $titulo;
    public $usuario_id;
    public $usuario_nombre;
    public $usuario_email;
    public $usuario_categoria;
    public $usuario_rol_nombre;
    public $db;

    public function __construct($titulo = "Inicio") {
        $this->titulo = $titulo;
        $this->db = new Conexion();
    }

    /**
     * Valida la sesión del usuario
     * @return bool
     */
    public function validate_session() {
        if(isset($_COOKIE['system_name']) && isset($_COOKIE["system_token"])){
            $cad = "SELECT u.*, c.NOMBRE as ROL_NOMBRE
                    FROM SYSTEM_USUARIOS u
                    LEFT JOIN SYSTEM_CAT_USUARIOS c ON u.ID_CAT = c.ID
                    WHERE u.ACTIVO = 'S'
                    AND u.NOMBRE = '" . $_COOKIE['system_name'] . "'
                    AND u.TOKEN = '" . $_COOKIE['system_token'] . "'";
            $sql = $this->db->query($cad);
            if($this->db->rows($sql)>0){
                foreach ($sql as $key) {
                    $this->usuario_id = $key['ID'];
                    $this->usuario_nombre = $key['NOMBRE'];
                    $this->usuario_email = $key['EMAIL'];
                    $this->usuario_categoria = $key['ID_CAT'];
                    $this->usuario_rol_nombre = $key['ROL_NOMBRE'];
                }
                // Actualizar último acceso
                $this->db->query("UPDATE SYSTEM_USUARIOS SET ULTIMO_ACCESO = NOW() WHERE ID = " . $this->usuario_id);
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * Verifica si el usuario tiene permiso sobre un módulo específico
     * @param string $modulo_slug Slug del módulo (ej: 'clubes', 'eventos')
     * @param string $permiso_slug Slug del permiso (ej: 'ver', 'crear', 'editar', 'eliminar', 'administrar')
     * @return bool
     */
    public function tiene_permiso($modulo_slug, $permiso_slug = 'ver') {
        // SUPERUSUARIO tiene acceso total
        if($this->usuario_categoria == 1) {
            return true;
        }

        $cad = "SELECT COUNT(*) as tiene_permiso
                FROM SYSTEM_ROL_MODULO_PERMISOS rmp
                INNER JOIN SYSTEM_MODULOS m ON rmp.ID_MODULO = m.ID
                INNER JOIN SYSTEM_PERMISOS p ON rmp.ID_PERMISO = p.ID
                WHERE rmp.ID_ROL = " . $this->usuario_categoria . "
                AND m.SLUG = '$modulo_slug'
                AND p.SLUG = '$permiso_slug'
                AND m.ACTIVO = 'S'";

        $sql = $this->db->query($cad);
        $result = $sql->fetch_assoc();

        return $result['tiene_permiso'] > 0;
    }

    /**
     * Obtiene todos los módulos a los que el usuario tiene acceso
     * @return array
     */
    public function obtener_modulos_acceso() {
        // SUPERUSUARIO tiene acceso a todos
        if($this->usuario_categoria == 1) {
            $cad = "SELECT DISTINCT m.*
                    FROM SYSTEM_MODULOS m
                    WHERE m.ACTIVO = 'S'
                    ORDER BY m.ORDEN";
        } else {
            $cad = "SELECT DISTINCT m.*
                    FROM SYSTEM_MODULOS m
                    INNER JOIN SYSTEM_ROL_MODULO_PERMISOS rmp ON m.ID = rmp.ID_MODULO
                    WHERE rmp.ID_ROL = " . $this->usuario_categoria . "
                    AND m.ACTIVO = 'S'
                    ORDER BY m.ORDEN";
        }

        $sql = $this->db->query($cad);
        $modulos = [];
        while($row = $sql->fetch_assoc()) {
            $modulos[] = $row;
        }

        return $modulos;
    }

    /**
     * Verifica si el usuario actual es director de club
     * @return bool
     */
    public function es_director_club() {
        return $this->usuario_rol_nombre === 'DIRECTOR_CLUB';
    }

    /**
     * Obtiene el club asignado al director actual
     * @return array|null
     */
    public function obtener_club_asignado() {
        if (!$this->es_director_club()) {
            return null;
        }

        // Buscar club asignado al usuario (por ID_CLUB_ASIGNADO)
        $sql = $this->db->query("SELECT c.*,
                                        u.NOMBRE as DIRECTOR_NOMBRE,
                                        u.EMAIL as DIRECTOR_EMAIL,
                                        (SELECT COUNT(*) FROM VRE_DIRECTIVA_CLUBES d WHERE d.ID_CLUB = c.ID AND d.ESTADO = 'activo') as TOTAL_DIRECTIVA
                                 FROM VRE_CLUBES c
                                 INNER JOIN SYSTEM_USUARIOS u ON u.ID = " . $this->usuario_id . "
                                 WHERE u.ID_CLUB_ASIGNADO = c.ID
                                 AND c.ACTIVO = 'S'");

        if ($sql && $sql->num_rows > 0) {
            return $sql->fetch_assoc();
        }

        return null;
    }

    /**
     * Verifica si el usuario actual es director de ministerio
     * @return bool
     */
    public function es_director_ministerio() {
        return $this->usuario_rol_nombre === 'DIRECTOR_MINISTERIO';
    }

    /**
     * Obtiene el ministerio asignado al director actual
     * @return array|null
     */
    public function obtener_ministerio_asignado() {
        if (!$this->es_director_ministerio()) {
            return null;
        }

        // Buscar ministerio asignado al usuario (por ID_MINISTERIO_ASIGNADO)
        $sql = $this->db->query("SELECT m.*,
                                        u.NOMBRE as DIRECTOR_NOMBRE,
                                        u.EMAIL as DIRECTOR_EMAIL,
                                        (SELECT COUNT(*) FROM VRE_DIRECTIVA_MINISTERIOS d WHERE d.ID_MINISTERIO = m.ID AND d.ESTADO = 'activo') as TOTAL_DIRECTIVA
                                 FROM VRE_MINISTERIOS m
                                 INNER JOIN SYSTEM_USUARIOS u ON u.ID = " . $this->usuario_id . "
                                 WHERE u.ID_MINISTERIO_ASIGNADO = m.ID
                                 AND m.ACTIVO = 'S'");

        if ($sql && $sql->num_rows > 0) {
            return $sql->fetch_assoc();
        }

        return null;
    }

    /**
     * Verifica si el director actual tiene acceso a un club específico
     * @param int $club_id ID del club
     * @return bool
     */
    public function puede_acceder_club($club_id) {
        // SuperUsuario y Administrador tienen acceso total
        if ($this->usuario_categoria <= 2) {
            return true;
        }
        
        // Director de club solo puede acceder a su club asignado
        if ($this->es_director_club()) {
            $club_asignado = $this->obtener_club_asignado();
            return $club_asignado && $club_asignado['ID'] == $club_id;
        }
        
        return false;
    }

    /**
     * Registra una acción en auditoría
     * @param string $modulo Nombre del módulo
     * @param string $accion Acción realizada (CREAR, EDITAR, ELIMINAR, VER)
     * @param string $descripcion Descripción detallada
     */
    public function registrar_auditoria($modulo, $accion, $descripcion = '') {
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'DESCONOCIDA';
        
        // Escapar datos para evitar errores SQL
        $modulo = $this->db->real_escape_string($modulo);
        $accion = $this->db->real_escape_string($accion);
        $descripcion = $this->db->real_escape_string($descripcion);
        $ip = $this->db->real_escape_string($ip);
        
        $cad = "INSERT INTO SYSTEM_AUDITORIA(ID_USUARIO, MODULO, ACCION, DESCRIPCION, IP)
                VALUES (" . $this->usuario_id . ", '$modulo', '$accion', '$descripcion', '$ip')";
        
        // Intentar insertar, pero no fallar si hay error
        try {
            $this->db->query($cad);
        } catch (Exception $e) {
            // Log error pero continuar (auditoría no debe romper operaciones principales)
            error_log('Error en auditoría: ' . $e->getMessage());
        }
    }
    public function head() { 
        ?>
        <meta name="robots" content="noindex">
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <title><?php echo $this->titulo ?> | CPanel</title>
        <meta name="description" content="An empty page with a fluid vertical layout." />
        <!-- Favicon Tags Start -->
        <link rel="apple-touch-icon-precomposed" sizes="57x57" href="<?php echo $this->siteURL ?>assets/img/favicon/apple-touch-icon-57x57.png" />
        <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo $this->siteURL ?>assets/img/favicon/apple-touch-icon-114x114.png" />
        <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo $this->siteURL ?>assets/img/favicon/apple-touch-icon-72x72.png" />
        <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo $this->siteURL ?>assets/img/favicon/apple-touch-icon-144x144.png" />
        <link rel="apple-touch-icon-precomposed" sizes="60x60" href="<?php echo $this->siteURL ?>assets/img/favicon/apple-touch-icon-60x60.png" />
        <link rel="apple-touch-icon-precomposed" sizes="120x120" href="<?php echo $this->siteURL ?>assets/img/favicon/apple-touch-icon-120x120.png" />
        <link rel="apple-touch-icon-precomposed" sizes="76x76" href="<?php echo $this->siteURL ?>assets/img/favicon/apple-touch-icon-76x76.png" />
        <link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?php echo $this->siteURL ?>assets/img/favicon/apple-touch-icon-152x152.png" />
        <link rel="icon" type="image/png" href="<?php echo $this->siteURL ?>assets/img/favicon/favicon-196x196.png" sizes="196x196" />
        <link rel="icon" type="image/png" href="<?php echo $this->siteURL ?>assets/img/favicon/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/png" href="<?php echo $this->siteURL ?>assets/img/favicon/favicon-32x32.png" sizes="32x32" />
        <link rel="icon" type="image/png" href="<?php echo $this->siteURL ?>assets/img/favicon/favicon-16x16.png" sizes="16x16" />
        <link rel="icon" type="image/png" href="<?php echo $this->siteURL ?>assets/img/favicon/favicon-128.png" sizes="128x128" />
        <meta name="application-name" content="&nbsp;" />
        <meta name="msapplication-TileColor" content="#FFFFFF" />
        <meta name="msapplication-TileImage" content="<?php echo $this->siteURL ?>assets/img/favicon/mstile-144x144.png" />
        <meta name="msapplication-square70x70logo" content="<?php echo $this->siteURL ?>assets/img/favicon/mstile-70x70.png" />
        <meta name="msapplication-square150x150logo" content="<?php echo $this->siteURL ?>assets/img/favicon/mstile-150x150.png" />
        <meta name="msapplication-wide310x150logo" content="<?php echo $this->siteURL ?>assets/img/favicon/mstile-310x150.png" />
        <meta name="msapplication-square310x310logo" content="<?php echo $this->siteURL ?>assets/img/favicon/mstile-310x310.png" />
        <!-- Favicon Tags End -->
        <!-- Font Tags Start -->
        <link rel="preconnect" href="https://fonts.gstatic.com" />
        <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;700&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;700&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="<?php echo $this->siteURL ?>assets/font/CS-Interface/style.css" />
        <script src="https://kit.fontawesome.com/bd1e8b652a.js" crossorigin="anonymous"></script>
        <!-- Font Tags End -->
        <!-- Vendor Styles Start -->
        <link rel="stylesheet" href="<?php echo $this->siteURL ?>assets/css/vendor/bootstrap.min.css" />
        <link rel="stylesheet" href="<?php echo $this->siteURL ?>assets/css/vendor/OverlayScrollbars.min.css" />
        <link rel="stylesheet" href="<?php echo $this->siteURL ?>assets/css/vendor/datatables.min.css" />
        <!-- Vendor Styles End -->
        <link rel="stylesheet" href="<?php echo $this->siteURL ?>assets/css/vendor/bootstrap-datepicker3.standalone.min.css" />
        <!-- Template Base Styles Start -->
        <link rel="stylesheet" href="<?php echo $this->siteURL ?>assets/css/vendor/select2.min.css" />
        <link rel="stylesheet" href="<?php echo $this->siteURL ?>assets/css/vendor/select2-bootstrap4.min.css" />
        <link rel="stylesheet" href="<?php echo $this->siteURL ?>assets/css/styles.css" />
        <!-- Template Base Styles End -->

        <link rel="stylesheet" href="<?php echo $this->siteURL ?>assets/css/main.css" />
        <script src="<?php echo $this->siteURL ?>assets/js/base/loader.js"></script>
        <?php
    }
    public function nav() {
        ?>
        <div id="nav" class="nav-container d-flex">
            <div class="nav-content d-flex">
                <!-- Logo Start -->
                <div class="logo position-relative">
                    <a href="<?php echo $this->siteURL ?>">
                        <!-- Logo can be added directly -->
                        <img src="<?php echo $this->siteURL ?>favicon.svg" class="theme-filter" alt="logo" />

                        <!-- Or added via css to provide different ones for different color themes -->
                        <!-- <div class="img"></div> -->
                    </a>
                </div>
                <!-- Logo End -->

                <!-- User Menu Start -->
                <div class="user-container d-flex">
                    <a href="#" class="d-flex user position-relative" data-bs-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                        <img class="profile" alt="profile" src="<?php echo $this->siteURL ?>assets/img/profile/user-solid.svg" />
                        <div class="name"><?php echo $this->usuario_nombre ?></div>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end user-menu wide">
                        <div class="row mb-3 ms-0 me-0">
                            <div class="col-12 ps-1 mb-2">
                                <div class="text-extra-small text-primary">CUENTA</div>
                            </div>
                            <!-- <div class="col-6 ps-1 pe-1">
                                <ul class="list-unstyled">
                                    <li>
                                        <a href="#">Detalles</a>
                                    </li>
                                </ul>
                            </div> -->
                            <div class="col-6 pe-1 ps-1">
                                <ul class="list-unstyled">
                                    <li>
                                        <a href="<?php echo $this->siteURL ?>configuracion/password/">Contraseña</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php if($this->usuario_categoria == 1): ?>
                        <div class="row mb-1 ms-0 me-0">
                            <div class="col-12 p-1 mb-2 pt-2">
                                <div class="text-extra-small text-primary">ADMINISTRADOR</div>
                            </div>
                            <div class="col-6 ps-1 pe-1">
                                <ul class="list-unstyled">
                                    <li>
                                        <a href="<?php echo $this->siteURL ?>configuracion/usuarios/">Usuarios</a>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-6 ps-1 pe-1">
                                <ul class="list-unstyled">
                                    <li>
                                        <a href="<?php echo $this->siteURL ?>configuracion/cargos/">Cargos</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="row mb-1 ms-0 me-0">
                            <div class="col-12 p-1 mb-3 pt-3">
                                <div class="separator-light"></div>
                            </div>
                            <!-- <div class="col-6 ps-1 pe-1">
                                <ul class="list-unstyled">
                                    <li>
                                        <a href="#">
                                            <i data-acorn-icon="help" class="me-2" data-acorn-size="17"></i>
                                            <span class="align-middle">Help</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <i data-acorn-icon="file-text" class="me-2" data-acorn-size="17"></i>
                                            <span class="align-middle">Docs</span>
                                        </a>
                                    </li>
                                </ul>
                            </div> -->
                            <div class="col-6 pe-1 ps-1">
                                <ul class="list-unstyled">
                                    <!-- <li>
                                        <a href="#">
                                            <i data-acorn-icon="gear" class="me-2" data-acorn-size="17"></i>
                                            <span class="align-middle">Settings</span>
                                        </a>
                                    </li> -->
                                    <li>
                                        <a href="<?php echo $this->siteURL ?>login/">
                                            <i data-acorn-icon="logout" class="me-2" data-acorn-size="17"></i>
                                            <span class="align-middle">Salir</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- User Menu End -->

                <!-- Icons Menu Start -->
                <ul class="list-unstyled list-inline text-center menu-icons">
                    <li class="list-inline-item">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#searchPagesModal">
                            <i data-acorn-icon="search" data-acorn-size="18"></i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a href="#" id="pinButton" class="pin-button">
                            <i data-acorn-icon="lock-on" class="unpin" data-acorn-size="18"></i>
                            <i data-acorn-icon="lock-off" class="pin" data-acorn-size="18"></i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a href="#" id="colorButton">
                            <i data-acorn-icon="light-on" class="light" data-acorn-size="18"></i>
                            <i data-acorn-icon="light-off" class="dark" data-acorn-size="18"></i>
                        </a>
                    </li>
                    <li class="list-inline-item">
                        <a href="#" data-bs-toggle="dropdown" data-bs-target="#notifications" aria-haspopup="true"
                            aria-expanded="false" class="notification-button">
                            <div class="position-relative d-inline-flex">
                                <i data-acorn-icon="bell" data-acorn-size="18"></i>
                                <span class="position-absolute notification-dot rounded-xl"></span>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end wide notification-dropdown scroll-out"
                            id="notifications">
                            <div class="scroll">
                                <ul class="list-unstyled border-last-none">
                                    <li class="mb-3 pb-3 border-bottom border-separator-light d-flex">
                                        <img src="<?php echo $this->siteURL ?>assets/img/profile/profile-1.webp"
                                            class="me-3 sw-4 sh-4 rounded-xl align-self-center" alt="..." />
                                        <div class="align-self-center">
                                            <a href="#">Joisse Kaycee just sent a new comment!</a>
                                        </div>
                                    </li>
                                    <li class="mb-3 pb-3 border-bottom border-separator-light d-flex">
                                        <img src="<?php echo $this->siteURL ?>assets/img/profile/profile-2.webp"
                                            class="me-3 sw-4 sh-4 rounded-xl align-self-center" alt="..." />
                                        <div class="align-self-center">
                                            <a href="#">New order received! It is total $147,20.</a>
                                        </div>
                                    </li>
                                    <li class="mb-3 pb-3 border-bottom border-separator-light d-flex">
                                        <img src="<?php echo $this->siteURL ?>assets/img/profile/profile-3.webp"
                                            class="me-3 sw-4 sh-4 rounded-xl align-self-center" alt="..." />
                                        <div class="align-self-center">
                                            <a href="#">3 items just added to wish list by a user!</a>
                                        </div>
                                    </li>
                                    <li class="pb-3 pb-3 border-bottom border-separator-light d-flex">
                                        <img src="<?php echo $this->siteURL ?>assets/img/profile/profile-6.webp"
                                            class="me-3 sw-4 sh-4 rounded-xl align-self-center" alt="..." />
                                        <div class="align-self-center">
                                            <a href="#">Kirby Peters just sent a new message!</a>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li>
                </ul>
                <!-- Icons Menu End -->

                <!-- Menu Start -->
                <div class="menu-container flex-grow-1">
                    <ul id="menu" class="menu">
                        <?php
                        // Solo mostrar estos módulos si NO es director de club o ministerio
                        if (!$this->es_director_club() && !$this->es_director_ministerio()) {
                        ?>
                        <li>
                            <a href="<?php echo $this->siteURL ?>anuarios/admin/">
                                <i class="fa-solid fa-book icon" data-acorn-size="18"></i>
                                <span class="label">Anuarios Admin</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo $this->siteURL ?>pulso/equipo">
                                <i class="fa-solid fa-users icon" data-acorn-size="18"></i>
                                <span class="label">Pulso - Equipo</span>
                            </a>
                        </li>
                        <li>
                            <a href="#federacion">
                                <i class="fa-solid fa-award icon" data-acorn-size="18"></i>
                                <span class="label">Federación</span>
                            </a>
                            <ul id="federacion">
                                <li>
                                    <a href="<?php echo $this->siteURL ?>federacion/miembros">
                                        <span class="label">Miembros</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $this->siteURL ?>federacion/informacion">
                                        <span class="label">Información</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="<?php echo $this->siteURL ?>repositorio/">
                                <i class="fa-solid fa-camera icon" data-acorn-size="18"></i>
                                <span class="label">Repositorio</span>
                            </a>
                        </li>
                        <!-- <li>
                            <a href="#blog" data-href="<?php echo $this->siteURL ?>blog/">
                                <i class="fa-solid fa-signs-post icon" data-acorn-size="18"></i>
                                <span class="label">Blog</span>
                            </a>
                            <ul id="blog">
                                <li>
                                    <a href="<?php echo $this->siteURL ?>blog/listado/">
                                        <span class="label">Listado</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $this->siteURL ?>blog/autores/">
                                        <span class="label">Autores</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="<?php echo $this->siteURL ?>blog/categorias/">
                                        <span class="label">Categorías</span>
                                    </a>
                                </li>
                            </ul>
                        </li> -->
                        <?php
                        }

                        // Definir iconos para cada módulo
                        $iconos_modulos = [
                            'dashboard' => 'home',
                            'usuarios' => 'user',
                            'anuarios' => 'book',
                            'pulso' => 'users',
                            'federacion' => 'award',
                            'clubes' => 'star',
                            'ministerios' => 'heart',
                            'deportes' => 'activity',
                            'instalaciones' => 'building',
                            'cocurriculares' => 'graduation',
                            'eventos' => 'calendar',
                            'banners' => 'layout',
                            'vida-campus' => 'coffee',
                            'configuracion' => 'settings'
                        ];

                        // Menú especial para directores de club o ministerio
                        if ($this->es_director_club()) {
                            // Menú simplificado para directores de club
                            echo '<li>';
                            echo '<a href="' . $this->siteURL . 'pages/mi-club/">';
                            echo '<i data-acorn-icon="star" class="icon" data-acorn-size="18"></i>';
                            echo '<span class="label">Mi Club</span>';
                            echo '</a>';
                            echo '</li>';
                            echo '<li>';
                            echo '<a href="' . $this->siteURL . 'pages/mi-club/apariencia.php">';
                            echo '<i data-acorn-icon="image" class="icon" data-acorn-size="18"></i>';
                            echo '<span class="label">Apariencia</span>';
                            echo '</a>';
                            echo '</li>';
                        } else if ($this->es_director_ministerio()) {
                            // Menú simplificado para directores de ministerio - solo Mi Ministerio
                            echo '<li>';
                            echo '<a href="' . $this->siteURL . 'pages/mi-ministerio/">';
                            echo '<i data-acorn-icon="heart" class="icon" data-acorn-size="18"></i>';
                            echo '<span class="label">Mi Ministerio</span>';
                            echo '</a>';
                            echo '</li>';
                        } else {
                            // Menú simplificado: Solo Involúcrate y Configuración
                            // Obtener módulos con acceso
                            $modulos = $this->obtener_modulos_acceso();

                            // Agrupar módulos de Involúcrate
                            $modulos_involucrate = ['clubes', 'ministerios', 'deportes', 'instalaciones', 'cocurriculares', 'eventos', 'banners'];
                            $tiene_involucrate = false;
                            $items_involucrate = [];

                            foreach($modulos as $modulo) {
                                $slug = $modulo['SLUG'];
                                $icono = $iconos_modulos[$slug] ?? 'circle';

                                if(in_array($slug, $modulos_involucrate)) {
                                    $tiene_involucrate = true;
                                    $items_involucrate[] = $modulo;
                                }
                            }

                            // URLs reales de cada módulo (algunos viven en pages/)
                            $urls_modulos = [
                                'clubes'          => 'pages/clubes/',
                                'ministerios'     => 'pages/ministerios/',
                                'deportes'        => 'pages/deportes/',
                                'instalaciones'   => 'pages/instalaciones/',
                                'cocurriculares'  => 'pages/cocurriculares/',
                                'eventos'         => 'pages/eventos/',
                                'banners'         => 'pages/banners/',
                            ];

                            // Si tiene acceso a módulos de Involúcrate, crear submenú
                            if($tiene_involucrate) {
                                echo '<li>';
                                echo '<a href="#involucrate" data-href="' . $this->siteURL . 'pages/clubes/">';
                                echo '<i data-acorn-icon="layout" class="icon" data-acorn-size="18"></i>';
                                echo '<span class="label">Involúcrate</span>';
                                echo '</a>';
                                echo '<ul id="involucrate">';
                                foreach($items_involucrate as $modulo) {
                                    $slug = $modulo['SLUG'];
                                    $icono = $iconos_modulos[$slug] ?? 'circle';
                                    $url = $urls_modulos[$slug] ?? ($slug . '/');
                                    echo '<li>';
                                    echo '<a href="' . $this->siteURL . $url . '">';
                                    echo '<i data-acorn-icon="' . $icono . '" class="icon" data-acorn-size="14"></i>';
                                    echo '<span class="label">' . $modulo['NOMBRE'] . '</span>';
                                    echo '</a>';
                                    echo '</li>';
                                }
                                echo '</ul>';
                                echo '</li>';
                            }

                            // Agregar Galería (módulo independiente)
                            if($this->tiene_permiso('galeria', 'ver')) {
                                echo '<li>';
                                echo '<a href="' . $this->siteURL . 'galeria/">';
                                echo '<i data-acorn-icon="image" class="icon" data-acorn-size="18"></i>';
                                echo '<span class="label">Galería</span>';
                                echo '</a>';
                                echo '</li>';
                            }

                            // Video Hero - visible para superusuarios y administradores
                            if($this->usuario_categoria <= 2) {
                                echo '<li>';
                                echo '<a href="' . $this->siteURL . 'pages/vida-estudiantil/video-hero.php">';
                                echo '<i class="fas fa-film icon" data-acorn-size="18"></i>';
                                echo '<span class="label">Video Hero</span>';
                                echo '</a>';
                                echo '</li>';
                            }

                            // Agregar Configuración como dropdown
                            echo '<li>';
                            echo '<a href="#configuracion" data-href="' . $this->siteURL . 'configuracion/">';
                            echo '<i data-acorn-icon="settings" class="icon" data-acorn-size="18"></i>';
                            echo '<span class="label">Configuración</span>';
                            echo '</a>';
                            echo '<ul id="configuracion">';

                            // Password - todos los usuarios
                            echo '<li>';
                            echo '<a href="' . $this->siteURL . 'configuracion/password/">';
                            echo '<i data-acorn-icon="lock-on" class="icon" data-acorn-size="14"></i>';
                            echo '<span class="label">Contraseña</span>';
                            echo '</a>';
                            echo '</li>';

                            // Usuarios - solo superusuarios
                            if($this->usuario_categoria == 1) {
                                echo '<li>';
                                echo '<a href="' . $this->siteURL . 'configuracion/usuarios/">';
                                echo '<i data-acorn-icon="user" class="icon" data-acorn-size="14"></i>';
                                echo '<span class="label">Usuarios</span>';
                                echo '</a>';
                                echo '</li>';
                            }

                            echo '</ul>';
                            echo '</li>';
                        }
                        ?>
                    </ul>
                </div>
                <!-- Menu End -->

                <!-- Mobile Buttons Start -->
                <div class="mobile-buttons-container">
                    <!-- Scrollspy Mobile Button Start -->
                    <a href="#" id="scrollSpyButton" class="spy-button" data-bs-toggle="dropdown">
                        <i data-acorn-icon="menu-dropdown"></i>
                    </a>
                    <!-- Scrollspy Mobile Button End -->

                    <!-- Scrollspy Mobile Dropdown Start -->
                    <div class="dropdown-menu dropdown-menu-end" id="scrollSpyDropdown"></div>
                    <!-- Scrollspy Mobile Dropdown End -->

                    <!-- Menu Button Start -->
                    <a href="#" id="mobileMenuButton" class="menu-button">
                        <i data-acorn-icon="menu"></i>
                    </a>
                    <!-- Menu Button End -->
                </div>
                <!-- Mobile Buttons End -->
            </div>
            <div class="nav-shadow"></div>
        </div>
        <?php
    }
    public function footer() {
        ?>
        <footer>
            <div class="footer-content">
                <div class="container">
                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <p class="mb-0 text-muted text-medium">Sistema desarrollado para uso exclusivo de la Universaidad de Montemorelos.</p>
                        </div>
                        <div class="col-sm-6 d-none d-sm-block">
                            <ul class="breadcrumb pt-0 pe-0 mb-0 float-end">
                                <?php if($this->usuario_categoria == 1): ?>
                                <li class="breadcrumb-item mb-0 text-medium">
                                    <a href="https://1.envato.market/BX5oGy" target="_blank" class="btn-link">Review</a>
                                </li>
                                <li class="breadcrumb-item mb-0 text-medium">
                                    <a href="https://1.envato.market/BX5oGy" target="_blank"
                                        class="btn-link">Purchase</a>
                                </li>
                                <li class="breadcrumb-item mb-0 text-medium">
                                    <a href="https://acorn-html-docs.coloredstrategies.com/" target="_blank"
                                        class="btn-link">Docs</a>
                                </li>
                                <?php endif; ?>
                                <li class="breadcrumb-item mb-0 text-medium">
                                    <a href="/" class="btn-link">Ir al sitio</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <?php
    }
    public function modalSettings() {
        ?>
        <div class="modal fade modal-right scroll-out-negative" id="settings" data-bs-backdrop="true" tabindex="-1" role="dialog" aria-labelledby="settings" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable full" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Theme Settings</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="scroll-track-visible">
                            <div class="mb-5" id="color">
                                <label class="mb-3 d-inline-block form-label">Color</label>
                                <div class="row d-flex g-3 justify-content-between flex-wrap mb-3">
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="light-blue"
                                        data-parent="color">
                                        <div class="card rounded-md p-3 mb-1 no-shadow color">
                                            <div class="blue-light"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">LIGHT BLUE</span>
                                        </div>
                                    </a>
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="dark-blue"
                                        data-parent="color">
                                        <div class="card rounded-md p-3 mb-1 no-shadow color">
                                            <div class="blue-dark"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">DARK BLUE</span>
                                        </div>
                                    </a>
                                </div>
                                <div class="row d-flex g-3 justify-content-between flex-wrap mb-3">
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="light-teal"
                                        data-parent="color">
                                        <div class="card rounded-md p-3 mb-1 no-shadow color">
                                            <div class="teal-light"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">LIGHT TEAL</span>
                                        </div>
                                    </a>
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="dark-teal"
                                        data-parent="color">
                                        <div class="card rounded-md p-3 mb-1 no-shadow color">
                                            <div class="teal-dark"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">DARK TEAL</span>
                                        </div>
                                    </a>
                                </div>
                                <div class="row d-flex g-3 justify-content-between flex-wrap mb-3">
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="light-sky"
                                        data-parent="color">
                                        <div class="card rounded-md p-3 mb-1 no-shadow color">
                                            <div class="sky-light"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">LIGHT SKY</span>
                                        </div>
                                    </a>
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="dark-sky"
                                        data-parent="color">
                                        <div class="card rounded-md p-3 mb-1 no-shadow color">
                                            <div class="sky-dark"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">DARK SKY</span>
                                        </div>
                                    </a>
                                </div>
                                <div class="row d-flex g-3 justify-content-between flex-wrap mb-3">
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="light-red"
                                        data-parent="color">
                                        <div class="card rounded-md p-3 mb-1 no-shadow color">
                                            <div class="red-light"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">LIGHT RED</span>
                                        </div>
                                    </a>
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="dark-red"
                                        data-parent="color">
                                        <div class="card rounded-md p-3 mb-1 no-shadow color">
                                            <div class="red-dark"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">DARK RED</span>
                                        </div>
                                    </a>
                                </div>
                                <div class="row d-flex g-3 justify-content-between flex-wrap mb-3">
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="light-green"
                                        data-parent="color">
                                        <div class="card rounded-md p-3 mb-1 no-shadow color">
                                            <div class="green-light"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">LIGHT GREEN</span>
                                        </div>
                                    </a>
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="dark-green"
                                        data-parent="color">
                                        <div class="card rounded-md p-3 mb-1 no-shadow color">
                                            <div class="green-dark"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">DARK GREEN</span>
                                        </div>
                                    </a>
                                </div>
                                <div class="row d-flex g-3 justify-content-between flex-wrap mb-3">
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="light-lime"
                                        data-parent="color">
                                        <div class="card rounded-md p-3 mb-1 no-shadow color">
                                            <div class="lime-light"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">LIGHT LIME</span>
                                        </div>
                                    </a>
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="dark-lime"
                                        data-parent="color">
                                        <div class="card rounded-md p-3 mb-1 no-shadow color">
                                            <div class="lime-dark"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">DARK LIME</span>
                                        </div>
                                    </a>
                                </div>
                                <div class="row d-flex g-3 justify-content-between flex-wrap mb-3">
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="light-pink"
                                        data-parent="color">
                                        <div class="card rounded-md p-3 mb-1 no-shadow color">
                                            <div class="pink-light"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">LIGHT PINK</span>
                                        </div>
                                    </a>
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="dark-pink"
                                        data-parent="color">
                                        <div class="card rounded-md p-3 mb-1 no-shadow color">
                                            <div class="pink-dark"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">DARK PINK</span>
                                        </div>
                                    </a>
                                </div>
                                <div class="row d-flex g-3 justify-content-between flex-wrap mb-3">
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="light-purple"
                                        data-parent="color">
                                        <div class="card rounded-md p-3 mb-1 no-shadow color">
                                            <div class="purple-light"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">LIGHT PURPLE</span>
                                        </div>
                                    </a>
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="dark-purple"
                                        data-parent="color">
                                        <div class="card rounded-md p-3 mb-1 no-shadow color">
                                            <div class="purple-dark"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">DARK PURPLE</span>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="mb-5" id="navcolor">
                                <label class="mb-3 d-inline-block form-label">Override Nav Palette</label>
                                <div class="row d-flex g-3 justify-content-between flex-wrap">
                                    <a href="#" class="flex-grow-1 w-33 option col" data-value="default"
                                        data-parent="navcolor">
                                        <div class="card rounded-md p-3 mb-1 no-shadow">
                                            <div class="figure figure-primary top"></div>
                                            <div class="figure figure-secondary bottom"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">DEFAULT</span>
                                        </div>
                                    </a>
                                    <a href="#" class="flex-grow-1 w-33 option col" data-value="light"
                                        data-parent="navcolor">
                                        <div class="card rounded-md p-3 mb-1 no-shadow">
                                            <div class="figure figure-secondary figure-light top"></div>
                                            <div class="figure figure-secondary bottom"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">LIGHT</span>
                                        </div>
                                    </a>
                                    <a href="#" class="flex-grow-1 w-33 option col" data-value="dark"
                                        data-parent="navcolor">
                                        <div class="card rounded-md p-3 mb-1 no-shadow">
                                            <div class="figure figure-muted figure-dark top"></div>
                                            <div class="figure figure-secondary bottom"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">DARK</span>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="mb-5" id="placement">
                                <label class="mb-3 d-inline-block form-label">Menu Placement</label>
                                <div class="row d-flex g-3 justify-content-between flex-wrap">
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="horizontal"
                                        data-parent="placement">
                                        <div class="card rounded-md p-3 mb-1 no-shadow">
                                            <div class="figure figure-primary top"></div>
                                            <div class="figure figure-secondary bottom"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">HORIZONTAL</span>
                                        </div>
                                    </a>
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="vertical"
                                        data-parent="placement">
                                        <div class="card rounded-md p-3 mb-1 no-shadow">
                                            <div class="figure figure-primary left"></div>
                                            <div class="figure figure-secondary right"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">VERTICAL</span>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="mb-5" id="behaviour">
                                <label class="mb-3 d-inline-block form-label">Menu Behaviour</label>
                                <div class="row d-flex g-3 justify-content-between flex-wrap">
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="pinned"
                                        data-parent="behaviour">
                                        <div class="card rounded-md p-3 mb-1 no-shadow">
                                            <div class="figure figure-primary left large"></div>
                                            <div class="figure figure-secondary right small"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">PINNED</span>
                                        </div>
                                    </a>
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="unpinned"
                                        data-parent="behaviour">
                                        <div class="card rounded-md p-3 mb-1 no-shadow">
                                            <div class="figure figure-primary left"></div>
                                            <div class="figure figure-secondary right"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">UNPINNED</span>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="mb-5" id="layout">
                                <label class="mb-3 d-inline-block form-label">Layout</label>
                                <div class="row d-flex g-3 justify-content-between flex-wrap">
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="fluid" data-parent="layout">
                                        <div class="card rounded-md p-3 mb-1 no-shadow">
                                            <div class="figure figure-primary top"></div>
                                            <div class="figure figure-secondary bottom"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">FLUID</span>
                                        </div>
                                    </a>
                                    <a href="#" class="flex-grow-1 w-50 option col" data-value="boxed" data-parent="layout">
                                        <div class="card rounded-md p-3 mb-1 no-shadow">
                                            <div class="figure figure-primary top"></div>
                                            <div class="figure figure-secondary bottom small"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">BOXED</span>
                                        </div>
                                    </a>
                                </div>
                            </div>

                            <div class="mb-5" id="radius">
                                <label class="mb-3 d-inline-block form-label">Radius</label>
                                <div class="row d-flex g-3 justify-content-between flex-wrap">
                                    <a href="#" class="flex-grow-1 w-33 option col" data-value="rounded"
                                        data-parent="radius">
                                        <div class="card rounded-md radius-rounded p-3 mb-1 no-shadow">
                                            <div class="figure figure-primary top"></div>
                                            <div class="figure figure-secondary bottom"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">ROUNDED</span>
                                        </div>
                                    </a>
                                    <a href="#" class="flex-grow-1 w-33 option col" data-value="standard"
                                        data-parent="radius">
                                        <div class="card rounded-md radius-regular p-3 mb-1 no-shadow">
                                            <div class="figure figure-primary top"></div>
                                            <div class="figure figure-secondary bottom"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">STANDARD</span>
                                        </div>
                                    </a>
                                    <a href="#" class="flex-grow-1 w-33 option col" data-value="flat" data-parent="radius">
                                        <div class="card rounded-md radius-flat p-3 mb-1 no-shadow">
                                            <div class="figure figure-primary top"></div>
                                            <div class="figure figure-secondary bottom"></div>
                                        </div>
                                        <div class="text-muted text-part">
                                            <span class="text-extra-small align-middle">FLAT</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="settings-buttons-container">
            <button type="button" class="btn settings-button btn-primary p-0" data-bs-toggle="modal"
                data-bs-target="#settings" id="settingsButton">
                <span class="d-inline-block no-delay" data-bs-delay="0" data-bs-offset="0,3" data-bs-toggle="tooltip"
                    data-bs-placement="left" title="Settings">
                    <i data-acorn-icon="paint-roller" class="position-relative"></i>
                </span>
            </button>
        </div>
        <?php
    }
    public function modalSearch() {
        ?>
        <div class="modal fade modal-under-nav modal-search modal-close-out" id="searchPagesModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header border-0 p-0">
                        <button type="button" class="btn-close btn btn-icon btn-icon-only btn-foreground"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body ps-5 pe-5 pb-0 border-0">
                        <input id="searchPagesInput"
                            class="form-control form-control-xl borderless ps-0 pe-0 mb-1 auto-complete" type="text"
                            autocomplete="off" />
                    </div>
                    <div class="modal-footer border-top justify-content-start ps-5 pe-5 pb-3 pt-3 border-0">
                        <span class="text-alternate d-inline-block m-0 me-3">
                            <i data-acorn-icon="arrow-bottom" data-acorn-size="15"
                                class="text-alternate align-middle me-1"></i>
                            <span class="align-middle text-medium">Navigate</span>
                        </span>
                        <span class="text-alternate d-inline-block m-0 me-3">
                            <i data-acorn-icon="arrow-bottom-left" data-acorn-size="15"
                                class="text-alternate align-middle me-1"></i>
                            <span class="align-middle text-medium">Select</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    public function scripts() {
        ?>
        <!-- Vendor Scripts Start -->
        <script src="<?php echo $this->siteURL ?>assets/js/vendor/jquery-3.5.1.min.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/vendor/bootstrap.bundle.min.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/vendor/OverlayScrollbars.min.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/vendor/autoComplete.min.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/vendor/clamp.min.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/icon/acorn-icons.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/icon/acorn-icons-interface.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/icon/acorn-icons-commerce.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/vendor/datatables.min.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/cs/scrollspy.js"></script>
        <!-- Vendor Scripts End -->

        <script src="<?php echo $this->siteURL ?>assets/js/vendor/select2.full.min.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/vendor/datepicker/bootstrap-datepicker.min.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/vendor/datepicker/locales/bootstrap-datepicker.es.min.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/vendor/jquery.validate/jquery.validate.min.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/vendor/jquery.validate/additional-methods.min.js"></script>

        <!-- Template Base Scripts Start -->
        <script src="<?php echo $this->siteURL ?>assets/js/base/helpers.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/base/globals.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/base/nav.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/base/search.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/base/settings.js"></script>
        <!-- Template Base Scripts End -->
        <!-- Page Specific Scripts Start -->

        <script src="<?php echo $this->siteURL ?>assets/js/cs/datatable.extend.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/vendor/bootstrap-notify.min.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/pages/vertical.js"></script>

        <script src="<?php echo $this->siteURL ?>assets/js/common.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/scripts.js"></script>
        <script src="<?php echo $this->siteURL ?>assets/js/app.js"></script>
        <!-- Page Specific Scripts End -->
        <?php
    }
}
