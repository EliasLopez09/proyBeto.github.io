<?php 
	// ======================================
// Clase base que heredan todos los controladores del sistema
	class Controllers
	{
		// Constructor: se ejecuta automáticamente al instanciar un controlador
		public function __construct()
		{
			// Carga la clase de vistas (Views.php)
			$this->views = new Views();
			// Carga el modelo correspondiente al controlador actual
			$this->loadModel();
		}
		// Carga automáticamente el modelo que corresponde al controlador
		public function loadModel()
		{
			// Obtiene el nombre del modelo basado en el nombre del controlador actual
        	// Ejemplo: si el controlador es Usuarios, busca UsuariosModel.php
			$model = get_class($this)."Model";
			$routClass = "Models/".$model.".php";
			// Si el archivo del modelo existe, lo incluye y crea una instancia
			if(file_exists($routClass)){
				require_once($routClass);
				$this->model = new $model();
			}
		}
	}

 ?>