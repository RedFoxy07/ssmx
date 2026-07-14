<?php
$conn = new mysqli("localhost", "root", "", "ssmx_db");
$conn->set_charset("utf8mb4");
if ($conn->connect_error) { 
    die("Error de conexión: " . $conn->connect_error); 
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    $folio = "COT-" . date("YmdHis");
    $sql = "INSERT INTO cotizaciones (folio, nombre_cliente, telefono, direccion, requiere_factura, equipos_json, subtotal, iva, total_estimado)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error en la preparación: " . $conn->error);
    }
    $stmt->bind_param("ssssissdd", $folio, $nombre, $telefono, $direccion, $requiere_factura, $equipos_json, $subtotal, $iva, $total_estimado);
    
    if ($stmt->execute()) {
        $numero_empresa = "525642756440";
        $mensaje = "Hola System Seguridad MX, acabo de armar mi kit en su página web.%0A";
        $mensaje .= "*Folio:* " . $folio . "%0A";
        $mensaje .= "*Nombre:* " . $nombre . "%0A";
        $mensaje .= "*Lugar de instalación:* " . $direccion . "%0A";
        $mensaje .= "*Total Estimado:* $" . number_format($total_estimado, 2) . "%0A%0A";
        $mensaje .= "¿Me pueden asesorar para agendar una evaluación?";
        
        $url_whatsapp = "https://wa.me/" . $numero_empresa . "?text=" . $mensaje;
        header("Location: " . $url_whatsapp);
        exit;
    } else {
        echo "Error al guardar la cotización. Por favor intenta de nuevo.";
        // En desarrollo, descomenta esto para ver el error:
        // echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
$conn->close();
?>