<?php                       
// Controlador de Jugadores: gestiona creación, edición y eliminación
class Jugadores extends Controllers{
	public function __construct()
	{
		parent::__construct();
		session_start();
		// if(empty($_SESSION['login']))
		// {
		// 	header('Location: '.base_url().'/login');
		// }
	}

	// Carga la vista principal de jugadores
	public function jugadores()
	{
		$data['page_tag'] = "Jugadoress";
		$data['page_title'] = "JUGADORES <small>Liga de Fútbol</small>";
		$data['page_name'] = "jugadores";
		$data['page_functions_js'] = "functions_jugadores.js";
		$this->views->getView($this,"jugadores",$data);
	}

	public function jugadoresLibres()
	{
		$data['page_tag'] = "Jugadores Libres";
		$data['page_title'] = "Jugadores Libres";
		$data['page_name'] = "jugadoresLibres";
		$data['page_functions_js'] = "functions_jugadores.js"; // mismo JS
		$this->views->getView($this, "jugadoresLibres", $data);
	}
	public function jugadoresPRB()
	{
		$data['page_tag'] = "Jugadores PRB";
		$data['page_title'] = "Jugadores PRB <small>Prueba</small>";
		$data['page_name'] = "jugadoresPRB";
		$data['page_functions_js'] = "functions_jugadores.js";
		$this->views->getView($this,"jugadoresPRB",$data);
	}


	
	// Devuelve todos los jugadores (para DataTables)
	public function getJugadores()
	{
			$arrData = $this->model->selectJugadores();
			for ($i=0; $i < count($arrData); $i++) {
				$btnEdit = '<button class="btn btn-warning btn-sm" onClick="fntEditJugador(' . $arrData[$i]['id'] . ')">Editar</button>';
				$btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelJugador('.$arrData[$i]['id'].')">Desactivar</button>';
				$btnRestore = '<button class="btn btn-success btn-sm" onClick="fntRestoreJugador('.$arrData[$i]['id'].')">Activar</button>';
				//$btnVer = '<button class="btn btn-info btn-sm" onClick="fntVerClub(' . $arrData[$i]['id'] . ')">Ver</button>';

				// Botón para historial
       			 $btnHistorial = '<button class="btn btn-info btn-sm" onClick="cargarHistorial('.$arrData[$i]['id'].', this)">Ver historial</button>';

				if ($arrData[$i]['status'] == 1) {
					$arrData[$i]['status'] = '<span class="badge bg-success">Activo</span>';
					$arrData[$i]['options'] = $btnEdit . ' ' . $btnDelete;
				} else {
					$arrData[$i]['status'] = '<span class="badge bg-danger">Inactivo</span>';
					$arrData[$i]['options'] = $btnRestore;
				}
				// Nueva columna con botón
        		$arrData[$i]['historial'] = $btnHistorial;
			}
		//  Aquí estás construyendo la respuesta para DataTables
			$response = array(
			"draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 0, // Aquí va
			"recordsTotal" => count($arrData),
			"recordsFiltered" => count($arrData),
			"data" => $arrData
			/*echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
   			 die();*/
		);

		// Envías la respuesta JSON al cliente
		echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
		die();
	}

