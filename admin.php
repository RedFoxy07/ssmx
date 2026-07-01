<?php
session_start();
$contraseña_correcta = "admin123";
$error = "";

if (isset($_POST['login'])) {
    if ($_POST['password'] === $contraseña_correcta) {
        $_SESSION['logueado'] = true;
    } else {
        $error = "Contraseña incorrecta. Intenta de nuevo.";
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SSMX Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>    
    <link rel="stylesheet" href="css/style.css"/>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js" defer></script>
    <link rel="apple-touch-icon" sizes="180x180" href="img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="img/favicon/favicon-16x16.png">
    <link rel="manifest" href="img/favicon/site.webmanifest">
    <link rel="shortcut icon" href="img/favicon/favicon.ico">
</head>
<body>
<header>
    <div class="logo-container" style="display: flex; align-items: center;">
        <a href="index.php" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
            <img src="img/logos/logo.png" alt="logo" class="logo-img">
            <span class="logo-text">System Seguridad MX</span>
        </a>
    </div>
    <nav>
        <ul class="menu">
            <?php if (isset($_SESSION['logueado'])) { ?>
                <a href="?logout=true" style="color: #ef4444; font-weight: bold;">Cerrar Sesión</a></li>
            <?php } ?>
        </ul>
    </nav>
</header>
<main> 
    <?php if (!isset($_SESSION['logueado'])) { ?>
        <div class="login-container">
            <h2>SSMX Admin</h2>
            <?php if($error) echo "<p class='error-msg'>$error</p>"; ?>
            <form method="POST" action="">
                <input type="password" name="password" placeholder="Ingresa tu contraseña" required>
                <button type="submit" name="login" class="btn-login">Entrar al Panel</button>
            </form>
        </div>

    <?php } else { ?>
        <div class="dashboard-wrapper">
            <h2 style="color: #ffffff; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; margin-bottom: 20px;">Últimas Cotizaciones</h2>
            
            <div class="tabla-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>#COT-1045</strong></td>
                            <td>Juan Pérez</td>
                            <td>55 1234 5678</td>
                            <td>Estado de México</td>
                            <td>Hoy, 10:30 AM</td>
                            <td>
                                <button class="btn-ver-detalle" onclick="abrirPanel()">Ver Detalles</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="overlay-panel" id="overlayPanel" onclick="cerrarPanel()"></div>
        
        <div class="panel-lateral" id="panelLateral">
            <div class="panel-cabecera">
                <h3>Detalle de Cotización</h3>
                <button class="btn-cerrar-panel" onclick="cerrarPanel()"><i class="fas fa-times"></i></button>
            </div>
            
            <div class="panel-cuerpo">
                <div class="info-cliente">
                    <p><strong>Cliente:</strong> Juan Pérez</p>
                    <p><strong>Teléfono:</strong> <a href="https://wa.me/525512345678">55 1234 5678 <i class="fab fa-whatsapp"></i></a></p>
                    <p><strong>Estado:</strong> Estado de México</p>
                    <p><strong>Requiere Factura:</strong> Sí</p>
                </div>
                <h4 style="margin-top: 20px; border-bottom: 1px solid #cbd5e1; padding-bottom: 5px;">Equipos Seleccionados</h4>
                <ul class="lista-equipos">
                    <li>4x Cámara Domo 2MP Análoga Hikvision</li>
                    <li>1x DVR 4 Canales 1080p</li>
                    <li>1x Disco Duro 1TB Western Digital</li>
                </ul>
                <div class="total-estimado">
                    <p>Total Equipos Estimado:</p>
                    <h3>$4,500.00 MXN</h3>
                </div>
            </div>
        </div>

    <?php } ?>
</main>
<footer>
    </footer>
<script>
    function abrirPanel() {
        document.getElementById('panelLateral').classList.add('activo');
        document.getElementById('overlayPanel').classList.add('activo');
        document.body.style.overflow = 'hidden'; // Evita que se haga scroll en el fondo
    }

    function cerrarPanel() {
        document.getElementById('panelLateral').classList.remove('activo');
        document.getElementById('overlayPanel').classList.remove('activo');
        document.body.style.overflow = 'auto'; // Vuelve a permitir scroll
    }
</script>
</body>
</html>