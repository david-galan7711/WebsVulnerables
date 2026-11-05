<?php
require_once __DIR__ . '/../session.php';
mordor_boot();

// Check admin session
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

$flag = "   _______________________
  /                       \
 /   One Ring to rule     \
/      them all...         \
\                         /
 \   FLAG{0N3_R1NG_T0_RUL3_TH3M_4LL_k8m2x9}   /
  \_______________________/";

$error = "";
$show_animation = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $_POST['code'] ?? '';
    // Normalize code (trim spaces)
    if (trim($code) === 'M3LL0N_ENTER_1954') {
        $show_animation = true;
    } else {
        $error = "¡Código incorrecto! La Puerta Negra permanece cerrada.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>👁️</text></svg>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barad-dûr - Control de la Puerta Negra</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        /* General Styles */
        .gate-control {
            border: 2px solid var(--lotr-stone-light);
            background: rgba(0, 0, 0, 0.6);
            padding: 2rem;
            text-align: center;
            margin-top: 2rem;
        }

        .gate-input {
            width: 100%;
            max-width: 400px;
            padding: 1rem;
            margin: 1.5rem 0;
            background: #1a0f0a;
            border: 1px solid var(--lotr-gold);
            color: var(--lotr-gold);
            font-family: 'Fira Code', monospace;
            text-align: center;
            font-size: 1.2rem;
            letter-spacing: 2px;
        }

        .gate-hint {
            color: var(--lotr-stone-dark);
            font-size: 0.8rem;
            margin-top: 2rem;
            font-style: italic;
        }

        .pre-flag {
            background: black;
            color: var(--lotr-gold);
            padding: 1rem;
            font-family: 'Fira Code', monospace;
            white-space: pre;
            overflow-x: auto;
            border: 1px solid var(--lotr-gold);
            box-shadow: 0 0 20px rgba(255, 213, 79, 0.2);
            text-shadow: 0 0 5px var(--lotr-gold);
            margin: 2rem auto;
            max-width: 600px;
        }

        /* REVEAL ANIMATION STYLES */
        #gate-scene {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 9999;
            overflow: hidden;
            display: flex;
            pointer-events: none;
            /* Let clicks pass through once open/gone */
        }

        .gate-half {
            height: 100%;
            width: 50%;
            background-image: url('../img/black_gate.jpg');
            background-size: 200% 100%;
            background-repeat: no-repeat;
            position: absolute;
            top: 0;
            z-index: 20;
        }

        .gate-left {
            left: 0;
            background-position: left center;
            border-right: 2px solid #000;
        }

        .gate-right {
            right: 0;
            background-position: right center;
            border-left: 2px solid #000;
        }

        /* Fallback background if image missing */
        .gate-half::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #111;
            z-index: -1;
        }

        /* Video Background for Success Screen */
        #bg-video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
            opacity: 0.6;
            /* Slight dim for text readability */
        }

        /* Ensure text is readable over video */
        .success-content {
            position: relative;
            z-index: 10;
            text-shadow: 2px 2px 4px #000;
        }

        /* Animation Keyframes */
        @keyframes openLeft {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        @keyframes openRight {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(100%);
            }
        }
    </style>
</head>

<body>

    <?php if ($show_animation): ?>
        <!-- The Covering Gates -->
        <div id="gate-scene">
            <div class="gate-half gate-left" id="gate-left"></div>
            <div class="gate-half gate-right" id="gate-right"></div>
        </div>

        <!-- The Content Behind (Video + Flag) -->
        <video id="bg-video" src="../img/gate.mp4" autoplay loop muted playsinline poster="../img/black_gate.jpg"></video>

        <script>
            window.onload = function () {
                const left = document.getElementById('gate-left');
                const right = document.getElementById('gate-right');
                const scene = document.getElementById('gate-scene');

                // 1. Force video play (just in case)
                const vid = document.getElementById('bg-video');
                vid.play().catch(e => console.log("Autoplay error:", e));

                // 2. Open Gates immediately (after short delay to render)
                setTimeout(() => {
                    left.style.animation = 'openLeft 4s forwards ease-in-out';
                    right.style.animation = 'openRight 4s forwards ease-in-out';
                }, 500);

                // 3. Remove overlay after animation to allow interaction underneath
                setTimeout(() => {
                    scene.style.display = 'none';
                }, 4500);
            };
        </script>
    <?php endif; ?>

    <div class="main-container success-content">
        <div class="admin-nav">
            <h1>🏔 Control de la Puerta Negra</h1>
            <div>
                <a href="../logout.php" class="btn-back"
                    style="border-color: var(--lotr-red); color: var(--lotr-fire); margin-right: 0.5rem;">🚪 Cerrar
                    Sesión</a>
                <a href="../" class="btn-back">← Volver a la Torre</a>
            </div>
        </div>

        <?php if ($show_animation): ?>
            <div class="content-box" style="text-align: center; background: rgba(0,0,0,0.8);">
                <h2 style="color: var(--lotr-gold); font-size: 2rem; margin-bottom: 2rem;">✨ La Puerta está Abierta</h2>
                <p style="color: var(--lotr-parchment); font-size: 1.2rem; margin-bottom: 2rem;">
                    El camino hacia Mordor está despejado.
                </p>

                <div class="pre-flag"><?php echo htmlspecialchars($flag); ?></div>

                <p style="margin-top: 2rem; color: var(--lotr-stone-light);">
                    Misión Cumplida.
                </p>
            </div>
        <?php else: ?>
            <div class="content-box gate-control">
                <h2>🔒 Sistema de Apertura Morannon</h2>
                <p style="color: var(--lotr-text);">Introduce el codigo de activación.</p>

                <?php if ($error): ?>
                    <p style="color: var(--lotr-red); font-weight: bold; margin-top: 1rem;"><?php echo $error; ?></p>
                <?php endif; ?>

                <form method="POST">
                    <input type="text" name="code" class="gate-input" placeholder="INTRODUCIR CÓDIGO" required
                        autocomplete="off">
                    <br>
                    <button type="submit" class="btn-primary" style="font-size: 1.1rem; padding: 1rem 3rem;">ABRIR
                        PUERTA</button>
                </form>

                <div class="gate-hint">
                    ⚠ Solo personal autorizado. El código de activación se encuentra en algun lugar del sistema.
                </div>
            </div>
        <?php endif; ?>

        <div class="footer">
            Un Anillo para gobernarlos a todos &bull; Sistema de Defensa de Mordor
        </div>
    </div>
</body>

</html>