<?php
// Controlador de Clubes: gestiona el CRUD de clubes deportivos
class Clubes extends Controllers
{
    public function __construct()
    {
        parent::__construct();
        session_start();
        
        // Redirigir si no hay sesión iniciada
        // if (empty($_SESSION['login'])) {
        //     header('Location: ' . base_url() . '/login');
        //     die();
        // }
    }

    // Carga la vista principal de clubes
    public function clubes()
    {
        //$data['page_id'] = 1;
        $data['page_tag'] = "Clubes";
        $data['page_title'] = "Clubes Activos";
        $data['page_name'] = "clubes";
        $data['page_functions_js'] = "functions_clubes.js"; 
        $this->views->getView($this, "clubes", $data);
    }
    public function clubesLibres()
    {
        //$data['page_id'] = 1;
        $data['page_tag'] = "Clubes Libres";
        $data['page_title'] = "Clubes Libres";
        $data['page_name'] = "clubesLibres";
        $data['page_functions_js'] = "functions_clubes.js"; 
        $this->views->getView($this, "clubesLibres", $data);
    }
    

    // Obtiene todos los clubes (activos e inactivos) para mostrar en tabla
public function getClubes()
{
    $arrData = $this->model->selectClubes();

    for ($i = 0; $i < count($arrData); $i++) {
        $btnEdit = '<button class="btn btn-warning btn-sm" onClick="fntEditClub(' . $arrData[$i]['id'] . ')">Editar</button>';
        $btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelClub('.$arrData[$i]['id'].')">Desactivar</button>';
        $btnRestore = '<button class="btn btn-success btn-sm" onClick="fntRestoreClub(' . $arrData[$i]['id'] . ')">Activar</button>';
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
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    die();
}



    // Obtiene un club específico por ID
    public function getClub($id)
    {
        $id = intval($id);
        if ($id > 0) {
            $data = $this->model->selectClub($id);
            if (empty($data)) {
                echo json_encode(["status" => false, "msg" => "Club no encontrado"], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(["status" => true, "data" => $data], JSON_UNESCAPED_UNICODE);
            }
        }
        die();
    }

    // Inserta o actualiza un club
    public function setClub()
    {
        $intId = intval($_POST['idClub']);
        $strNombre = strClean($_POST['txtNombre']);
       $intStatus = 1; // Siempre activo al crear o actualizar

        if ($intId == 0) {
            // Nuevo registro
            $request = $this->model->insertClub($strNombre);
            $option = 1;
        } else {
            // Actualización
            $request = $this->model->updateClub($intId, $strNombre);
            $option = 2;
        }
        
        
        if ($request === "exist") {
        $arrResponse = array('status' => false, 'msg' => 'El nombre del club ya existe.');
    } elseif ($request > 0) {
        $msg = ($option == 1) ? 'Club creado correctamente.' : 'Club actualizado correctamente.';
        $arrResponse = array('status' => true, 'msg' => $msg);
    } else {
        $arrResponse = array('status' => false, 'msg' => 'No se pudo guardar el club.');
    }

        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }

    // Desactiva un club (eliminación lógica)
    public function delClub()
    {
        $id = intval($_POST['idClub']); // <- necesita 'idClub'
        $request = $this->model->deleteClub($id);
        if ($request) {
            echo json_encode(['status' => true, 'msg' => 'Club desactivado correctamente.'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['status' => false, 'msg' => 'Error al desactivar el club.'], JSON_UNESCAPED_UNICODE);
        }
        die();
    }


    // Reactiva un club (cambia status a 1)
    public function restoreClub()
    {   
    
        $id = intval($_POST['idClub']);
        $request = $this->model->restoreClub($id); // llama al modelo
        if ($request) {
            echo json_encode(['status' => true, 'msg' => 'Club activado correctamente.'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['status' => false, 'msg' => 'Error al activar el club.'], JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    // Devuelve los roles activos en <option> para <select>
		public function getSelectClubes()
		{
			$htmlOptions = "";
			$arrData = $this->model->selectClubes();
			if(count($arrData) > 0 ){
				for ($i=0; $i < count($arrData); $i++) { 
					if($arrData[$i]['status'] == 1 ){
					$htmlOptions .= '<option value="'.$arrData[$i]['id'].'">'.$arrData[$i]['nombre'].'</option>';
					}
				}
			}
			echo $htmlOptions;
			die();		
		}
        // obtenemos clubes libres
    public function getClubesLibres()
    {
        $arrData = $this->model->selectClubesLibres();

        for ($i = 0; $i < count($arrData); $i++) {
            $btnEdit = '<button class="btn btn-warning btn-sm" onClick="fntEditClub(' . $arrData[$i]['id'] . ')">Editar</button>';
            $btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelClub('.$arrData[$i]['id'].')">Desactivar</button>';
            $btnRestore = '<button class="btn btn-success btn-sm" onClick="fntRestoreClub(' . $arrData[$i]['id'] . ')">Activar</button>';
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
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        die();
    }
    // obtenemos clubes activos
    public function getClubesActivos()
    {
        $arrData = $this->model->selectClubesActivos();

        for ($i = 0; $i < count($arrData); $i++) {
            $btnEdit = '<button class="btn btn-warning btn-sm" onClick="fntEditClub(' . $arrData[$i]['id'] . ')">Editar</button>';
            $btnDelete = '<button class="btn btn-danger btn-sm" onClick="fntDelClub('.$arrData[$i]['id'].')">Desactivar</button>';
            $btnRestore = '<button class="btn btn-success btn-sm" onClick="fntRestoreClub(' . $arrData[$i]['id'] . ')">Activar</button>';
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
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        die();
    }
}
?>
