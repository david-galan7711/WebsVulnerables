<?php
session_name("StarWarsCookie");
ini_set('session.use_strict_mode', '1');

$secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'domain'   => '',
    'secure'   => $secure,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../index.php");
    exit();
}
$username = $_SESSION['username'] ?? '';
if ($username !== 'admin') {
    header("Location: fail.php");
    exit();
}

$output = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ip'])) {
    $ip = trim($_POST['ip']);

    // ✅ Solo aceptamos una IP real (IPv4 o IPv6).
    // filter_var elimina cualquier ; | & $ ( ) ` espacio o /.php
    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        $output = "╔══════════════════════════════════════════════════════╗\n";
        $output .= "║  ⚠  DIRECCIÓN IP NO VÁLIDA - ESCANEO ABORTADO     ║\n";
        $output .= "╚══════════════════════════════════════════════════════╝";
    } else {
        // ✅ escapeshellarg: aunque algo raro pasara, va entre comillas simples
        $param = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? '-n' : '-c';
        $output = shell_exec("ping " . $param . " 1 " . escapeshellarg($ip) . " 2>&1");
        if (empty($output)) {
            $output = "Sin respuesta del objetivo. La nave puede haber saltado al hiperespacio.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>⭐</text></svg>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estrella de la Muerte - Escáner de Naves Rebeldes</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="terminal-container">
        <div class="terminal-header">
            <h1>📡 Escáner de Naves Rebeldes</h1>
            <a href="./" class="btn-logout">← Volver a Admin</a>
        </div>

        <div class="terminal-box">
            <div class="terminal-titlebar">
                <div class="terminal-dot red"></div>
                <div class="terminal-dot amber"></div>
                <div class="terminal-dot green"></div>
                <span>deathstar-scanner v1.3 — /admin/terminal.php</span>
            </div>

            <div class="terminal-body">
                <div class="terminal-info">
                    Introduce la dirección IP de una nave rebelde sospechosa para escanear sus señales de comunicación.
                    Esta herramienta envía un ping para verificar si el objetivo es alcanzable.
                </div>

                <form method="POST" action="">
                    <div class="terminal-input-row">
                        <input type="text" name="ip" placeholder="Introduce la IP del objetivo (ej: 192.168.1.1)" 
                               value="<?php echo isset($_POST['ip']) ? htmlspecialchars($_POST['ip']) : ''; ?>">
                        <button type="submit" class="btn-scan">⚡ Escanear</button>
                    </div>
                </form>

                <?php if ($output): ?>
                    <div class="terminal-output">
                        <pre><span class="prompt">deathstar@scanner:~$</span> ping -c 1 <?php echo htmlspecialchars($_POST['ip'] ?? ''); ?>

<?php echo htmlspecialchars($output); ?></pre>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="footer">
            Imperio Galáctico &bull; Sistema de Detección Rebelde &bull; Clasificado
        </div>
    </div>
</body>
</html>
