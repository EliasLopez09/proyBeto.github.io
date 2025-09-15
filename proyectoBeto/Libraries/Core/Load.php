<?php 
	// Este archivo es el enrutador principal del sistema.
    // Se encarga de cargar el controlador y ejecutar el método correspondiente con parámetros.

    // Convierte el nombre del controlador en formato capitalizado (ej. login → Login)
	$controller = ucwords($controller);
	// Construye la ruta del archivo del controlador
	$controllerFile = "Controllers/".$controller.".php";
	// Verifica si el archivo del controlador existe
	if(file_exists($controllerFile))
	{
		// Incluye el archivo del controlador
		require_once($controllerFile);
		// Crea una instancia de la clase controlador
		$controller = new $controller();
		// Verifica si el método solicitado existe dentro del controlador
		if(method_exists($controller, $method))
		{
			// Ejecuta el método pasándole los parámetros (si existen)
			$controller->{$method}($params);
		}else{
			// Si el controlador no existe, muestra la vista de error
			require_once("Controllers/Error.php");
		}
	}else{
		// Si el método no existe, muestra la vista de error
		require_once("Controllers/Error.php");
	}

 ?>