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
if (isset($_POST['actualizar_estatus_venta']) && isset($_SESSION['logueado'])) {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Token inválido. No se puede actualizar el estatus.";
    } else {
        $estatus_permitidos_venta = ['Pendiente', 'Pagado', 'Atrasado'];
        $nuevo_estatus_venta = $_POST['nuevo_estatus_venta'];
        $id_venta = intval($_POST['id_venta']);
        
        if (!in_array($nuevo_estatus_venta, $estatus_permitidos_venta)) {
            $error = "Estatus de venta inválido.";
        } else {
            $stmt_update_v = $conn->prepare("UPDATE ventas_externas SET estatus_pago = ? WHERE id_venta = ?");
            $stmt_update_v->bind_param("si", $nuevo_estatus_venta, $id_venta);
            
            if ($stmt_update_v->execute()) {
                header("Location: " . basename(__FILE__));
                exit;
            } else {
                $error = "Error al actualizar el estatus de venta: " . $conn->error;
            }
            $stmt_update_v->close();
        }
    }
}
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: " . basename(__FILE__));
    exit;
}
if (isset($_POST['actualizar_estatus']) && isset($_SESSION['logueado'])) {
    if (empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "Token inválido. No se puede actualizar el estatus.";
    } else {
        $estatus_permitidos = ['Nuevo', 'Atendido', 'Cancelado'];
        $nuevo_estatus = $_POST['nuevo_estatus'];
        $folio_cotizacion = $_POST['folio_cotizacion'];
        
        if (!in_array($nuevo_estatus, $estatus_permitidos)) {
            $error = "Estatus inválido.";
        } else {
            $stmt_update = $conn->prepare("UPDATE cotizaciones SET estatus = ? WHERE folio = ?");
            $stmt_update->bind_param("ss", $nuevo_estatus, $folio_cotizacion);
            
            if ($stmt_update->execute()) {
                header("Location: " . basename(__FILE__));
                exit;
            } else {
                $error = "Error al actualizar el estatus: " . $conn->error;
            }
            $stmt_update->close();
        }
    }
}
$result = $conn->query("SELECT * FROM cotizaciones ORDER BY fecha DESC");
$result_ventas = $conn->query("SELECT * FROM ventas_externas ORDER BY fecha_registro DESC");
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
    <link rel="apple-touch-icon" sizes="180x180" href="../img/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="../img/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../img/favicon/favicon-16x16.png">
    <link rel="manifest" href="../img/favicon/site.webmanifest">
    <link rel="shortcut icon" href="../img/favicon/favicon.ico">
