<?php 

	class Dashboard extends Controllers{
		
		public function __construct() {
			parent::__construct();               // Llama al constructor del padre (Controllers)
			session_start();                     // Inicia sesión
			session_regenerate_id(true);        // Evita secuestro de sesión (más seguro)
			// if(empty($_SESSION['login'])) {     // Si no hay sesión activa
			// 	header('Location: '.base_url().'/login'); // Redirecciona al login
			// }
			//getPermisos(1);                     // Valida permisos (probablemente ID de módulo 1)
		}

		public function dashboard() 
		{
			$data['page_id'] = 2;                         // ID de página (para controlar vistas o permisos)
			$data['page_tag'] = "Dashboard - Tienda Virtual"; // Etiqueta para SEO o pestaña del navegador
			$data['page_title'] = "Dashboard - Tienda Virtual"; // Título mostrado
			$data['page_name'] = "dashboard";             // Nombre de la vista (dashboard.php)
			$data['page_functions_js'] = "functions_dashboard.js"; // JS específico de esta vista
			$this->views->getView($this, "dashboard", $data); // Carga la vista: /Views/Dashboard/dashboard.php
		}

	}
 ?>