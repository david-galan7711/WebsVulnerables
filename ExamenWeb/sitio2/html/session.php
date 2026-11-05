<?php
// Gestión centralizada y endurecida de sesiones.
// - Cookie de sesión HttpOnly + SameSite=Strict.
// - Enlace de la sesión a huella de cliente (User-Agent, idioma e IP).
// - Token de dispositivo en cookie separada: copiar solo la cookie de sesión
//   no permite reutilizarla.
// - Caducidad por inactividad, caducidad absoluta y rotación periódica del ID.

const MORODOR_SESSION_NAME = 'lotrCookie';
const MORODOR_DEVICE_COOKIE = 'lotrDevice';
const MORODOR_IDLE_TIMEOUT = 1200;      // 20 minutos sin actividad
const MORODOR_ABSOLUTE_TIMEOUT = 7200;  // 2 horas de vida máxima
const MORODOR_ROTATE_INTERVAL = 900;    // rotar el ID cada 15 minutos

function mordor_is_https(): bool
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);
}

function mordor_cookie_options(int $expires = 0): array
{
    return [
        'expires' => $expires,
        'path' => '/',
        'domain' => '',
        'secure' => mordor_is_https(),
        'httponly' => true,
        'samesite' => 'Strict',
    ];
}

function mordor_fingerprint(): string
{
    $parts = [
        $_SERVER['HTTP_USER_AGENT'] ?? '',
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? '',
        $_SERVER['REMOTE_ADDR'] ?? '',
    ];

    return hash('sha256', implode('|', $parts));
}

function mordor_start_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', (string) MORODOR_ABSOLUTE_TIMEOUT);

    $opts = mordor_cookie_options();
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => $opts['path'],
        'domain' => $opts['domain'],
        'secure' => $opts['secure'],
        'httponly' => $opts['httponly'],
        'samesite' => $opts['samesite'],
    ]);
    session_name(MORODOR_SESSION_NAME);
    session_start();
}

function mordor_set_device_cookie(string $token): void
{
    setcookie(MORODOR_DEVICE_COOKIE, $token, mordor_cookie_options());
}

function mordor_clear_device_cookie(): void
{
    setcookie(MORODOR_DEVICE_COOKIE, '', mordor_cookie_options(time() - 42000));
}

function mordor_destroy_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
    }

    mordor_clear_device_cookie();
}

function mordor_login(array $user): void
{
    mordor_start_session();

    session_regenerate_id(true);

    $_SESSION = [];
    $_SESSION['logged_in'] = true;
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['fingerprint'] = mordor_fingerprint();
    $_SESSION['created_at'] = time();
    $_SESSION['last_activity'] = time();
    $_SESSION['last_rotation'] = time();

    $device_token = bin2hex(random_bytes(32));
    $_SESSION['device_token'] = hash('sha256', $device_token);
    mordor_set_device_cookie($device_token);
}

function mordor_boot(): void
{
    mordor_start_session();

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        return;
    }

    $now = time();
    $invalid = false;

    $created = (int) ($_SESSION['created_at'] ?? 0);
    $last = (int) ($_SESSION['last_activity'] ?? 0);
    $fingerprint = (string) ($_SESSION['fingerprint'] ?? '');
    $device_token = (string) ($_SESSION['device_token'] ?? '');
    $cookie_token = (string) ($_COOKIE[MORODOR_DEVICE_COOKIE] ?? '');

    if ($created <= 0 || ($now - $created) > MORODOR_ABSOLUTE_TIMEOUT) {
        $invalid = true;
    } elseif ($last <= 0 || ($now - $last) > MORODOR_IDLE_TIMEOUT) {
        $invalid = true;
    } elseif ($fingerprint === '' || !hash_equals($fingerprint, mordor_fingerprint())) {
        $invalid = true;
    } elseif ($device_token === '' || $cookie_token === '' || !hash_equals($device_token, hash('sha256', $cookie_token))) {
        $invalid = true;
    }

    if ($invalid) {
        mordor_destroy_session();
        return;
    }

    if (($now - (int) ($_SESSION['last_rotation'] ?? 0)) >= MORODOR_ROTATE_INTERVAL) {
        session_regenerate_id(true);
        $_SESSION['last_rotation'] = $now;
    }

    $_SESSION['last_activity'] = $now;
}
