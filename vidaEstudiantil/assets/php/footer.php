<?php
// Cargar configuración del footer
if (!isset($homeConfig)) {
    $rutaDB = $_SERVER['DOCUMENT_ROOT'] . '/vida_estudiantil_Hitha/cpanel/assets/API/db.php';
    if (file_exists($rutaDB) && !class_exists('Conexion')) {
        include($rutaDB);
    }
    if (class_exists('Conexion')) {
        $dbFooter = new Conexion();
        $homeConfig = [];
        $sqlFooterConfig = $dbFooter->query("SELECT SECCION, CLAVE, VALOR FROM VRE_HOME_CONFIG WHERE ACTIVO = 'S' AND SECCION = 'footer'");
        if ($sqlFooterConfig && $dbFooter->rows($sqlFooterConfig) > 0) {
            while ($row = $sqlFooterConfig->fetch_assoc()) {
                $homeConfig[$row['SECCION']][$row['CLAVE']] = $row['VALOR'];
            }
        }
    }
}

$footerDesc = $homeConfig['footer']['descripcion'] ?? 'Portal de Vida Estudiantil — Descubre los clubes, ministerios y actividades que hacen única la experiencia universitaria.';
$footerDireccion = $homeConfig['footer']['direccion'] ?? 'Ave. Ignacio Morones Prieto 4500';
$footerTelefono = $homeConfig['footer']['telefono'] ?? '+52 81 8215-1000';
$footerEmail = $homeConfig['footer']['email'] ?? 'vidaestudiantil@um.edu.mx';
$footerFacebook = $homeConfig['footer']['facebook'] ?? '';
$footerInstagram = $homeConfig['footer']['instagram'] ?? '';
$footerTwitter = $homeConfig['footer']['twitter'] ?? '';
$footerYoutube = $homeConfig['footer']['youtube'] ?? '';
?>

<!-- Footer -->
<footer class="portal-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <h6><i class="fas fa-university me-2"></i>Universidad de Montemorelos</h6>
                <p style="font-size:.875rem;"><?php echo htmlspecialchars($footerDesc); ?></p>

                <!-- Redes Sociales -->
                <?php if ($footerFacebook || $footerInstagram || $footerTwitter || $footerYoutube): ?>
                <div class="d-flex gap-3 mt-3">
                    <?php if ($footerFacebook): ?>
                        <a href="<?php echo htmlspecialchars($footerFacebook); ?>" target="_blank" rel="noopener"
                           class="text-white" style="font-size:1.3rem;" title="Facebook">
                            <i class="fab fa-facebook"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($footerInstagram): ?>
                        <a href="<?php echo htmlspecialchars($footerInstagram); ?>" target="_blank" rel="noopener"
                           class="text-white" style="font-size:1.3rem;" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($footerTwitter): ?>
                        <a href="<?php echo htmlspecialchars($footerTwitter); ?>" target="_blank" rel="noopener"
                           class="text-white" style="font-size:1.3rem;" title="Twitter">
                            <i class="fab fa-twitter"></i>
                        </a>
                    <?php endif; ?>
                    <?php if ($footerYoutube): ?>
                        <a href="<?php echo htmlspecialchars($footerYoutube); ?>" target="_blank" rel="noopener"
                           class="text-white" style="font-size:1.3rem;" title="YouTube">
                            <i class="fab fa-youtube"></i>
                        </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-lg-2 col-6">
                <h6>Secciones</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-1"><a href="<?php echo $portalURL; ?>"><i class="fas fa-chevron-right me-1" style="font-size:.7rem;"></i>Inicio</a></li>
                    <li class="mb-1"><a href="<?php echo $portalURL; ?>clubes"><i class="fas fa-chevron-right me-1" style="font-size:.7rem;"></i>Clubes</a></li>
                    <li class="mb-1"><a href="<?php echo $portalURL; ?>ministerios"><i class="fas fa-chevron-right me-1" style="font-size:.7rem;"></i>Ministerios</a></li>
                    <li class="mb-1"><a href="<?php echo $portalURL; ?>deportes"><i class="fas fa-chevron-right me-1" style="font-size:.7rem;"></i>Deportes</a></li>
                    <li class="mb-1"><a href="<?php echo $portalURL; ?>instalaciones"><i class="fas fa-chevron-right me-1" style="font-size:.7rem;"></i>Instalaciones</a></li>
                    <li class="mb-1"><a href="<?php echo $portalURL; ?>anuarios"><i class="fas fa-chevron-right me-1" style="font-size:.7rem;"></i>Anuarios</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-6">
                <h6>Contacto</h6>
                <ul class="list-unstyled mb-0" style="font-size:.875rem;">
                    <?php if ($footerDireccion): ?>
                        <li class="mb-1"><i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($footerDireccion); ?></li>
                    <?php endif; ?>
                    <?php if ($footerTelefono): ?>
                        <li class="mb-1"><i class="fas fa-phone me-2"></i><?php echo htmlspecialchars($footerTelefono); ?></li>
                    <?php endif; ?>
                    <?php if ($footerEmail): ?>
                        <li class="mb-1"><i class="fas fa-envelope me-2"></i><?php echo htmlspecialchars($footerEmail); ?></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        <div class="footer-bottom text-center">
            <span>&copy; <?php echo date('Y'); ?> Universidad de Montemorelos — Vida Estudiantil</span>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
