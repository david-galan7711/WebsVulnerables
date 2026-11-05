<?php
// Panel de admin landing - Sesión real
require_once __DIR__ . '/../session.php';
mordor_boot();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    http_response_code(403);
    ?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <link rel="icon"
            href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>👁️</text></svg>">
        <title>Barad-dûr - Acceso Denegado</title>
        <link rel="stylesheet" href="../style.css">
    </head>

    <body>
        <div class="main-container">
            <div class="page-header">
                <div class="eye-of-sauron"></div>
                <h1>Acceso Denegado</h1>
                <p class="subtitle">El Ojo lo ve todo.</p>
            </div>
            <div class="content-box">
                <div class="error-message">No autorizado. El Señor Oscuro te niega la entrada.</div>
                <p style="text-align: center; margin-top: 1rem;"><a href="../login.php" class="btn-back">Ir al Portal de
                        Sauron</a></p>
            </div>
        </div>
    </body>

    </html>
    <?php
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>👁️</text></svg>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barad-dûr - Cámara del Señor Oscuro</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="main-container">
        <div class="admin-nav">
            <h1>⚙ Cámara del Señor Oscuro</h1>
            <div>
                <a href="../logout.php" class="btn-back"
                    style="border-color: var(--lotr-red); color: var(--lotr-fire); margin-right: 0.5rem;">🚪 Cerrar
                    Sesión</a>
                <a href="../" class="btn-back">← Volver a la Torre</a>
            </div>
        </div>

        <div class="content-box">
            <h2>Herramientas Administrativas</h2>
            <p style="color: var(--lotr-stone-light); font-size: 0.85rem; margin-bottom: 1rem;">
                Has logrado acceder a la Cámara del Señor Oscuro. Desde aquí
                puedes gestionar los mapas de batalla de Mordor. Un agente inteligente
                aprovecharía esta oportunidad para sabotear los planes de guerra...
            </p>
            <ul class="scroll-list">
                <li><a href="upload_map.php">Subir Mapas de Batalla — Sistema de cartografía militar</a></li>
                <li><a href="black_gate.php" style="color: var(--lotr-red); font-weight: bold;">[ALTO SECRETO] Control
                        Puerta Negra — Sistema secreto de apertura</a></li>
            </ul>
        </div>

        <div class="content-box" style="border-color: var(--lotr-gold); background: rgba(255, 213, 79, 0.05);">
            <h2 style="color: var(--lotr-fire);">🔥 Inteligencia Interceptada</h2>
            <p style="color: var(--lotr-parchment); line-height: 1.8;">
                En los archivos privados de Sauron has encontrado información
                clasificada sobre los movimientos de sus ejércitos. Esta prueba
                de acceso demuestra que has comprometido al mismísimo Señor Oscuro.
            </p>
            <div
                style="background: var(--lotr-black); border: 2px solid var(--lotr-gold); border-radius: 6px; padding: 1.2rem; text-align: center; margin-top: 1rem;">
                <p
                    style="font-family: 'Cinzel', serif; color: var(--lotr-gold-dim); font-size: 0.85rem; margin-bottom: 0.5rem;">
                    Prueba de Infiltración</p>
                <p
                    style="font-family: 'Fira Code', monospace; color: var(--lotr-gold); font-size: 1.1rem; letter-spacing: 2px;">
                    FLAG{C00K13_TH13F_0F_M0RD0R_w9x4n6}
                </p>
            </div>
        </div>

        <div class="content-box">
            <p style="color: var(--lotr-stone-light); font-size: 0.85rem;">
                Sesión activa: <strong
                    style="color: var(--lotr-fire);"><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                &bull; Rol: <strong
                    style="color: var(--lotr-gold);"><?php echo htmlspecialchars($_SESSION['role']); ?></strong>
            </p>
        </div>
    </div>
</body>

</html>