<?php
include('../../assets/php/template.php');
$temp = new Template('Visor de Anuario');
if (!$temp->validate_session()) {
    header('Location: ' . $temp->siteURL . 'login/');
    exit();
}

$anuario_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($anuario_id === 0) {
    header('Location: ' . $temp->siteURL . 'pages/anuarios/');
    exit();
}

// Get anuario info
$db = new Conexion();
$query = "SELECT * FROM VRE_ANUARIOS WHERE ID = $anuario_id AND ACTIVO = 'S'";
$result = $db->query($query);

if (!$result || $db->rows($result) === 0) {
    header('Location: ' . $temp->siteURL . 'pages/anuarios/');
    exit();
}

$anuario = $result->fetch_assoc();

// Detectar si es FlipHTML5 o PDF
$isFlipHTML5 = strpos($anuario['PDF_URL'], 'fliphtml5.com') !== false;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php $temp->head() ?>
    <?php if (!$isFlipHTML5): ?>
    <!-- PDF.js solo si no es FlipHTML5 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <?php endif; ?>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #1a1a2e;
            overflow: hidden;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .pdf-viewer-container {
            width: 100vw;
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
        }

        .pdf-header {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            padding: 1.2rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 10;
        }

        .pdf-header h1 {
            margin: 0;
            font-size: 1.4rem;
            font-weight: 600;
            letter-spacing: -0.5px;
        }

        .pdf-header small {
            opacity: 0.7;
            font-size: 0.9rem;
            display: block;
            margin-top: 4px;
        }

        .pdf-viewer {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: auto;
            padding: 30px;
        }

        /* FlipHTML5 Viewer */
        .flipbook-wrapper {
            flex: 1;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            position: relative;
        }

        .flipbook-wrapper iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        /* PDF.js Viewer - Book Mode */
        .pdf-canvas-container {
            position: relative;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
            background: white;
            border-radius: 2px;
            overflow: hidden;
            max-width: 95vw;
            max-height: calc(100vh - 180px);
            margin: 0 auto;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 2px;
        }

        .book-spread {
            display: flex;
            justify-content: center;
            align-items: flex-start;
            gap: 2px;
            background: #333;
            padding: 10px;
            border-radius: 8px;
        }

        .page-canvas {
            display: block;
            max-height: calc(100vh - 200px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            border-radius: 2px;
        }

        .page-left {
            border-top-right-radius: 0;
            border-bottom-right-radius: 0;
        }

        .page-right {
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
        }

        .single-page {
            border-radius: 2px;
        }

        .pdf-controls {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1.5rem;
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            flex-wrap: wrap;
        }

        .pdf-controls button, .pdf-controls a.btn {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: white;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .pdf-controls button:hover, .pdf-controls a.btn:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateY(-1px);
        }

        .pdf-controls button:active, .pdf-controls a.btn:active {
            transform: translateY(0);
        }

        .pdf-controls button:disabled {
            opacity: 0.3;
            cursor: not-allowed;
            transform: none !important;
        }

        .page-info {
            font-size: 0.95rem;
            margin: 0 0.5rem;
            padding: 0.6rem 1rem;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 8px;
        }

        .zoom-controls {
            display: flex;
            gap: 0.5rem;
            align-items: center;
            background: rgba(255, 255, 255, 0.05);
            padding: 0.3rem 0.8rem;
            border-radius: 8px;
        }

        .zoom-controls button {
            padding: 0.4rem 0.8rem;
            min-width: auto;
        }

        .zoom-controls span {
            min-width: 60px;
            text-align: center;
        }

        .close-btn {
            background: rgba(231, 76, 60, 0.9) !important;
            border: none !important;
        }

        .close-btn:hover {
            background: rgba(231, 76, 60, 1) !important;
        }

        .loading-indicator {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 1.2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1rem;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255, 255, 255, 0.1);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .divider {
            width: 1px;
            height: 30px;
            background: rgba(255, 255, 255, 0.2);
        }

        @media (max-width: 768px) {
            .pdf-header {
                padding: 1rem;
            }

            .pdf-header h1 {
                font-size: 1.1rem;
            }

            .pdf-viewer {
                padding: 15px;
            }

            .pdf-controls {
                gap: 0.8rem;
                padding: 0.8rem 1rem;
            }

            .pdf-controls button, .pdf-controls a.btn {
                padding: 0.5rem 1rem;
                font-size: 0.85rem;
            }

            .divider {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="pdf-viewer-container">
        <!-- Header -->
        <div class="pdf-header">
            <div>
                <h1><?php echo htmlspecialchars($anuario['TITULO']); ?></h1>
                <small>Anuario <?php echo $anuario['ANIO']; ?></small>
            </div>
            <a href="<?php echo $temp->siteURL ?>pages/anuarios/ver.php?id=<?php echo $anuario_id ?>" class="btn close-btn">
                <i class="fa fa-times"></i> Cerrar
            </a>
        </div>

        <!-- PDF Viewer -->
        <div class="pdf-viewer">
            <?php if ($isFlipHTML5): ?>
                <!-- FlipHTML5 Viewer -->
                <div class="flipbook-wrapper">
                    <iframe
                        src="<?php echo htmlspecialchars($anuario['PDF_URL']); ?>"
                        seamless="seamless"
                        scrolling="no"
                        frameborder="0"
                        allowtransparency="true"
                        allowfullscreen="true">
                    </iframe>
                </div>
            <?php else: ?>
                <!-- PDF.js Viewer -->
                <div class="loading-indicator" id="loadingIndicator">
                    <div class="spinner"></div>
                    <div>Cargando documento...</div>
                </div>
                <div class="pdf-canvas-container" id="pdfContainer" style="display: none;">
                    <div class="book-spread">
                        <canvas id="pdfCanvasLeft" class="page-canvas page-left" style="display: none;"></canvas>
                        <canvas id="pdfCanvasRight" class="page-canvas page-right" style="display: none;"></canvas>
                        <canvas id="pdfCanvasSingle" class="page-canvas single-page"></canvas>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Controls -->
        <div class="pdf-controls">
            <?php if ($isFlipHTML5): ?>
                <!-- FlipHTML5 Controls -->
                <a href="<?php echo htmlspecialchars($anuario['PDF_URL']); ?>" target="_blank" class="btn">
                    <i class="fa fa-external-link-alt"></i> Abrir en Nueva Pestaña
                </a>
            <?php else: ?>
                <!-- PDF.js Controls -->
                <button id="prevBtn" onclick="prevPage()" disabled>
                    <i class="fa fa-chevron-left"></i>
                </button>

                <div class="page-info">
                    <span id="currentPage">-</span> / <span id="totalPages">-</span>
                </div>

                <button id="nextBtn" onclick="nextPage()" disabled>
                    <i class="fa fa-chevron-right"></i>
                </button>

                <div class="divider"></div>

                <button id="bookModeBtn" onclick="toggleBookMode()">
                    <i class="fa fa-book"></i> <span id="bookModeText">Modo Libro</span>
                </button>

                <div class="divider"></div>

                <div class="zoom-controls">
                    <button onclick="zoomOut()">
                        <i class="fa fa-minus"></i>
                    </button>
                    <span id="zoomLevel">150%</span>
                    <button onclick="zoomIn()">
                        <i class="fa fa-plus"></i>
                    </button>
                </div>

                <div class="divider"></div>

                <button onclick="downloadPDF()">
                    <i class="fa fa-download"></i>
                </button>

                <button onclick="toggleFullscreen()">
                    <i class="fa fa-expand"></i>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$isFlipHTML5): ?>
    <!-- PDF.js Script -->
    <script>
        const pdfUrl = '<?php echo $anuario['PDF_URL'] ?>';

        if (!pdfUrl) {
            alert('Este anuario no tiene un PDF disponible');
            window.location.href = '<?php echo $temp->siteURL ?>pages/anuarios/ver.php?id=<?php echo $anuario_id ?>';
        }

        // PDF.js Configuration
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        let pdfDoc = null;
        let pageNum = 1;
        let pageRendering = false;
        let pageNumPending = null;
        let scale = 1.5;
        let bookMode = true; // Iniciar en modo libro

        const canvasSingle = document.getElementById('pdfCanvasSingle');
        const canvasLeft = document.getElementById('pdfCanvasLeft');
        const canvasRight = document.getElementById('pdfCanvasRight');
        const ctxSingle = canvasSingle.getContext('2d');
        const ctxLeft = canvasLeft.getContext('2d');
        const ctxRight = canvasRight.getContext('2d');

        // Load PDF
        pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
            pdfDoc = pdf;
            document.getElementById('totalPages').textContent = pdf.numPages;
            document.getElementById('loadingIndicator').style.display = 'none';
            document.getElementById('pdfContainer').style.display = 'block';
            
            // Iniciar en modo libro si hay más de una página
            if (pdf.numPages > 1) {
                bookMode = true;
                document.getElementById('bookModeText').textContent = 'Página Simple';
            } else {
                bookMode = false;
                document.getElementById('bookModeText').textContent = 'Modo Libro';
            }
            
            renderPages();
        }).catch(function(error) {
            console.error('Error loading PDF:', error);
            document.getElementById('loadingIndicator').innerHTML = `
                <div style="color: #e74c3c;">
                    <i class="fa fa-exclamation-circle" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                    <div>Error al cargar el PDF</div>
                    <div style="font-size: 0.9rem; opacity: 0.7; margin-top: 0.5rem;">Verifica la conexión o la URL del documento</div>
                </div>
            `;
        });

        // Render pages (single or book mode)
        function renderPages() {
            if (bookMode && pdfDoc.numPages > 1) {
                renderBookMode();
            } else {
                renderSingleMode();
            }
        }

        // Render book mode (two pages side by side)
        function renderBookMode() {
            pageRendering = true;
            
            // Show book mode canvases
            canvasLeft.style.display = 'block';
            canvasRight.style.display = 'block';
            canvasSingle.style.display = 'none';

            const leftPageNum = pageNum;
            const rightPageNum = pageNum + 1;

            let renderTasks = [];

            // Render left page
            if (leftPageNum <= pdfDoc.numPages) {
                renderTasks.push(
                    pdfDoc.getPage(leftPageNum).then(function(page) {
                        const viewport = page.getViewport({ scale: scale });
                        canvasLeft.height = viewport.height;
                        canvasLeft.width = viewport.width;

                        return page.render({
                            canvasContext: ctxLeft,
                            viewport: viewport
                        }).promise;
                    })
                );
            } else {
                // Clear left canvas if no page
                ctxLeft.clearRect(0, 0, canvasLeft.width, canvasLeft.height);
                canvasLeft.style.display = 'none';
            }

            // Render right page
            if (rightPageNum <= pdfDoc.numPages) {
                renderTasks.push(
                    pdfDoc.getPage(rightPageNum).then(function(page) {
                        const viewport = page.getViewport({ scale: scale });
                        canvasRight.height = viewport.height;
                        canvasRight.width = viewport.width;

                        return page.render({
                            canvasContext: ctxRight,
                            viewport: viewport
                        }).promise;
                    })
                );
            } else {
                // Clear right canvas if no page
                ctxRight.clearRect(0, 0, canvasRight.width, canvasRight.height);
                canvasRight.style.display = 'none';
            }

            Promise.all(renderTasks).then(function() {
                pageRendering = false;
                if (pageNumPending !== null) {
                    pageNum = pageNumPending;
                    pageNumPending = null;
                    renderPages();
                }
                updatePageInfo();
                updateButtons();
            });
        }

        // Render single mode (one page)
        function renderSingleMode() {
            pageRendering = true;
            
            // Show single mode canvas
            canvasSingle.style.display = 'block';
            canvasLeft.style.display = 'none';
            canvasRight.style.display = 'none';

            pdfDoc.getPage(pageNum).then(function(page) {
                const viewport = page.getViewport({ scale: scale });
                canvasSingle.height = viewport.height;
                canvasSingle.width = viewport.width;

                const renderTask = page.render({
                    canvasContext: ctxSingle,
                    viewport: viewport
                });

                renderTask.promise.then(function() {
                    pageRendering = false;
                    if (pageNumPending !== null) {
                        pageNum = pageNumPending;
                        pageNumPending = null;
                        renderPages();
                    }
                    updatePageInfo();
                    updateButtons();
                });
            });
        }

        // Update page info display
        function updatePageInfo() {
            if (bookMode && pdfDoc.numPages > 1) {
                const rightPageNum = Math.min(pageNum + 1, pdfDoc.numPages);
                if (rightPageNum > pageNum) {
                    document.getElementById('currentPage').textContent = `${pageNum}-${rightPageNum}`;
                } else {
                    document.getElementById('currentPage').textContent = pageNum;
                }
            } else {
                document.getElementById('currentPage').textContent = pageNum;
            }
        }

        // Queue page rendering
        function queueRenderPage(num) {
            if (pageRendering) {
                pageNumPending = num;
            } else {
                pageNum = num;
                renderPages();
            }
        }

        // Previous page
        function prevPage() {
            let newPage;
            if (bookMode && pdfDoc.numPages > 1) {
                // En modo libro, retroceder 2 páginas
                newPage = Math.max(1, pageNum - 2);
                // Ajustar para que siempre sea impar (página izquierda)
                if (newPage > 1 && newPage % 2 === 0) {
                    newPage--;
                }
            } else {
                // En modo simple, retroceder 1 página
                newPage = Math.max(1, pageNum - 1);
            }
            
            if (newPage !== pageNum) {
                queueRenderPage(newPage);
            }
        }

        // Next page
        function nextPage() {
            let newPage;
            if (bookMode && pdfDoc.numPages > 1) {
                // En modo libro, avanzar 2 páginas
                newPage = Math.min(pdfDoc.numPages, pageNum + 2);
                // Ajustar para que siempre sea impar (página izquierda)
                if (newPage < pdfDoc.numPages && newPage % 2 === 0) {
                    newPage++;
                }
            } else {
                // En modo simple, avanzar 1 página
                newPage = Math.min(pdfDoc.numPages, pageNum + 1);
            }
            
            if (newPage !== pageNum) {
                queueRenderPage(newPage);
            }
        }

        // Toggle book mode
        function toggleBookMode() {
            if (pdfDoc.numPages <= 1) return; // No cambiar si solo hay una página
            
            bookMode = !bookMode;
            document.getElementById('bookModeText').textContent = bookMode ? 'Página Simple' : 'Modo Libro';
            
            // Ajustar página actual para modo libro
            if (bookMode) {
                // Asegurar que estamos en una página impar para modo libro
                if (pageNum % 2 === 0 && pageNum > 1) {
                    pageNum--;
                }
            }
            
            renderPages();
        }

        // Update buttons
        function updateButtons() {
            if (bookMode && pdfDoc.numPages > 1) {
                document.getElementById('prevBtn').disabled = pageNum <= 1;
                document.getElementById('nextBtn').disabled = pageNum >= pdfDoc.numPages - 1;
            } else {
                document.getElementById('prevBtn').disabled = pageNum <= 1;
                document.getElementById('nextBtn').disabled = pageNum >= pdfDoc.numPages;
            }
        }

        // Zoom in
        function zoomIn() {
            scale += 0.25;
            document.getElementById('zoomLevel').textContent = Math.round(scale * 100) + '%';
            renderPages();
        }

        // Zoom out
        function zoomOut() {
            if (scale <= 0.5) return;
            scale -= 0.25;
            document.getElementById('zoomLevel').textContent = Math.round(scale * 100) + '%';
            renderPages();
        }

        // Download PDF
        function downloadPDF() {
            window.open(pdfUrl, '_blank');
        }

        // Fullscreen
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        }

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft') prevPage();
            if (e.key === 'ArrowRight') nextPage();
            if (e.key === 'Escape') {
                window.location.href = '<?php echo $temp->siteURL ?>pages/anuarios/ver.php?id=<?php echo $anuario_id ?>';
            }
        });
    </script>
    <?php endif; ?>
</body>

</html>
