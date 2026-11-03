<?php
session_name("StarTrekCookie");
session_start();
require_once __DIR__ . '/../db.php';

// Verificar si es admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Si no es admin, redirigir al login
    header("Location: ../login.php?error=access_denied");
    exit();
}

$msg = "";
$error = "";

// Directorio de subida
$upload_dir = __DIR__ . '/uploads/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['sys_file'])) {
    $file = $_FILES['sys_file'];
    $filename = $file['name'];
    $tmp_name = $file['tmp_name'];

    // --- INICIO DEL CÓDIGO SEGURO (Estilo DVWA Impossible) ---

    // 1. Obtener la extensión real (en minúsculas)
    $uploaded_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    // 2. Generar un nombre de archivo aleatorio y seguro (Evita Path Traversal y XSS)
    $nuevo_nombre = md5(uniqid() . $filename) . '.' . $uploaded_ext;
    $target_path = $upload_dir . $nuevo_nombre;

    // 3. Validaciones estrictas: Extensión, Tamaño y Contenido Real
    // Nota: He puesto un límite de 2MB, ajústalo según necesites.
    if ( ($uploaded_ext == 'jpg' || $uploaded_ext == 'jpeg' || $uploaded_ext == 'png') &&
         ($file['size'] < 2000000) && 
         (getimagesize($tmp_name) !== false) ) {

        // 4. Re-encodeo de la imagen (Strip Metadata / Destruye código malicioso)
        $tipo_mime = mime_content_type($tmp_name);
        
        if ($tipo_mime == 'image/jpeg') {
            $img = imagecreatefromjpeg($tmp_name);
            imagejpeg($img, $target_path . '.tmp', 100); 
        } elseif ($tipo_mime == 'image/png') {
            $img = imagecreatefrompng($tmp_name);
            imagepng($img, $target_path . '.tmp', 9);
        }

        if (isset($img)) {
            imagedestroy($img);

            // 5. Mover el archivo limpio a su destino final
            if (rename($target_path . '.tmp', $target_path)) {
                $msg = "Archivo de sistema subido correctamente: " . htmlspecialchars($nuevo_nombre);
            } else {
                $error = "Error al mover el archivo al repositorio de sistemas.";
            }
        } else {
            $error = "Error al procesar la imagen.";
        }

        // Limpieza de archivos temporales
        if (file_exists($target_path . '.tmp')) {
            unlink($target_path . '.tmp');
        }

    } else {
        $error = "Formato no válido. El sistema solo acepta esquemas visuales reales (.jpg, .jpeg, .png).";
    }
    // --- FIN DEL CÓDIGO SEGURO ---
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🖖</text></svg>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>USS Enterprise - Gestión de Sistemas</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <div class="lcars-bar-top">
        <div class="lcars-elbow-top">NCC-1701</div>
        <div class="lcars-strip-top">
            <div class="block"></div>
            <div class="block"></div>
            <div class="block"></div>
            <div class="block"></div>
            <div class="block"></div>
        </div>
    </div>

    <div class="lcars-layout">
        <div class="lcars-sidebar">
            <div class="panel orange">SISTEMAS</div>
            <div class="panel blue">UPLOAD</div>
            <div class="panel purple">CONFIG</div>
            <div class="panel yellow">LOGS</div>
        </div>

        <div class="lcars-content">
            <div class="page-header">
                <h1>Gestión de Sistemas</h1>
                <p class="subtitle">Panel de Mantenimiento — Solo Personal Autorizado</p>
            </div>

            <div class="nav-links">
                <a href="../index.php" class="nav-link">← Volver al Puente</a>
                <a href="../admin/" class="nav-link">🔐 Panel Admin</a>
            </div>

            <div class="alert-box info">
                ℹ PANEL DE INGENIERÍA: Utilice esta interfaz para cargar esquemas de sistemas o actualizaciones de
                firmware visuales.
            </div>

            <div class="content-box">
                <h2>📤 Carga de Archivos de Sistema</h2>
                <p style="color: var(--lcars-text-dim); margin-bottom: 1rem;">
                    Sube esquemas en formato <strong>.jpg</strong> para su análisis por el ordenador central.
                </p>

                <?php if ($msg): ?>
                    <div class="alert-box success" style="border-color: var(--lcars-blue); color: var(--lcars-blue);">
                        ✓ <?php echo $msg; ?>
                        <br>
                        <small>Ruta: <code>uploads/<?php echo htmlspecialchars($filename); ?></code></small>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="alert-box danger">
                        ⚠ <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data"
                    style="background: rgba(255,153,0,0.05); padding: 1.5rem; border: 1px dashed var(--lcars-orange); border-radius: 8px;">
                    <div style="margin-bottom: 1rem;">
                        <label for="sys_file"
                            style="display: block; color: var(--lcars-orange); margin-bottom: 0.5rem; font-weight: bold;">Seleccionar
                            Archivo:</label>
                        <input type="file" name="sys_file" id="sys_file" style="color: var(--lcars-text);">
                    </div>
                    <button type="submit"
                        style="padding: 0.6rem 1.2rem; background: var(--lcars-orange); color: #000; border: none; border-radius: 4px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; cursor: pointer;">
                        ⬆ Iniciar Carga
                    </button>
                </form>
            </div>

        </div>
    </div>

    <div class="lcars-bar-bottom">
        <div class="lcars-elbow-bottom">INGENIERÍA</div>
        <div class="lcars-strip-bottom">
            <div class="block"></div>
            <div class="block"></div>
            <div class="block"></div>
            <div class="block"></div>
        </div>
    </div>
</body>

</html>
