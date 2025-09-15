<?php
// Clase encargada de establecer la conexión con la base de datos usando PDO
class Conexion{
	private $conect;// Almacena el objeto de conexión PDO

	public function __construct(){
		// Cadena de conexión con la base de datos (usando constantes definidas en config.php)
		$connectionString = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=".DB_CHARSET;
		try{
			// Intenta crear la conexión PDO
			$this->conect = new PDO($connectionString, DB_USER, DB_PASSWORD);
			// Modo de errores: lanza excepciones
			$this->conect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		    //echo "conexión exitosa";
		}catch(PDOException $e){
			// Si ocurre un error, guarda mensaje
			$this->conect = 'Error de conexión';
		    echo "ERROR: " . $e->getMessage();
		}
	}
	// Devuelve la conexión activa (PDO)
	public function conect(){
		return $this->conect;
	}
}

?>