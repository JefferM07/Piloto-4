<?php
// Configuración de la base de datos (ajusta estos valores según tu configuración)
$host = "localhost";
$user = "root";
$password = "";
$dbname = "inventario";

// Crear la conexión a la base de datos
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Respuesta inicial
$response = array('status' => 'error', 'message' => 'Algo salió mal.');

// Verificar si la solicitud es POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Comprobamos que se reciban los parámetros necesarios
    if (isset($_POST['valorFactura'], $_POST['valorCodigo'], $_POST['valorNombre'], $_POST['valorFechaEntrada'], $_POST['valorCantidadEntrada'], $_POST['valorProveedor'], $_POST['valorUbicacion'])) {
        
        // Recibir los valores del formulario
        $factura = $_POST['valorFactura'];
        $codigo = $_POST['valorCodigo'];
        $nombre = $_POST['valorNombre'];
        $fechaEntrada = $_POST['valorFechaEntrada'];
        $cantidadEntrada = $_POST['valorCantidadEntrada'];
        $fechaSalida = !empty($_POST['valorFechaSalida']) ? $_POST['valorFechaSalida'] : NULL;  // Si no se proporciona, asignar NULL
        $cantidadSalida = !empty($_POST['valorCantidadSalida']) ? $_POST['valorCantidadSalida'] : 0;  // Si no se proporciona, asignar 0
        $proveedor = $_POST['valorProveedor'];
        $ubicacion = $_POST['valorUbicacion'];
        $observaciones = isset($_POST['valorObservaciones']) ? $_POST['valorObservaciones'] : '';  // Observaciones no siempre son requeridas

        // Usar sentencia preparada para insertar los datos en la base de datos
        $stmt = $conn->prepare("INSERT INTO registros (factura, codigo, nombre_producto, fecha_entrada, cantidad_entrada, fecha_salida, cantidad_salida, proveedor, ubicacion, observaciones) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        // Enlazar los parámetros a la sentencia preparada
        $stmt->bind_param("ssssiiisss", $factura, $codigo, $nombre, $fechaEntrada, $cantidadEntrada, $fechaSalida, $cantidadSalida, $proveedor, $ubicacion, $observaciones);
        
        // Ejecutar la sentencia
        if ($stmt->execute()) {
            // Respuesta exitosa
            $response = array('status' => 'success', 'message' => 'Registro exitoso');
        } else {
            // Si ocurre un error en la ejecución de la consulta
            $response = array('status' => 'error', 'message' => 'Error al guardar los datos: ' . $stmt->error);
        }

        // Cerrar la sentencia
        $stmt->close();
    } else {
        // Si falta algún campo requerido
        $response = array('status' => 'error', 'message' => 'Faltan campos obligatorios');
    }
}

// Cerrar la conexión
$conn->close();

// Devolver la respuesta como JSON
echo json_encode($response);
?>
