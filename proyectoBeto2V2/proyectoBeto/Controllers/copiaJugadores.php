<?php                       
// Controlador de Jugadores: gestiona creación, edición y eliminación
class Jugadores extends Controllers{
	public function __construct()
	{
		parent::__construct();
		session_start();
		if(empty($_SESSION['login']))
		{
			header('Location: '.base_url().'/login');
		}
	}

	// Carga la vista principal de jugadores
	public function Jugadores()
	{
		$data['page_tag'] = "Jugadores";
		$data['page_title'] = "JUGADORES <small>Liga de Fútbol</small>";
		$data['page_name'] = "jugadores";
		$data['page_functions_js'] = "functions_jugadores.js";
		$this->views->getView($this,"jugadores",$data);
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


				if ($arrData[$i]['status'] == 1) {
					$arrData[$i]['status'] = '<span class="badge bg-success">Activo</span>';
					$arrData[$i]['options'] = $btnEdit . ' ' . $btnDelete;
				} else {
					$arrData[$i]['status'] = '<span class="badge bg-danger">Inactivo</span>';
					$arrData[$i]['options'] = $btnRestore;
				}
			}
		//  Aquí estás construyendo la respuesta para DataTables
			$response = array(
			"draw" => isset($_GET['draw']) ? intval($_GET['draw']) : 0, // Aquí va
			"recordsTotal" => count($arrData),
			"recordsFiltered" => count($arrData),
			"data" => $arrData
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
		if($_POST){			
			
			$idJugador = intval($_POST['idJugador']);
			$strCodigo = ucwords(strClean($_POST['txtCodigo']));
			$intClub = intval($_POST['listClub']);
			$strNombre = ucwords(strClean($_POST['txtNombre']));
			$strApellido = ucwords(strClean($_POST['txtApellido']));
			$strCarnet = strClean($_POST['txtCarnet']);
			$strFechaJuego = strClean($_POST['txtFechaJuego']);
			$intStatus = intval(strClean($_POST['listStatus']));

			if($strNombre == '' || $strApellido == '' || $strCarnet == '' || $intClub == '' || $strFechaJuego == ''){
            $arrResponse = array("status" => false, "msg" => 'Datos incompletos.');
        	}else{ 
				$request_jugador = "";
				if($idJugador == 0){
                // Generar código incremental tipo JG0001
                $codigo = $this->model->generateCodigoJugador();

                // Insertar nuevo jugador
                $request_jugador = $this->model->insertJugador(
					$codigo,      // 👈 Aquí se manda el código generado
					$intClub,
					$strNombre,
					$strApellido,
					$strCarnet,
					$intStatus,
					$strFechaJuego
				); 
                	$option = 1;
				}else{
					// Actualizar jugador existente
					$request_jugador = $this->model->updateJugador(
						$idJugador,
						//$strCodigo,
						$intClub,
						$strNombre,
						$strApellido,
						$strCarnet,
						$intStatus,
						$strFechaJuego
                	);
                	$option = 2;
				}

				if ($request_jugador === "exist") {
					$arrResponse = array('status' => false, 'msg' => 'El carnet ya está registrado.');
				} elseif ($request_jugador > 0) {
					$msg = ($option == 1) ? 'Jugador creado correctamente.' : 'Jugador actualizado correctamente.';
					$arrResponse = array('status' => true, 'msg' => $msg);
				} else {
					$arrResponse = array('status' => false, 'msg' => 'No se pudo guardar el jugador.');
				}
			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        	die();	
		}
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

}

?>
