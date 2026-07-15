<?php
session_start();
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
require 'config.php';
$error="";
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($conn->connect_error) { die("Error de conexión: " . $conn->connect_error); }

if (isset($_SESSION['last_attempt_time']) && time() - $_SESSION['last_attempt_time'] > 900) {
    $_SESSION['login_attempts'] = 0;
}
$blocked = false;
if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 5) {
    if (time() - $_SESSION['last_attempt_time'] < 900) {
        $blocked = true;
        $error = "Demasiados intentos fallidos. Intenta en 15 minutos.";
    }
}
if (isset($_POST['login'])) {
    if ($blocked) {
    } elseif (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Token inválido. Intenta de nuevo.";
        $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
        $_SESSION['last_attempt_time'] = time();
    } else {
        $usuario_ingresado = $_POST['usuario'];
        $password_ingresada = $_POST['password'];
        $stmt = $conn->prepare("SELECT password_hash FROM usuarios_admin WHERE usuario = ?");
        $stmt->bind_param("s", $usuario_ingresado);
        $stmt->execute();
        $resultado = $stmt->get_result();
        if ($resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            if (password_verify($password_ingresada, $fila['password_hash'])) {
                $_SESSION['logueado'] = true;
                $_SESSION['login_attempts'] = 0;
                $_SESSION['usuario_admin'] = $usuario_ingresado;
            } else {
                $error = "Contraseña incorrecta.";
                $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
                $_SESSION['last_attempt_time'] = time();
            }
        } else {
            $error = "El usuario no existe.";
            $_SESSION['login_attempts'] = ($_SESSION['login_attempts'] ?? 0) + 1;
            $_SESSION['last_attempt_time'] = time();
        }
        $stmt->close();
    }
}
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}
if (isset($_POST['actualizar_estatus']) && isset($_SESSION['logueado'])) {
    $nuevo_estatus = $_POST['nuevo_estatus'];
    $folio_cotizacion = $_POST['folio_cotizacion']; 

    $stmt_update = $conn->prepare("UPDATE cotizaciones SET estatus = ? WHERE folio = ?");
    $stmt_update->bind_param("ss", $nuevo_estatus, $folio_cotizacion);
    
    if ($stmt_update->execute()) {
        header("Location: admin.php");
        exit;
    } else {
        $error = "Error al actualizar el estatus: " . $conn->error;
    }
    $stmt_update->close();
}
$result = $conn->query("SELECT * FROM cotizaciones ORDER BY fecha DESC");
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
                <a href="?logout=true" style="color: #ef4444; font-weight: bold;">Cerrar Sesión</a>
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
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    <input type="text" name="usuario" placeholder="Ingresa tu usuario" required style="margin-bottom: 10px; width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
    <input type="password" name="password" placeholder="Ingresa tu contraseña" required style="margin-bottom: 15px; width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
    <button type="submit" name="login" class="btn-login">Entrar al Panel</button>
</form>
        </div>
    <?php } else { ?>
        <div class="dashboard-wrapper">
    <h2>Historial de Cotizaciones Recibidas</h2>
    <div class="tabla-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Folio</th>
                    <th>Cliente</th>
                    <th>Teléfono</th>
                    <th>Ubicación</th>
                    <th>Factura</th>
                    <th>Total</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><strong style="color: #ffd700;"><?php echo $row['folio']; ?></strong></td>
                        <td><?php echo htmlspecialchars($row['nombre_cliente']); ?></td>
                        <td><?php echo htmlspecialchars($row['telefono']); ?></td>
                        <td><?php echo htmlspecialchars($row['direccion']); ?></td>
                        <td>
                            <?php echo $row['requiere_factura'] == 1 ? '<span style="color:#25d366;">Sí (+16%)</span>' : 'No (Sin IVA)'; ?>
                        </td>
                        <td><strong>$<?php echo number_format($row['total_estimado'], 2); ?></strong></td>
                        <td>
    <form method="POST" action="" style="display: flex; gap: 5px; align-items: center; margin: 0;">
        <input type="hidden" name="folio_cotizacion" value="<?php echo $row['folio']; ?>">
                            <select name="nuevo_estatus" style="padding: 4px; border-radius: 4px; background: #222; color: #fff; border: 1px solid #ffd700; font-size: 0.8rem;">
                                <option value="Nuevo" <?php if($row['estatus'] == 'Nuevo') echo 'selected'; ?>>Nuevo</option>
                                <option value="Atendido" <?php if($row['estatus'] == 'Atendido') echo 'selected'; ?>>Atendido</option>
                                <option value="Cancelado" <?php if($row['estatus'] == 'Cancelado') echo 'selected'; ?>>Cancelado</option>
                            </select>
                                <button type="submit" name="actualizar_estatus" style="padding: 4px 8px; background: #ffd700; color: #000; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;" title="Guardar estatus">
                                    <i class="fas fa-save"></i>
                                </button>
                            </form>
                        </td>
                        <td>
                            <button class="btn-ver-detalle" 
                                    data-equipos='<?php echo htmlspecialchars($row['equipos_json'], ENT_QUOTES, 'UTF-8'); ?>'
                                    data-subtotal="<?php echo $row['subtotal']; ?>"
                                    data-iva="<?php echo $row['iva']; ?>"
                                    data-total="<?php echo $row['total_estimado']; ?>"
                                    onclick="verDetallesCotizacion(this)">
                                Ver Detalles
                            </button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="panel-lateral" id="panelAdminLateral">
    <div class="panel-cabecera">
        <h3>Detalle de Equipos Solicitados</h3>
        <button onclick="cerrarPanelAdmin()" style="background:none; border:none; font-size:1.5rem; cursor:pointer; color:#000;">&times;</button>
    </div>
    <div class="panel-cuerpo">
        <div id="contenedorEquiposDetalle">
            </div>
        
        <div class="total-estimado">
            <p style="margin: 5px 0; color: #aaa;">Subtotal: <span id="detSubtotal">$0.00</span></p>
            <p style="margin: 5px 0; color: #aaa;">IVA: <span id="detIva">$0.00</span></p>
            <hr style="border-color: #333;">
            <h3>Total: <span id="detTotal">$0.00</span></h3>
        </div>
    </div>
</div>

    <?php } ?>
</main>
<footer>
</footer>
<script src="js/main.js"></script>
</body>
</html>