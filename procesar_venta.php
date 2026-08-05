<?php
error_reporting(0);
ini_set('display_errors', 0);
require 'config.php';
require 'vendor/autoload.php';
session_start();
if (!isset($_SESSION['logueado'])) {
    error_log("Intento de acceso no autorizado a procesar_venta.php");
    header("Location: administracion_ssmx.php?mensaje=no_autorizado");
    exit();
}
$conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conexion->set_charset("utf8mb4");
if ($conexion->connect_error) { 
    error_log("Error de conexión a BD: " . $conexion->connect_error);
    header("Location: administracion_ssmx.php?mensaje=error_conexion");
    exit();
}
if (isset($_POST['guardar_venta'])) {
    $token_recibido = isset($_POST['csrf_token']) ? $_POST['csrf_token'] : '';
    $token_esperado = isset($_SESSION['csrf_token']) ? $_SESSION['csrf_token'] : '';
    if (empty($token_recibido) || $token_recibido !== $token_esperado) {
        error_log("Token CSRF inválido para la IP: " . $_SERVER['REMOTE_ADDR']);
        header("Location: administracion_ssmx.php?mensaje=error_csrf");
        exit();
    }
    $nombre_cliente = isset($_POST['nombre_cliente']) ? trim($_POST['nombre_cliente']) : '';
    if (strlen($nombre_cliente) < 2 || strlen($nombre_cliente) > 100) {
        error_log("Nombre de cliente inválido: longitud fuera de rango");
        header("Location: administracion_ssmx.php?mensaje=nombre_invalido");
        exit();
    }
    if (strlen($nombre_cliente) > 0 && preg_match('/<|>|"|\'|;|`/', $nombre_cliente)) {
        error_log("Nombre de cliente contiene caracteres peligrosos: " . $nombre_cliente);
        header("Location: administracion_ssmx.php?mensaje=nombre_invalido");
        exit();
    }
    $concepto_venta = isset($_POST['concepto_venta']) ? trim($_POST['concepto_venta']) : '';
    if (strlen($concepto_venta) < 3 || strlen($concepto_venta) > 500) {
        error_log("Concepto de venta inválido: longitud fuera de rango");
        header("Location: administracion_ssmx.php?mensaje=concepto_invalido");
        exit();
    }
    if (strlen($concepto_venta) > 0 && preg_match('/<|>|"|\'|;|`/', $concepto_venta)) {
        error_log("Concepto de venta contiene caracteres peligrosos: " . $concepto_venta);
        header("Location: administracion_ssmx.php?mensaje=concepto_invalido");
        exit();
    }
    if (!isset($_POST['monto_venta']) || $_POST['monto_venta'] === '') {
        error_log("Monto de venta vacío");
        header("Location: administracion_ssmx.php?mensaje=monto_vacio");
        exit();
    }
    $monto_venta = floatval($_POST['monto_venta']);
    if ($monto_venta <= 0 || $monto_venta >= 999999999 || !is_finite($monto_venta)) {
        error_log("Monto inválido: " . $monto_venta);
        header("Location: administracion_ssmx.php?mensaje=monto_invalido");
        exit();
    }
    if (!isset($_POST['fecha_venta']) || $_POST['fecha_venta'] === '') {
        error_log("Fecha de venta vacía");
        header("Location: administracion_ssmx.php?mensaje=fecha_vacia");
        exit();
    }
    $fecha_venta = $_POST['fecha_venta'];
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_venta)) {
        error_log("Formato de fecha inválido: " . $fecha_venta);
        header("Location: administracion_ssmx.php?mensaje=fecha_invalida");
        exit();
    }
    $fecha_obj = DateTime::createFromFormat('Y-m-d', $fecha_venta);
    if (!$fecha_obj || $fecha_obj->format('Y-m-d') !== $fecha_venta) {
        error_log("Fecha no válida: " . $fecha_venta);
        header("Location: administracion_ssmx.php?mensaje=fecha_invalida");
        exit();
    }
    if ($fecha_obj->getTimestamp() > time()) {
        error_log("Fecha futura rechazada: " . $fecha_venta);
        header("Location: administracion_ssmx.php?mensaje=fecha_futura");
        exit();
    }
    $fecha_para_bd = $fecha_venta; 
    $fecha_para_bd = $fecha_obj->format('Y-m-d');
    $estatus_permitidos = ['Pendiente', 'Pagado'];
    $estatus_pago = $_POST['estatus_pago'] ?? '';
    
    if (!in_array($estatus_pago, $estatus_permitidos, true)) {
        error_log("Estatus inválido: " . $estatus_pago);
        header("Location: administracion_ssmx.php?mensaje=error");
        exit();
    }
    $nombre_carpeta_expediente = "Expediente - " . $nombre_cliente . " - " . $fecha_para_bd;
    $ruta_credenciales = __DIR__ . '/credenciales-bot.json';
    $id_carpeta_padre = '';
    $enlace_nube = "";
        try{
            $client = new \Google_Client();
            $client->setAuthConfig($ruta_credenciales);
            $client->addScope(\Google_Service_Drive::DRIVE);
            $service = new \Google_Service_Drive($client);

            $fileMetadata = new \Google_Service_Drive_DriveFile(array(
                'name' => $nombre_carpeta_expediente,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents' => array($id_carpeta_padre)
            ));
            $folder = $service->files->create($fileMetadata, array('fields' => 'id, webViewLink'));
            $enlace_nube = $folder->webViewLink;
        } catch (Exception $e) {
            error_log("Error al crear carpeta en Google Drive: " . $e->getMessage());
            header("Location: administracion_ssmx.php?mensaje=error_drive");
            exit();
        }
    $sql_venta = "INSERT INTO ventas_externas (nombre_cliente, concepto_venta, monto_venta, estatus_pago, enlace_nube, fecha_venta) 
                  VALUES (?, ?, ?, ?, ?, ?)";              
    $stmt_venta = $conexion->prepare($sql_venta);
    if (!$stmt_venta) {
        error_log("Error en prepare(): " . $conexion->error);
        header("Location: administracion_ssmx.php?mensaje=error_conexion");
        exit();
    }
    if (!$stmt_venta->bind_param("ssdsss", $nombre_cliente, $concepto_venta, $monto_venta, $estatus_pago, $enlace_nube, $fecha_para_bd)) {
        error_log("Error en bind_param(): " . $stmt_venta->error);
        header("Location: administracion_ssmx.php?mensaje=error_conexion");
        exit();
    }
    if ($stmt_venta->execute()) {
        error_log("Venta registrada exitosamente por usuario: " . $_SESSION['usuario'] . " - Fecha original: " . $fecha_venta);
        $stmt_venta->close();
        $conexion->close();
        header("Location: administracion_ssmx.php?mensaje=exito");
        exit();
    } else {
        error_log("Error al ejecutar INSERT: " . $stmt_venta->error);
        header("Location: administracion_ssmx.php?mensaje=error");
        exit();
    }
}
$conexion->close();
?>