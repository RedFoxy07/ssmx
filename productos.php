<?php
$servidor = "localhost";
$usuario = "root";
$password = "";
$base_datos = "productos";
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
                            <p class="desc-prod"><?php echo $producto['descripcion']; ?></p>
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