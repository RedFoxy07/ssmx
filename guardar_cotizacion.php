<?php
$conn = new mysqli("localhost", "root", "", "ssmx_db");
$conn->set_charset("utf8mb4");
if ($conn->connect_error) { die("Error de conexión: " . $conn->connect_error); }
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $telefono = $_POST['telefono'];
    $direccion = $_POST['direccion'];
    $equipos_json = $_POST['equipos_json'];
    $subtotal = floatval($_POST['subtotal']);
    $requiere_factura = isset($_POST['factura']) ? 1 : 0;
    $iva = 0.00;
    if ($requiere_factura == 1) {
        $iva = $subtotal * 0.16;
    }
    $total_estimado = $subtotal + $iva;
    $folio = "COT-" . date("YmdHis"); 
    $sql = "INSERT INTO cotizaciones (folio, nombre_cliente, telefono, direccion, requiere_factura, equipos_json, subtotal, iva, total_estimado) 
            VALUES ('$folio', '$nombre', '$telefono', '$direccion', $requiere_factura, '$equipos_json', $subtotal, $iva, $total_estimado)";
    if ($conn->query($sql) === TRUE) {
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
        echo "Error al guardar: " . $conn->error;
    }
}
$conn->close();
?>