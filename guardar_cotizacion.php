<?php
require 'config.php';
session_start();
$conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conexion->set_charset("utf8mb4");
if ($conexion->connect_error) { 
    die("Error de conexión: " . $conexion->connect_error); 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $acepta_privacidad = isset($_POST['acepta_privacidad']) ? 1 : 0;
    if (!$acepta_privacidad) {
        die("Error: Debes aceptar el Aviso de Privacidad para continuar.");
    }
    $nombre = trim($_POST['nombre'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $equipos_json = trim($_POST['equipos_json'] ?? '');
    $subtotal = floatval($_POST['subtotal'] ?? 0);
    $requiere_factura = isset($_POST['factura']) ? 1 : 0;
    if (empty($nombre) || empty($telefono) || empty($direccion) || empty($equipos_json)) {
        die("Error: Todos los campos son requeridos.");
    }
    if ($subtotal <= 0) {
        die("Error: El subtotal debe ser mayor a 0.");
    }
    $equipos_array = json_decode($equipos_json, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        die("Error: El formato de equipos no es válido.");
    }
    $iva = 0.00;
    if ($requiere_factura == 1) {
        $iva = $subtotal * 0.16;
    }
    $total_estimado = $subtotal + $iva;
    $folio = "COT-" . date("YmdHis") . "-" . uniqid();
    $sql = "INSERT INTO cotizaciones (folio, nombre_cliente, telefono, direccion, requiere_factura, equipos_json, subtotal, iva, total_estimado)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conexion->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación: " . $conexion->error);
    }
    $stmt->bind_param("ssssissdd", $folio, $nombre, $telefono, $direccion, $requiere_factura, $equipos_json, $subtotal, $iva, $total_estimado);
    if ($stmt->execute()) {
        date_default_timezone_set('America/Mexico_City');
        $id_cotizacion = $conexion->insert_id;
        $ip_cliente = $_SERVER['REMOTE_ADDR'];
        $fecha_hora = date('Y-m-d H:i:s');
        
        $sql_consentimiento = "INSERT INTO registro_consentimiento (id_cotizacion, fecha_hora, ip_cliente, tipo_consentimiento) 
        VALUES (?, ?, ?, ?)";
        
        $stmt_consent = $conexion->prepare($sql_consentimiento);
        if ($stmt_consent) {
            $tipo_consentimiento = "privacidad_y_terminos";
            $stmt_consent->bind_param("isss", $id_cotizacion, $fecha_hora, $ip_cliente, $tipo_consentimiento);
            $stmt_consent->execute();
            $stmt_consent->close();
        }
        header("Location: exito-cotizacion.php?folio=" . urlencode($folio) . "&total=" . urlencode($total_estimado) . "&nombre=" . urlencode($nombre));
        exit;
    } else {
        echo "Error al guardar la cotización. Por favor intenta de nuevo.";
    }
    $stmt->close();
}
$conexion->close();
?>