<?php
// Datos de conexión de InfinityFree
define('SERVIDOR', 'localhost');
define('USUARIO', 'root');
define('PASSWORD', ''); // Asegúrate de que esta sea la contraseña correcta
define('BD', 'liga_bdd');    // Reemplaza XXX con el nombre real de tu base de datos

$servidor = "mysql:dbname=".BD.";host=".SERVIDOR.";port=3306"; // Incluye el puerto

try {
    $pdo = new PDO($servidor, USUARIO, PASSWORD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
     echo "La conexión a la base de datos fue exitosa.";
} catch (PDOException $e) {
    echo "❌ Error al conectar a la base de datos: " . $e->getMessage();
}

// URL base para cuando ya esté publicado en InfinityFree (cámbiala si estás en local)
$URL = "http://localhost/proyectobeto"; // Ajusta a tu subdominio real

// Configuración de zona horaria
date_default_timezone_set("America/Caracas");
$fechaHora = date('Y-m-d H:i:s');
?>
