<?php
$host = 'localhost';
$dbname = 'biblioteca_escolar';
$user = 'root';
$pass = '';

try {
    $conexion = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    // Habilitar excepciones en caso de error
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Si hay error, mostrar mensaje y detener el script
    die("Error de conexión: " . $e->getMessage());
}
?>
