<?php
$siteURL  = '/vida_estudiantil_Hitha/';
$portalURL = $siteURL . 'vidaEstudiantil/';

include('../cpanel/assets/API/db.php');
$db = new Conexion();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: ' . $portalURL . 'ministerios'); exit; }

$sql = $db->query("SELECT ID, NOMBRE, TIPO, DESCRIPCION, IMAGEN_URL, DIRECTOR_NOMBRE, HORARIO, LUGAR
    FROM VRE_MINISTERIOS WHERE ID=$id AND ACTIVO='S'");
if ($db->rows($sql) === 0) { header('Location: ' . $portalURL . 'ministerios'); exit; }
$min = $sql->fetch_assoc();

$directiva = [];
$sqlDir = $db->query("SELECT NOMBRE, CARGO, EMAIL, FOTO_URL FROM VRE_DIRECTIVA_MINISTERIOS
    WHERE ID_MINISTERIO=$id AND ACTIVO='S' ORDER BY ORDEN ASC, NOMBRE ASC");
while ($d = $db->recorrer($sqlDir)) $directiva[] = $d;

$galeria = [];
$sqlG = $db->query("SELECT IMAGEN_URL FROM VRE_GALERIA
    WHERE MODULO='ministerios' AND ID_REGISTRO=$id AND ACTIVO='S' ORDER BY ORDEN ASC LIMIT 8");
while ($g = $db->recorrer($sqlG)) $galeria[] = $g['IMAGEN_URL'];

$titulo = $min['NOMBRE'];
$paginaActiva = 'ministerios';
include('assets/php/header.php');
?>

<div style="position:relative;min-height:320px;background:linear-gradient(135deg,#825ee4,#5e72e4);overflow:hidden;">
    <?php if (!empty($min['IMAGEN_URL'])): ?>
        <img src="<?php echo $siteURL . htmlspecialchars($min['IMAGEN_URL']); ?>"
             style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.3;" alt="">
    <?php endif; ?>
    <div style="position:relative;z-index:1;padding:3rem 0;color:#fff;" class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2" style="--bs-breadcrumb-divider-color:rgba(255,255,255,.5);">
                <li class="breadcrumb-item"><a href="<?php echo $portalURL; ?>" style="color:rgba(255,255,255,.75);">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?php echo $portalURL; ?>ministerios" style="color:rgba(255,255,255,.75);">Ministerios</a></li>
                <li class="breadcrumb-item active text-white"><?php echo htmlspecialchars($min['NOMBRE']); ?></li>
            </ol>
        </nav>
        <?php if (!empty($min['TIPO'])): ?>
            <span class="badge bg-white text-primary mb-2"><?php echo htmlspecialchars($min['TIPO']); ?></span>
        <?php endif; ?>
        <h1 style="font-size:2.5rem;font-weight:800;"><?php echo htmlspecialchars($min['NOMBRE']); ?></h1>
        <?php if (!empty($min['DIRECTOR_NOMBRE'])): ?>
            <p style="opacity:.85;margin:0;"><i class="fas fa-user-tie me-2"></i>Director: <?php echo htmlspecialchars($min['DIRECTOR_NOMBRE']); ?></p>
        <?php endif; ?>
    </div>
</div>

<main class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="portal-card mb-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-3"><i class="fas fa-info-circle me-2 text-primary"></i>Acerca del ministerio</h4>
                    <p style="line-height:1.8;color:#495057;"><?php echo nl2br(htmlspecialchars($min['DESCRIPCION'] ?? 'Sin descripción disponible.')); ?></p>
                </div>
            </div>

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
                                         style="width:70px;height:70px;border-radius:50%;object-fit:cover;margin:0 auto .75rem;display:block;" alt="">
                                <?php else: ?>
                                    <div style="width:70px;height:70px;background:linear-gradient(135deg,#825ee4,#5e72e4);display:flex;align-items:center;justify-content:center;border-radius:50%;margin:0 auto .75rem;">
                                        <i class="fas fa-user text-white fs-4"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="member-name"><?php echo htmlspecialchars($m['NOMBRE']); ?></div>
                                <div class="member-cargo"><?php echo htmlspecialchars($m['CARGO']); ?></div>
                                <?php if (!empty($m['EMAIL'])): ?>
                                    <a href="mailto:<?php echo htmlspecialchars($m['EMAIL']); ?>"
                                       style="font-size:.75rem;color:#825ee4;text-decoration:none;display:block;margin-top:.3rem;">
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

            <?php if (!empty($galeria)): ?>
            <div class="portal-card">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-3"><i class="fas fa-images me-2 text-primary"></i>Galería</h4>
                    <div class="row g-2">
                        <?php foreach ($galeria as $img): ?>
                        <div class="col-4 col-md-3">
                            <img src="<?php echo $siteURL . htmlspecialchars($img); ?>"
                                 class="img-fluid rounded" style="height:110px;width:100%;object-fit:cover;" alt="">
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-4">
            <div class="portal-card mb-3">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Información</h5>
                    <?php if (!empty($min['HORARIO'])): ?>
                    <div class="d-flex gap-2 mb-2">
                        <i class="fas fa-clock text-primary mt-1"></i>
                        <div>
                            <div style="font-size:.75rem;color:#8392ab;text-transform:uppercase;">Horario</div>
                            <div style="font-size:.9rem;"><?php echo htmlspecialchars($min['HORARIO']); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($min['LUGAR'])): ?>
                    <div class="d-flex gap-2 mb-2">
                        <i class="fas fa-map-marker-alt text-primary mt-1"></i>
                        <div>
                            <div style="font-size:.75rem;color:#8392ab;text-transform:uppercase;">Lugar</div>
                            <div style="font-size:.9rem;"><?php echo htmlspecialchars($min['LUGAR']); ?></div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?php echo $portalURL; ?>ministerios" class="btn-portal-outline w-100 text-center d-block py-2">
                <i class="fas fa-arrow-left me-2"></i>Ver todos los ministerios
            </a>
        </div>
    </div>
</main>

<?php include('assets/php/footer.php'); ?>
