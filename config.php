<?php
// config.php - Configuración para PostgreSQL en DBeaver

// Configuración de la base de datos (AJUSTA ESTOS VALORES)
$host = 'localhost';        // Si usas DBeaver local, normalmente es localhost
$port = '5432';             // Puerto por defecto de PostgreSQL
$dbname = 'escuela';        // Nombre de tu base de datos
$user = 'postgres';         // Tu usuario de PostgreSQL
$password = 'tu_contraseña'; // Tu contraseña de PostgreSQL

try {
    // Crear conexión PDO
    $pdo = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $user,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,  // Lanza excepciones en errores
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Resultados como array asociativo
            PDO::ATTR_EMULATE_PREPARES => false          // Usa prepared statements reales
        ]
    );
    
    // Establecer el esquema por defecto
    $pdo->exec("SET search_path TO public");
    
    // Opcional: Para verificar la conexión
    // echo "Conexión exitosa a PostgreSQL";
    
} catch (PDOException $e) {
    // Si hay error, mostrar mensaje y detener ejecución
    die("❌ Error de conexión a PostgreSQL: " . $e->getMessage());
}
?>