	// Devuelve un jugador específico por ID
	public function getJugador($id)
	{
		$id = intval($id);
		if($id > 0)
		{
			$data = $this->model->selectJugador($id);
			if (empty($data)) {
                echo json_encode(["status" => false, "msg" => "Jugador no encontrado"], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(["status" => true, "data" => $data], JSON_UNESCAPED_UNICODE);
            }
		}
		die();
	}
	// Inserta o actualiza un jugador
	public function setJugador(){
				
			
			$idJugador = intval($_POST['idJugador']);
			$strCodigo = ucwords(strClean($_POST['txtCodigo']));
			$intClub = intval($_POST['listClub']);
			$strNombre = ucwords(strClean($_POST['txtNombre']));
			$strApellido = ucwords(strClean($_POST['txtApellido']));
			$strCarnet = strClean($_POST['txtCarnet']);
			$strFechaNacimiento = strClean($_POST['txtFechaNacimiento']);
			//$intStatus = intval($_POST['listStatus']); //siempre activo al crear 
			// $strFechaPartido = isset($_POST['txtFechaPartido']) ? strClean($_POST['txtHistorial']) : "";
			$strFechaPartido = strClean($_POST['txtFechaPartido']);

			  
			if($idJugador == 0){
                // Generar código incremental tipo JG0001
                $strCodigo = $this->model->generateCodigoJugador();

                // Insertar nuevo jugador
                $request_jugador = $this->model->insertJugador(
					$intClub,
					$strCodigo,      // 👈 Aquí se manda el código generado
					$strNombre,
					$strApellido,
					$strCarnet,
					//$intStatus,
					$strFechaNacimiento,
					$strFechaPartido

				); 
                	$option = 1;
				}else{
					// Actualizar jugador existente
					$request_jugador = $this->model->updateJugador(
						$idJugador,
						$intClub,
						$strNombre,
						$strApellido,
						$strCarnet,
						//$intStatus,
						$strFechaNacimiento
                	);
                	$option = 2;
				}

				if ($request_jugador === "exist") {
					// Obtener el jugador que tiene el mismo carnet
					$exist = $this->model->selectJugadorByCarnet($strCarnet);

					$arrResponse = array(
						"status" => false,
						"msg" => "El carnet ya está registrado.",
						"exist_carnet" => true,  // bandera para JS
						"data" => array(
							"id" => $exist['id'],
							"nombre" => $exist['nombre'],
							"apellido" => $exist['apellido'],
							"carnet" => $exist['carnet'],
							"club_id" => $exist['club_id']
						)
					);
				} elseif ($request_jugador > 0) {
					$msg = ($option == 1) ? 'Jugador creado correctamente.' : 'Jugador actualizado correctamente.';
					$arrResponse = array('status' => true, 'msg' => $msg);
				} else {
					$arrResponse = array('status' => false, 'msg' => 'No se pudo guardar el jugador.');
				}
			
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        	die();	
		
	}

	// Elimina un jugador (cambia estado a inactivo)
	public function delJugador()
	{	
		$id = intval($_POST['idJugador']);
		$request = $this->model->deleteJugador($id);

		if($request){
			if($request){
				echo json_encode(['status' => true, 'msg' => 'Jugador desactivado correctamente.'], JSON_UNESCAPED_UNICODE);
			}else{
				echo json_encode(['status' => false, 'msg' => 'Error al desactivar el club.'], JSON_UNESCAPED_UNICODE);
			}
		}
		die();
	}

	// Reactiva un club (cambia status a 1)
    public function restoreJugador()
    {
        $id = intval($_POST['idJugador']);
        $request = $this->model->restoreJugador($id); // llama al modelo
        if ($request) {
            echo json_encode(['status' => true, 'msg' => 'Jugador activado correctamente.'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['status' => false, 'msg' => 'Error al activar el Jugador.'], JSON_UNESCAPED_UNICODE);
        }
        die();
    }

	public function getSelectClubes()
	{
		$htmlOptions = "";
		$arrData = $this->model->selectClubes();
		if(count($arrData) > 0){
			for ($i=0; $i < count($arrData); $i++) {
				if($arrData[$i]['status'] == 1){
					$htmlOptions .= '<option value="'.$arrData[$i]['id'].'">'.$arrData[$i]['nombre'].'</option>';
				}
			}
		}
		echo $htmlOptions;
		die();
	}
	
	public function getHistorialPorJugador($jugador_id) 
	{
		// 1. Validar el ID del jugador
		if (!is_numeric($jugador_id) || $jugador_id <= 0) {
			header('Content-Type: application/json');
			echo json_encode(['status' => false, 'msg' => 'ID de jugador inválido.']);
			exit;
		}

		// 2. Usar el modelo ya cargado en la propiedad $this->model
		$historial = $this->model->selectHistorialPorJugador($jugador_id);

		// 3. Devolver la respuesta JSON
		header('Content-Type: application/json');
		if (!empty($historial)) {
			echo json_encode(['status' => true, 'historial' => $historial]);
		} else {
			echo json_encode(['status' => true, 'historial' => [], 'msg' => 'No se encontró historial para este jugador.']);
		}
		exit;
	}
	public function getServerDate()
	{
		
		$fecha_servidor = date('Y-m-d');
		$response = array(
				'status' => true,
				'fecha'  => $fecha_servidor
				// 'hora'   => $hora_servidor 
		);
		header('Content-Type: application/json');
			echo json_encode($response);
			
			// Es crucial terminar la ejecución aquí si solo quieres devolver el JSON
		exit;
	}

	// Tabla Jugadores Activos
	// public function getJugadoresActivos()
	// {
	// 	$arrData = $this->model->selectJugadoresActivos();
	// 	foreach ($arrData as $i => $jug) {
	// 		$btnEdit = '<button class="btn btn-warning btn-sm" onClick="fntEditJugador(' . $jug['id'] . ')">Editar</button>';
	// 		$btnDel  = '<button class="btn btn-danger btn-sm" onClick="fntDelJugador(' . $jug['id'] . ')">Desactivar</button>';

	// 		$arrData[$i]['status'] = '<span class="badge bg-success">Activo</span>';
	// 		$arrData[$i]['options'] = $btnEdit . ' ' . $btnDel;
	// 	}
	// 	echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
	// 	die();
	// }

	// Tabla Jugadores Libres/Inactivos
	// public function getJugadoresLibres()
	// {
	// 	$arrData = $this->model->selectJugadoresLibres();
	// 	foreach ($arrData as $i => $jug) {
	// 		$btnRestore = '<button class="btn btn-success btn-sm" onClick="fntRestoreJugador(' . $jug['id'] . ')">Activar</button>';
	// 		$arrData[$i]['status'] = '<span class="badge bg-danger">Inactivo</span>';
	// 		$arrData[$i]['options'] = $btnRestore;
	// 	}
	// 	echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
	// 	die();
	// }

	public function getJugadoresLibres()
	{
			$arrData = $this->model->selectJugadoresLibres();
			for ($i=0; $i < count($arrData); $i++) {
				$btnEdit = '<button class="btn btn-warning btn-sm" onClick="fntEditJugador(' . $arrData[$i]['id'] . ')">Editar</button>';
				$btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelJugador('.$arrData[$i]['id'].')">Desactivar</button>';
				$btnRestore = '<button class="btn btn-success btn-sm" onClick="fntRestoreJugador('.$arrData[$i]['id'].')">Activar</button>';
				//$btnVer = '<button class="btn btn-info btn-sm" onClick="fntVerClub(' . $arrData[$i]['id'] . ')">Ver</button>';

				// Botón para historial
       			 $btnHistorial = '<button class="btn btn-info btn-sm" onClick="cargarHistorial('.$arrData[$i]['id'].', this)">Ver historial</button>';

				if ($arrData[$i]['status'] == 1) {
					$arrData[$i]['status'] = '<span class="badge bg-success">Activo</span>';
					$arrData[$i]['options'] = $btnEdit . ' ' . $btnDelete;
				} else {
					$arrData[$i]['status'] = '<span class="badge bg-danger">Inactivo</span>';
					$arrData[$i]['options'] = $btnRestore;
				}
				// Nueva columna con botón
        		$arrData[$i]['historial'] = $btnHistorial;
			}
		//  Aquí estás construyendo la respuesta para DataTables
			$response = array(
			"draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 0, // Aquí va
			"recordsTotal" => count($arrData),
			"recordsFiltered" => count($arrData),
			"data" => $arrData
			/*echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
   			 die();*/
		);

		// Envías la respuesta JSON al cliente
		echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
		die();
	}

	public function getJugadoresActivos()
	{
			$arrData = $this->model->selectJugadoresActivos();
			for ($i=0; $i < count($arrData); $i++) {
				$btnEdit = '<button class="btn btn-warning btn-sm" onClick="fntEditJugador(' . $arrData[$i]['id'] . ')">Editar</button>';
				$btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelJugador('.$arrData[$i]['id'].')">Desactivar</button>';
				$btnRestore = '<button class="btn btn-success btn-sm" onClick="fntRestoreJugador('.$arrData[$i]['id'].')">Activar</button>';
				//$btnVer = '<button class="btn btn-info btn-sm" onClick="fntVerClub(' . $arrData[$i]['id'] . ')">Ver</button>';

				// Botón para historial
       			 $btnHistorial = '<button class="btn btn-info btn-sm" onClick="cargarHistorial('.$arrData[$i]['id'].', this)">Ver historial</button>';

				if ($arrData[$i]['status'] == 1) {
					$arrData[$i]['status'] = '<span class="badge bg-success">Activo</span>';
					$arrData[$i]['options'] = $btnEdit . ' ' . $btnDelete;
				} else {
					$arrData[$i]['status'] = '<span class="badge bg-danger">Inactivo</span>';
					$arrData[$i]['options'] = $btnRestore;
				}
				// Nueva columna con botón
        		$arrData[$i]['historial'] = $btnHistorial;
			}
		//  Aquí estás construyendo la respuesta para DataTables
			$response = array(
			"draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 0, // Aquí va
			"recordsTotal" => count($arrData),
			"recordsFiltered" => count($arrData),
			"data" => $arrData
			/*echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
   			 die();*/
		);

		// Envías la respuesta JSON al cliente
		echo json_encode($arrData, JSON_UNESCAPED_UNICODE);
		die();
	}

}
?>