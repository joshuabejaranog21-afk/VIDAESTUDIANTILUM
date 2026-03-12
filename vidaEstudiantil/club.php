<?php
$siteURL  = '/vida_estudiantil_Hitha/';
$portalURL = $siteURL . 'vidaEstudiantil/';

include('../cpanel/assets/API/db.php');
$db = new Conexion();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: ' . $portalURL . 'clubes'); exit; }

// Club
$sql = $db->query("SELECT ID, NOMBRE, DESCRIPCION, IMAGEN_URL, RESPONSABLE_NOMBRE,
    HORARIO, LUGAR, CONTACTO, REDES_SOCIALES FROM VRE_CLUBES WHERE ID=$id AND ACTIVO='S'");
if ($db->rows($sql) === 0) { header('Location: ' . $portalURL . 'clubes'); exit; }
$club = $sql->fetch_assoc();
$redes = !empty($club['REDES_SOCIALES']) ? (json_decode($club['REDES_SOCIALES'], true) ?? []) : [];

// Directiva
$directiva = [];
$sqlDir = $db->query("SELECT NOMBRE, CARGO, EMAIL, TELEFONO, FOTO_URL FROM VRE_DIRECTIVA_CLUBES
    WHERE ID_CLUB=$id AND ACTIVO='S' ORDER BY ORDEN ASC, NOMBRE ASC");
while ($d = $db->recorrer($sqlDir)) $directiva[] = $d;

// Galería
$galeria = [];
$sqlG = $db->query("SELECT IMAGEN_URL FROM VRE_GALERIA
    WHERE MODULO='clubes' AND ID_REGISTRO=$id AND ACTIVO='S' ORDER BY ORDEN ASC LIMIT 8");
while ($g = $db->recorrer($sqlG)) $galeria[] = $g['IMAGEN_URL'];

$titulo = $club['NOMBRE'];
$paginaActiva = 'clubes';
include('assets/php/header.php');
?>

<!-- Hero del club -->
<div style="position:relative;min-height:320px;background:linear-gradient(135deg,#5e72e4,#825ee4);overflow:hidden;">
    <?php if (!empty($club['IMAGEN_URL'])): ?>
        <img src="<?php echo $siteURL . htmlspecialchars($club['IMAGEN_URL']); ?>"
             style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.35;" alt="">
    <?php endif; ?>
    <div style="position:relative;z-index:1;padding:3rem 0;color:#fff;" class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2" style="--bs-breadcrumb-divider-color:rgba(255,255,255,.5);">
                <li class="breadcrumb-item"><a href="<?php echo $portalURL; ?>" style="color:rgba(255,255,255,.75);">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?php echo $portalURL; ?>clubes" style="color:rgba(255,255,255,.75);">Clubes</a></li>
                <li class="breadcrumb-item active text-white"><?php echo htmlspecialchars($club['NOMBRE']); ?></li>
            </ol>
        </nav>
        <h1 style="font-size:2.5rem;font-weight:800;"><?php echo htmlspecialchars($club['NOMBRE']); ?></h1>
        <?php if (!empty($club['RESPONSABLE_NOMBRE'])): ?>
            <p style="opacity:.85;margin:0;"><i class="fas fa-user-tie me-2"></i>Responsable: <?php echo htmlspecialchars($club['RESPONSABLE_NOMBRE']); ?></p>
        <?php endif; ?>
    </div>
</div>

<main class="container py-5">
    <div class="row g-4">
        <!-- Columna principal -->
        <div class="col-lg-8">
            <!-- Descripción -->
            <div class="portal-card mb-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Acerca del club</h4>
                    <p style="line-height:1.8;color:#495057;"><?php echo nl2br(htmlspecialchars($club['DESCRIPCION'] ?? 'Sin descripción disponible.')); ?></p>
                </div>
            </div>

            <!-- Directiva -->
            <?php if (!empty($directiva)): ?>
            <div class="portal-card mb-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4"><i class="fas fa-id-badge me-2 text-primary"></i>Directiva</h4>
                    <div class="row g-3">
                        <?php foreach ($directiva as $m): ?>
                        <div class="col-sm-6 col-md-4">
                            <div class="member-card">
                                <?php if (!empty($m['FOTO_URL'])): ?>
                                    <img src="<?php echo $siteURL . htmlspecialchars($m['FOTO_URL']); ?>"
                                         class="member-avatar" alt="<?php echo htmlspecialchars($m['NOMBRE']); ?>"
                                         style="width:70px;height:70px;border-radius:50%;object-fit:cover;margin:0 auto .75rem;display:block;">
                                <?php else: ?>
                                    <div class="member-avatar mx-auto mb-3" style="width:70px;height:70px;background:linear-gradient(135deg,#5e72e4,#825ee4);display:flex;align-items:center;justify-content:center;border-radius:50%;">
                                        <i class="fas fa-user text-white fs-4"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="member-name"><?php echo htmlspecialchars($m['NOMBRE']); ?></div>
                                <div class="member-cargo"><?php echo htmlspecialchars($m['CARGO']); ?></div>
                                <?php if (!empty($m['EMAIL'])): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($m['EMAIL']); ?>"
                                       style="font-size:.75rem;color:#5e72e4;text-decoration:none;display:block;margin-top:.3rem;">
                                        <i class="fas fa-envelope me-1"></i><?php echo htmlspecialchars($m['EMAIL']); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Galería -->
            <?php if (!empty($galeria)): ?>
            <div class="portal-card">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-3"><i class="fas fa-images me-2 text-primary"></i>Galería</h4>
                    <div class="row g-2">
                        <?php foreach ($galeria as $img): ?>
                        <div class="col-4 col-md-3">
                            <img src="<?php echo $siteURL . htmlspecialchars($img); ?>"
                                 class="img-fluid rounded" style="height:110px;width:100%;object-fit:cover;"
                                 alt="Galería">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="portal-card mb-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Información</h5>
                    <?php if (!empty($club['HORARIO'])): ?>
                    <div class="d-flex gap-2 mb-2">
                        <i class="fas fa-clock text-primary mt-1"></i>
                        <div>
                            <div style="font-size:.75rem;color:#8392ab;text-transform:uppercase;letter-spacing:.05em;">Horario</div>
                            <div style="font-size:.9rem;"><?php echo htmlspecialchars($club['HORARIO']); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($club['LUGAR'])): ?>
                    <div class="d-flex gap-2 mb-2">
                        <i class="fas fa-map-marker-alt text-primary mt-1"></i>
                        <div>
                            <div style="font-size:.75rem;color:#8392ab;text-transform:uppercase;letter-spacing:.05em;">Lugar</div>
                            <div style="font-size:.9rem;"><?php echo htmlspecialchars($club['LUGAR']); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($club['CONTACTO'])): ?>
                    <div class="d-flex gap-2 mb-2">
                        <i class="fas fa-envelope text-primary mt-1"></i>
                        <div>
                            <div style="font-size:.75rem;color:#8392ab;text-transform:uppercase;letter-spacing:.05em;">Contacto</div>
                            <div style="font-size:.9rem;"><?php echo htmlspecialchars($club['CONTACTO']); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($redes)): ?>
            <div class="portal-card">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Redes Sociales</h5>
                    <?php foreach ($redes as $red => $url): ?>
                        <?php if (empty($url)) continue; ?>
                        <?php
                        $icono = match(strtolower($red)) {
                            'instagram' => 'fab fa-instagram',
                            'facebook'  => 'fab fa-facebook-f',
                            'twitter'   => 'fab fa-x-twitter',
                            'youtube'   => 'fab fa-youtube',
                            'tiktok'    => 'fab fa-tiktok',
                            default     => 'fas fa-link'
                        };
                        ?>
                        <a href="<?php echo htmlspecialchars($url); ?>" target="_blank"
                           class="d-flex align-items-center gap-2 mb-2 text-decoration-none"
                           style="color:#344767;">
                            <i class="<?php echo $icono; ?> fa-lg text-primary"></i>
                            <span style="font-size:.9rem;"><?php echo ucfirst($red); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="mt-3">
                <a href="<?php echo $portalURL; ?>clubes" class="btn-portal-outline w-100 text-center d-block py-2">
                    <i class="fas fa-arrow-left me-2"></i>Ver todos los clubes
                </a>
            </div>
        </div>
    </div>
</main>

<?php include('assets/php/footer.php'); ?>
