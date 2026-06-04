<?php
// guardar_estudiante.php - Procesa el formulario y guarda en PostgreSQL

// Incluir la conexión a la base de datos
require_once 'config.php';

// Verificar que los datos vienen por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

// Recibir y limpiar los datos del formulario
$nombre = trim($_POST['nombre'] ?? '');
$apellido = trim($_POST['apellido'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefono = !empty($_POST['telefono']) ? trim($_POST['telefono']) : null;
$fecha_nacimiento = !empty($_POST['fecha_nacimiento']) ? $_POST['fecha_nacimiento'] : null;

// Validaciones
$errores = [];

if (empty($nombre)) {
    $errores[] = "El nombre es obligatorio";
} elseif (strlen($nombre) > 100) {
    $errores[] = "El nombre no puede exceder 100 caracteres";
}

if (empty($apellido)) {
    $errores[] = "El apellido es obligatorio";
} elseif (strlen($apellido) > 100) {
    $errores[] = "El apellido no puede exceder 100 caracteres";
}

if (empty($email)) {
    $errores[] = "El email es obligatorio";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errores[] = "El formato del email no es válido";
} elseif (strlen($email) > 150) {
    $errores[] = "El email no puede exceder 150 caracteres";
}

if ($telefono && strlen($telefono) > 20) {
    $errores[] = "El teléfono no puede exceder 20 caracteres";
}

// Si hay errores, redirigir mostrándolos
if (!empty($errores)) {
    $error_msg = implode(', ', $errores);
    header("Location: index.html?error=" . urlencode($error_msg));
    exit;
}

// Intentar guardar en la base de datos
try {
    // Preparar la consulta SQL (usando prepared statements por seguridad)
    $sql = "INSERT INTO estudiantes (nombre, apellido, email, telefono, fecha_nacimiento) 
            VALUES (:nombre, :apellido, :email, :telefono, :fecha_nacimiento)";
    
    $stmt = $pdo->prepare($sql);
    
    // Ejecutar con los parámetros
    $resultado = $stmt->execute([
        ':nombre' => $nombre,
        ':apellido' => $apellido,
        ':email' => $email,
        ':telefono' => $telefono,
        ':fecha_nacimiento' => $fecha_nacimiento
    ]);
    
    // Verificar si se insertó correctamente
    if ($resultado) {
        $mensaje = "¡Estudiante $nombre $apellido registrado exitosamente!";
        header("Location: index.html?success=" . urlencode($mensaje));
        exit;
    } else {
        throw new PDOException("No se pudo insertar el registro");
    }
    
} catch (PDOException $e) {
    // Verificar si es error de email duplicado
    if (strpos($e->getMessage(), 'duplicate key value violates unique constraint') !== false) {
        $error = "El email '$email' ya está registrado. Por favor, usa otro email.";
    } else {
        $error = "Error al registrar: " . $e->getMessage();
    }
    
    header("Location: index.html?error=" . urlencode($error));
    exit;
}
?>