</head>
<body>
<header>
    <div class="logo-container" style="display: flex; align-items: center;">
        <a href="../index.html" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
            <img src="../img/logos/logo.png" alt="logo" class="logo-img">
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
                    <th>De donde nos conoce?</th>
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
                        <td><?php echo htmlspecialchars($row['medio_contacto']); ?></td>
                        <td>
                            <?php echo $row['requiere_factura'] == 1 ? '<span style="color:#25d366;">Sí (+16%)</span>' : 'No (Sin IVA)'; ?>
                        </td>
                        <td><strong>$<?php echo number_format($row['total_estimado'], 2); ?></strong></td>
                        <td>
                            <form method="POST" action="" style="display: flex; gap: 5px; align-items: center; margin: 0;">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
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
<div class="divider"></div>  
    <?php
    if (isset($_GET['mensaje'])) {
        if ($_GET['mensaje'] == 'exito') {
            echo '<div style="background-color: rgba(37, 211, 102, 0.2); border: 1px solid #25d366; color: #25d366; padding: 12px; text-align: center; border-radius: 6px; margin: 15px auto; width: 90%; max-width: 600px; font-weight: bold;">
                    <i class="fas fa-check-circle"></i> ¡Venta registrada y expediente creado con éxito!
                  </div>';
        } elseif ($_GET['mensaje'] == 'error') {
            echo '<div style="background-color: rgba(239, 68, 68, 0.2); border: 1px solid #ef4444; color: #ef4444; padding: 12px; text-align: center; border-radius: 6px; margin: 15px auto; width: 90%; max-width: 600px; font-weight: bold;">
                    <i class="fas fa-exclamation-triangle"></i> Hubo un error al guardar la venta en la base de datos.
                  </div>';
        }
    }
    ?>
    <h1 style="color: #ffd700; text-align: center; margin-top: 10px;">Registrar Nueva Venta</h1>
    <form action="procesar_venta.php" method="POST" class="mb-4">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <div class= "venta-wrapper">
            <div class="columna-venta">
                <label>Nombre del Cliente</label>
                <input type="text" name="nombre_cliente" class="form-control" required>
            </div>
            <div class="columna-venta">
                <label>Concepto de Venta</label>
                <input type="text" name="concepto_venta" class="form-control" required>
            </div>
            <div class="columna-venta">
                <label> Monto ($)</label>
                <input type="number" step="0.01" name="monto_venta" class="form-control required">
            </div>
            <div class="columna-venta">
            <label for="fecha_venta">Fecha de Venta </label>
            <input type="date" id="fecha_venta" name="fecha_venta" class="form-control" required>
            <div class="columna-venta">
                <label>Estatus</label>
                <select name="estatus_pago" class="form-control">
                    <option value="Pendiente">Pendiente</option>
                    <option value="Pagado">Pagado</option>
                </select>
            </div> 
            <div class="boton-venta">
            <button type="submit" name="guardar_venta" class="btn btn-guardar">Guardar Venta y Crear Expediente</button>
        </div>
        </div>
                </div>
    </form>
    <div class="divider"></div>
<div class="dashboard-wrapper">
    <h2 style="color: #ffd700;">Historial de Ventas Externas</h2>
    <div class="tabla-responsive">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Concepto</th>
                    <th>Fecha</th>
                    <th>Monto</th>
                    <th>Estatus</th>
                    <th>Expediente</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result_ventas && $result_ventas->num_rows > 0): ?>
                    <?php while($venta = $result_ventas->fetch_assoc()): ?>
                        <tr>
                            <td><strong style="color: #ffd700;"><?php echo $venta['id_venta']; ?></strong></td>
                            <td><?php echo htmlspecialchars($venta['nombre_cliente']); ?></td>
                            <td><?php echo htmlspecialchars($venta['concepto_venta']); ?></td>
                            <td><?php echo $venta['fecha_venta']; ?></td>
                            <td><strong>$<?php echo number_format($venta['monto_venta'], 2); ?></strong></td>
                            <td>
                                <form method="POST" action="" style="display: flex; gap: 5px; align-items: center; margin: 0;">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="id_venta" value="<?php echo $venta['id_venta']; ?>">
                                    
                                    <select name="nuevo_estatus_venta" style="padding: 4px; border-radius: 4px; background: #222; color: #fff; border: 1px solid #ffd700; font-size: 0.8rem;">
                                        <option value="Pendiente" <?php if($venta['estatus_pago'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                        <option value="Pagado" <?php if($venta['estatus_pago'] == 'Pagado') echo 'selected'; ?>>Pagado</option>
                                        <option value="Atrasado" <?php if($venta['estatus_pago'] == 'Atrasado') echo 'selected'; ?>>Atrasado</option>
                                    </select>
                                    
                                    <button type="submit" name="actualizar_estatus_venta" style="padding: 4px 8px; background: #ffd700; color: #000; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;" title="Guardar estatus">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </form>
                            </td>
                            <td>
                                <?php if (!empty($venta['enlace_nube'])): ?>
                                    <a href="<?php echo htmlspecialchars($venta['enlace_nube']); ?>" target="_blank" style="display: inline-block; padding: 6px 12px; background: #25d366; color: #fff; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 0.85rem;">
                                        <i class="fas fa-folder-open"></i> Abrir
                                    </a>
                                <?php else: ?>
                                    <span style="color: #aaa; font-size: 0.8rem;">Sin enlace</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #aaa; padding: 20px;">No hay ventas registradas aún.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
    <?php } ?>
</main>
<footer>
</footer>
<script src="../js/main.js"></script>
</body>
</html>