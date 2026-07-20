<?php
require 'config.php';
session_start();
$conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conexion->set_charset("utf8mb4");
if ($conexion->connect_error) { 
    die("Error de conexión: " . $conexion->connect_error); 
}
$folio = isset($_GET['folio']) ? htmlspecialchars($_GET['folio']) : 'N/A';
$total = isset($_GET['total']) ? htmlspecialchars($_GET['total']) : '0.00';
$nombre = isset($_GET['nombre']) ? htmlspecialchars($_GET['nombre']) : 'Cliente';
$numero_empresa = "525642756440";
$mensaje = "Hola System Seguridad MX, acabo de armar mi kit en su página web.%0A";
$mensaje .= "*Folio:* " . $folio . "%0A";
$mensaje .= "*Nombre:* " . $nombre . "%0A";
$mensaje .= "*Total Estimado:* $" . $total . "%0A%0A";
$mensaje .= "¿Me pueden asesorar para agendar una evaluación?";
$url_whatsapp = "https://wa.me/" . $numero_empresa . "?text=" . $mensaje;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Cotización Guardada! - SSMX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css"/>
    <style>
        .contenedor-exito {
            max-width: 600px;
            margin: 60px auto;
            padding: 40px;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            border-radius: 12px;
            border: 2px solid #ffd700;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
        }

        .icono-exito {
            font-size: 64px;
            color: #25d366;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .contenedor-exito h1 {
            color: #ffd700;
            font-size: 32px;
            margin-bottom: 10px;
        }

        .contenedor-exito p {
            color: #ccc;
            font-size: 16px;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .folio-cotizacion {
            background: #111;
            border: 2px dashed #ffd700;
            padding: 20px;
            margin: 30px 0;
            border-radius: 8px;
        }

        .folio-cotizacion strong {
            display: block;
            color: #ffd700;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .folio-numero {
            color: #fff;
            font-size: 24px;
            font-weight: bold;
            font-family: monospace;
            word-break: break-all;
        }

        .total-cotizacion {
            color: #25d366;
            font-size: 20px;
            font-weight: bold;
            margin-top: 15px;
        }

        .botones-accion {
            display: flex;
            gap: 15px;
            margin: 40px 0;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-principal {
            flex: 1;
            min-width: 200px;
            padding: 15px 30px;
            background: #25d366;
            color: #000;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: transform 0.3s ease;
        }

        .btn-principal:hover {
            transform: scale(1.05);
            background: #20ba58;
        }

        .btn-principal i {
            font-size: 20px;
        }

        .btn-secundario {
            flex: 1;
            min-width: 200px;
            padding: 15px 30px;
            background: #ffd700;
            color: #000;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: transform 0.3s ease;
        }

        .btn-secundario:hover {
            transform: scale(1.05);
            background: #ffed4e;
        }

        .btn-secundario i {
            font-size: 20px;
        }

        .mensaje-info {
            background: #222;
            border-left: 4px solid #ffd700;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            color: #aaa;
            font-size: 14px;
            text-align: left;
        }

        .pasos {
            background: #111;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: left;
        }

        .paso {
            color: #ccc;
            margin-bottom: 15px;
            padding-left: 30px;
            position: relative;
        }

        .paso::before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #25d366;
            font-weight: bold;
            font-size: 18px;
        }

        .copiar-folio {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #ffd700;
            color: #000;
            padding: 8px 12px;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            margin-top: 10px;
            border: none;
            font-size: 14px;
        }

        .copiar-folio:hover {
            background: #ffed4e;
        }

        .enlace-retorno {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #333;
        }

        .enlace-retorno a {
            color: #ffd700;
            text-decoration: none;
            transition: color 0.3s;
        }

        .enlace-retorno a:hover {
            color: #ffed4e;
        }
    </style>
</head>
<body>
<header>
    <div class="logo-container" style="display: flex; align-items: center;">
        <a href="index.php" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
            <img src="img/logos/logo.png" alt="logo" class="logo-img">
            <span class="logo-text">System Seguridad MX</span>
        </a>
    </div>
</header>

<main>
    <div class="contenedor-exito">
        <div class="icono-exito">
            <i class="fas fa-check-circle"></i>
        </div>

        <h1>¡Cotización Guardada!</h1>
        <p>Tu solicitud ha sido procesada exitosamente. Guarda tu folio para referencia futura.</p>

        <div class="folio-cotizacion">
            <strong>Tu Folio de Cotización:</strong>
            <div class="folio-numero"><?php echo $folio; ?></div>
            <button class="copiar-folio" onclick="copiarAlPortapapeles('<?php echo $folio; ?>')">
                <i class="fas fa-copy"></i> Copiar Folio
            </button>
        </div>

        <div class="total-cotizacion">
            Total Estimado: $<?php echo number_format(floatval($total), 2); ?> MXN
        </div>

        <div class="mensaje-info">
            <strong>ℹ️ Información importante:</strong><br>
            Este es un estimado. Los costos finales (viáticos, instalación, adaptaciones) 
            serán confirmados después de la evaluación en sitio.
        </div>

        <div class="pasos">
            <strong style="color: #ffd700; display: block; margin-bottom: 15px;">Próximos pasos:</strong>
            <div class="paso">Nos contactaremos pronto a tu teléfono para agendar evaluación</div>
            <div class="paso">Un técnico visitará tu ubicación para evaluar los requerimientos</div>
            <div class="paso">Enviaremos un presupuesto final con costos exactos</div>
        </div>

        <div class="botones-accion">
            <a href="<?php echo $url_whatsapp; ?>" class="btn-principal" target="_blank">
                <i class="fab fa-whatsapp"></i> Enviar a WhatsApp
            </a>
            <a href="productos.php" class="btn-secundario">
                <i class="fas fa-plus"></i> Otra Cotización
            </a>
        </div>

        <div class="enlace-retorno">
            <a href="index.php"><i class="fas fa-home"></i> Volver al inicio</a>
        </div>
    </div>
</main>

<footer>
    <div class="contact-links">
        <a href="mailto:contactosystemseguridad@gmail.com" class="btn-contacto btn-correo">
            <i class="fas fa-envelope"></i>
            <span>Escribenos un Correo!</span>
        </a>
        <div><a href="https://wa.me/5642756440?text=Hola,%20me%20interesa%20una%20cotización" class="btn-contacto btn-whatsapp">
            <i class="fab fa-whatsapp"></i><span>Contactanos por WhatsApp!</span>
        </a></div>
        <div><a href="tel:+525592790958" class="btn-contacto btn-telefono">
            <i class="fas fa-phone"></i><span>Llamenos!</span>
        </a></div>
    </div>
</footer>

<script>
function copiarAlPortapapeles(texto) {
    navigator.clipboard.writeText(texto).then(() => {
        alert('Folio copiado al portapapeles: ' + texto);
    }).catch(err => {
        console.error('Error al copiar:', err);
    });
}
</script>

</body>
</html>