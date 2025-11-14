<?php 

	class Home extends Controllers{
		public function __construct()
		{
			parent::__construct();
		}

		public function home() {
			$data['page_id'] = 1;
			$data['page_tag'] = "Home";
			$data['page_title'] = "Página principal";
			$data['page_name'] = "home";
			$data['page_content'] = "Lorem ipsum dolor sit amet, ...";
			$this->views->getView($this, "home", $data);
		}
/*
page_id: ID de la página (puede usarse para permisos o identificación).

page_tag: Título que aparece en la pestaña del navegador.

page_title: Encabezado principal en la página.

page_name: Nombre lógico de la vista.

page_content: Texto de ejemplo para mostrar contenido.

*/ 

	}
 ?>