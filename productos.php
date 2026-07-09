<?php
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "ssmx_db";
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
$sql = "SELECT * FROM inventario"; 
$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SSMX</title>
<head>
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
<style>
</style>
<body>
<header>
    <a href="index.html" class="logo-container">
        <img src="img/logos/logo.png"alt="logo" class="logo-img">
        <span class="logo-text">System Seguridad MX</span>
    </a>
    <a></a>
</header>
<main class="main-cotizador">
    <div class="cotizador-container">
        <h1 class="titulo-cotizador">Arma tu Sistema de Seguridad</h1>
        
        <div class="grid-productos">
            <?php while($producto = $resultado->fetch_assoc()) { ?>
                
                <div class="tarjeta-producto">
                    <div class="img-contenedor">
                        <img src="<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>">
                    </div>
                    
                    <div class="info-contenedor">
                        <div>
                            <h3 class="nombre-prod"><?php echo $producto['nombre']; ?></h3>
                            <li class="desc-prod"><?php echo $producto['descripcion']; ?></li>
                        </div>
                        <div class="caja-precio-boton">
                            <p class="precio-prod">$<?php echo number_format($producto['precio'], 2); ?> MXN</p>
                            <button class="btn-cotizar" data-id="<?php echo $producto['id']; ?>">
                                Agregar al carrito
                            </button>
                        </div>
                    </div>
                </div>

            <?php } ?>
            </div>
    </div>
    <button class="btn-carrito-flotante" onclick="abrirCarrito()">
    <i class="fas fa-shopping-cart"></i>
    <span id="contador-items">0</span>
</button>

<div class="overlay-carrito" id="overlayCarrito" onclick="cerrarCarrito()"></div>
<div class="panel-carrito" id="panelCarrito">
    <div class="carrito-cabecera">
        <h3>Tu Kit de Seguridad</h3>
        <button onclick="cerrarCarrito()"><i class="fas fa-times"></i></button>
    </div>
    <div class="carrito-contenido" id="listaCarrito">
        </div>
        <div class="carrito-formulario" style="padding: 20px; border-top: 1px solid #333;">
        <form action="guardar_cotizacion.php" method="POST" id="formCotizacion">
            <input type="text" name="nombre" placeholder="Tu Nombre" required style="width: 100%; padding: 10px; margin-bottom: 10px; background: #222; color: white; border: 1px solid #444;">
            <input type="tel" name="telefono" placeholder="Numero Telefonico" required style="width: 100%; padding: 10px; margin-bottom: 10px; background: #222; color: white; border: 1px solid #444;">
            <input type="text" name="direccion" placeholder="Estado y Municipio" required style="width: 100%; padding: 10px; margin-bottom: 10px; background: #222; color: white; border: 1px solid #444;">
            <label style="color: #ccc; font-size: 0.9rem; display: block; margin-bottom: 15px;">
            <input type="checkbox" name="factura" value="1"> Requiero Factura (+16% IVA)
            </label>
            <input type="hidden" name="equipos_json" id="input_json">
            <input type="hidden" name="subtotal" id="input_subtotal">
            <button type="button" class="btn-solicitar" onclick="revisarYEnviar()">Confirmar y Enviar</button>
            <p>Total Estimado: <span id="total-precio">$0.00</span></p>
            <p style="color: #666; font-size: 0.75rem; margin-bottom: 15px;">*Los viáticos e instalación final se cotizarán tras evaluar el sitio.</p>
        </form>
    </div>
</div>
</main>
<footer>
        <div class="contact-links">
        <a href="mailto:contactosystemseguridad@gmail.com" class="btn-contacto btn-correo">
        <i class="fas fa-envelope"></i>
        <span>Escribenos un Correo!</span></a>
        <div><a href="https://wa.me/5642756440?text=Hola,%20me%20interesa%20una%20cotización" class="btn-contacto btn-whatsapp"><i class="fab fa-whatsapp"></i><span>Contactanos por WhatsApp!</span></a></div>
        <div><a href="tel:+525592790958" class="btn-contacto btn-telefono"><i class="fas fa-phone"></i><span>Llamenos!</span></a></div>
        <div><a href="https://maps.app.goo.gl/M1gMu6osNP6oCxVE7" target="_blank" class="btn-mapa"><i class="fas fa-map-marked-alt"></i><span>Hidalgo 9, Santiaguito, 54900 Tultitlán, Méx.</span></a></div>
    </div>
    <div class="contenedor-redes-footer">
        <span class="texto-redes">¡Síguenos en nuestras redes sociales!</span>
        <div class=".redes-sociales-footer">
        <a href="https://www.tiktok.com/@ss.mx03" target="_blank" class="icono-red tiktok"><i class="fa-brands fa-tiktok"></i></a>
        <a href="https://www.facebook.com/profile.php?id=61560236490676" target="_blank" class="icono-red facebook"><i class="fa-brands fa-facebook"></i></a>
        <a href="https://youtube.com/@ssmx-2024?si=s9h4MxshQCH4MepR" target="_blank" class="icono-red youtube"><i class="fa-brands fa-youtube"></i></a>
        <a href="https://www.instagram.com/system_seguridadmx/" target="_blank" class="icono-red instagram"><i class="fa-brands fa-instagram"></i></a>
    </div>
    </div>
    &copy; Todos los Derechos Reservados - System Seguridad MX
</footer>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>