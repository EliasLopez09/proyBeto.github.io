<?php 
	// Clase que hereda de Conexion.php y proporciona métodos para ejecutar consultas SQL
	class Mysql extends Conexion
	{
		private $conexion;     // Objeto de conexión PDO
		private $strquery;     // Consulta SQL
		private $arrValues;    // Valores para consultas preparadas
		// Constructor: al crear una instancia de Mysql, se conecta automáticamente a la base de datos
		function __construct()
		{
			$this->conexion = new Conexion();              // Crea instancia de conexión
       		 $this->conexion = $this->conexion->conect();   // Obtiene el objeto PDO listo para usar
		}

		//Insertar un registro
		public function insert(string $query, array $arrValues)
		{
			$this->strquery = $query;
			$this->arrVAlues = $arrValues;
			// Prepara y ejecuta la consulta
        	$insert = $this->conexion->prepare($this->strquery);
        	$resInsert = $insert->execute($this->arrVAlues);
        	// Si se ejecuta correctamente, devuelve el último ID insertado
			if($resInsert)
	        {
	        	$lastInsert = $this->conexion->lastInsertId();
	        }else{
	        	$lastInsert = 0;
	        }
	        return $lastInsert; 
		}
		// Obtener un solo registro
		public function select(string $query)
		{
			$this->strquery = $query;
        	// Ejecuta la consulta
			$result = $this->conexion->prepare($this->strquery);
			$result->execute();
        	// Devuelve una sola fila como array asociativo
			$data = $result->fetch(PDO::FETCH_ASSOC);
        	return $data;
		}
		// Obtener todos los registros
		public function select_all(string $query)
		{
			$this->strquery = $query;
        	$result = $this->conexion->prepare($this->strquery);
			$result->execute();
        	// Devuelve todos los registros como array de arrays asociativos
			$data = $result->fetchall(PDO::FETCH_ASSOC);
        	return $data;
		}
		//Actualiza registros
		public function update(string $query, array $arrValues)
		{
			$this->strquery = $query;
			$this->arrVAlues = $arrValues;
			$update = $this->conexion->prepare($this->strquery);
			$resExecute = $update->execute($this->arrVAlues);
	        return $resExecute;// true o false
		}
		//Eliminar un registros
		public function delete(string $query)
		{
			$this->strquery = $query;
        	$result = $this->conexion->prepare($this->strquery);
			$del = $result->execute();
        	return $del; // true o false
		}
	}


 ?>

