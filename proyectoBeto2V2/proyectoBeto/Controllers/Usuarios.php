<?php 
// Controlador de Usuarios: gestiona creación, edición, eliminación y perfil
	class Usuarios extends Controllers{
		public function __construct()
		{
			// Constructor: valida sesión y permisos
			parent::__construct();
			session_start();
			if(empty($_SESSION['login']))
			{
				header('Location: '.base_url().'/login');
			}
			getPermisos(2);// Módulo de usuarios
		}
		// Carga la vista principal de usuarios
		public function Usuarios()
		{
			if(empty($_SESSION['permisosMod']['r'])){
				header("Location:".base_url().'/dashboard');
			}
			$data['page_tag'] = "Usuarios";
			$data['page_title'] = "USUARIOS <small>Tienda CBN</small>";
			$data['page_name'] = "usuarios";
			$data['page_functions_js'] = "functions_usuarios.js";
			$this->views->getView($this,"usuarios",$data);
		}
		// Inserta o actualiza un usuario
		public function setUsuario(){
			if($_POST){			
				// Validación de campos obligatorios
				if(empty($_POST['txtIdentificacion']) || empty($_POST['txtNombre']) || empty($_POST['txtApellido']) || empty($_POST['txtTelefono']) || empty($_POST['txtEmail']) || empty($_POST['listRolid']) || empty($_POST['listStatus']) )
				{
					$arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
				}else{ 
					// Obtener datos y limpiar
					$idUsuario = intval($_POST['idUsuario']);
					$strIdentificacion = strClean($_POST['txtIdentificacion']);
					$strNombre = ucwords(strClean($_POST['txtNombre']));
					$strApellido = ucwords(strClean($_POST['txtApellido']));
					$intTelefono = intval(strClean($_POST['txtTelefono']));
					$strEmail = strtolower(strClean($_POST['txtEmail']));
					$intTipoId = intval(strClean($_POST['listRolid']));
					$intStatus = intval(strClean($_POST['listStatus']));
					$request_user = "";
					if($idUsuario == 0)
					{
						// CREAR nuevo usuario
						$option = 1;
						$strPassword =  empty($_POST['txtPassword']) ? hash("SHA256",passGenerator()) : hash("SHA256",$_POST['txtPassword']);

						if($_SESSION['permisosMod']['w']){
							$request_user = $this->model->insertUsuario($strIdentificacion,
																				$strNombre, 
																				$strApellido, 
																				$intTelefono, 
																				$strEmail,
																				$strPassword, 
																				$intTipoId, 
																				$intStatus );
						}
					}else{
						// ACTUALIZAR usuario existente
						$option = 2;
						$strPassword =  empty($_POST['txtPassword']) ? "" : hash("SHA256",$_POST['txtPassword']);
						if($_SESSION['permisosMod']['u']){
							$request_user = $this->model->updateUsuario($idUsuario,
																		$strIdentificacion, 
																		$strNombre,
																		$strApellido, 
																		$intTelefono, 
																		$strEmail,
																		$strPassword, 
																		$intTipoId, 
																		$intStatus);
						}

					}
					// Verificar resultado
					if($request_user > 0 )
					{
						if($option == 1){
							$arrResponse = array('status' => true, 'msg' => 'Datos guardados correctamente.');
						}else{
							$arrResponse = array('status' => true, 'msg' => 'Datos Actualizados correctamente.');
						}
					}else if($request_user == 'exist'){
						$arrResponse = array('status' => false, 'msg' => '¡Atención! el email o la identificación ya existe, ingrese otro.');		
					}else{
						$arrResponse = array("status" => false, "msg" => 'No es posible almacenar los datos.');
					}
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
			die();
		}
		// Devuelve todos los usuarios con botones de acción (para DataTables)
		public function getUsuarios()
		{
			if($_SESSION['permisosMod']['r']){
				$arrData = $this->model->selectUsuarios();
				for ($i=0; $i < count($arrData); $i++) {
					$btnView = '';
					$btnEdit = '';
					$btnDelete = '';
					// Mostrar estado
					if($arrData[$i]['status'] == 1)
					{
						$arrData[$i]['status'] = '<span class="badge badge-success">Activo</span>';
					}else{
						$arrData[$i]['status'] = '<span class="badge badge-danger">Inactivo</span>';
					}
					// Ver usuario
					if($_SESSION['permisosMod']['r']){
						$btnView = '<button class="btn btn-info btn-sm btnViewUsuario" onClick="fntViewUsuario('.$arrData[$i]['idpersona'].')" title="Ver usuario"><i class="far fa-eye"></i></button>';
					}
					// Editar usuario con validaciones de privilegio
					if($_SESSION['permisosMod']['u']){
						if(($_SESSION['idUser'] == 1 and $_SESSION['userData']['idrol'] == 1) ||
							($_SESSION['userData']['idrol'] == 1 and $arrData[$i]['idrol'] != 1) ){
							$btnEdit = '<button class="btn btn-primary  btn-sm btnEditUsuario" onClick="fntEditUsuario(this,'.$arrData[$i]['idpersona'].')" title="Editar usuario"><i class="fas fa-pencil-alt"></i></button>';
						}else{
							$btnEdit = '<button class="btn btn-secondary btn-sm" disabled ><i class="fas fa-pencil-alt"></i></button>';
						}
					}
					// Eliminar usuario con reglas de protección
					if($_SESSION['permisosMod']['d']){
						if(($_SESSION['idUser'] == 1 and $_SESSION['userData']['idrol'] == 1) ||
							($_SESSION['userData']['idrol'] == 1 and $arrData[$i]['idrol'] != 1) and
							($_SESSION['userData']['idpersona'] != $arrData[$i]['idpersona'] )
							 ){
							$btnDelete = '<button class="btn btn-danger btn-sm btnDelUsuario" onClick="fntDelUsuario('.$arrData[$i]['idpersona'].')" title="Eliminar usuario"><i class="far fa-trash-alt"></i></button>';
						}else{
							$btnDelete = '<button class="btn btn-secondary btn-sm" disabled ><i class="far fa-trash-alt"></i></button>';
						}
					}
					$arrData[$i]['options'] = '<div class="text-center">'.$btnView.' '.$btnEdit.' '.$btnDelete.'</div>';
				}
				echo json_encode($arrData,JSON_UNESCAPED_UNICODE);
			}
			die();
		}
		// Devuelve un usuario específico por ID (para editar o ver)
		public function getUsuario($idpersona){
			if($_SESSION['permisosMod']['r']){
				$idusuario = intval($idpersona);
				if($idusuario > 0)
				{
					$arrData = $this->model->selectUsuario($idusuario);
					if(empty($arrData))
					{
						$arrResponse = array('status' => false, 'msg' => 'Datos no encontrados.');
					}else{
						$arrResponse = array('status' => true, 'data' => $arrData);
					}
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
				}
			}
			die();
		}
		// Elimina un usuario (por ID)
		public function delUsuario()
		{
			if($_POST){
				if($_SESSION['permisosMod']['d']){
					$intIdpersona = intval($_POST['idUsuario']);
					$requestDelete = $this->model->deleteUsuario($intIdpersona);
					if($requestDelete)
					{
						$arrResponse = array('status' => true, 'msg' => 'Se ha eliminado el usuario');
					}else{
						$arrResponse = array('status' => false, 'msg' => 'Error al eliminar el usuario.');
					}
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
				}
			}
			die();
		}
		// Cargar vista de perfil
		public function perfil(){
			$data['page_tag'] = "Perfil";
			$data['page_title'] = "Perfil de usuario";
			$data['page_name'] = "perfil";
			$data['page_functions_js'] = "functions_usuarios.js";
			$this->views->getView($this,"perfil",$data);
		}
		// Actualizar datos del perfil del usuario actual
		public function putPerfil(){
			if($_POST){
				if(empty($_POST['txtIdentificacion']) || empty($_POST['txtNombre']) || empty($_POST['txtApellido']) || empty($_POST['txtTelefono']) )
				{
					$arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
				}else{
					$idUsuario = $_SESSION['idUser'];
					$strIdentificacion = strClean($_POST['txtIdentificacion']);
					$strNombre = strClean($_POST['txtNombre']);
					$strApellido = strClean($_POST['txtApellido']);
					$intTelefono = intval(strClean($_POST['txtTelefono']));
					$strPassword = "";
					if(!empty($_POST['txtPassword'])){
						$strPassword = hash("SHA256",$_POST['txtPassword']);
					}
					$request_user = $this->model->updatePerfil($idUsuario,
																$strIdentificacion, 
																$strNombre,
																$strApellido, 
																$intTelefono, 
																$strPassword);
					if($request_user)// actualizar sesión
					{
						sessionUser($_SESSION['idUser']);
						$arrResponse = array('status' => true, 'msg' => 'Datos Actualizados correctamente.');
					}else{
						$arrResponse = array("status" => false, "msg" => 'No es posible actualizar los datos.');
					}
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
			die();
		}
		// Actualizar datos fiscales del usuario
		public function putDFical(){
			if($_POST){
				if(empty($_POST['txtNit']) || empty($_POST['txtNombreFiscal']) || empty($_POST['txtDirFiscal']) )
				{
					$arrResponse = array("status" => false, "msg" => 'Datos incorrectos.');
				}else{
					$idUsuario = $_SESSION['idUser'];
					$strNit = strClean($_POST['txtNit']);
					$strNomFiscal = strClean($_POST['txtNombreFiscal']);
					$strDirFiscal = strClean($_POST['txtDirFiscal']);
					$request_datafiscal = $this->model->updateDataFiscal($idUsuario,
																		$strNit,
																		$strNomFiscal, 
																		$strDirFiscal);
					if($request_datafiscal)
					{
						sessionUser($_SESSION['idUser']);
						$arrResponse = array('status' => true, 'msg' => 'Datos Actualizados correctamente.');
					}else{
						$arrResponse = array("status" => false, "msg" => 'No es posible actualizar los datos.');
					}
				}
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
			}
			die();
		}

	}
	/* 
	  📌 Principales métodos y funciones explicadas
1. Usuarios()
Carga la vista de la lista de usuarios si el usuario tiene permisos de lectura.

2. setUsuario()
Guarda un nuevo usuario o actualiza uno existente. Incluye validación de campos y control de permisos.

3. getUsuarios()
Devuelve todos los usuarios en formato JSON (para usar con DataTables). También genera los botones de acción según los permisos del usuario actual.

4. getUsuario($idpersona)
Devuelve los datos de un usuario específico en formato JSON. Se usa para cargar datos en formularios.

5. delUsuario()
Elimina un usuario, siempre y cuando tenga permiso y no esté intentando eliminarse a sí mismo (protección adicional).

6. perfil()
Muestra la vista del perfil del usuario logueado.

7. putPerfil()
Actualiza los datos del perfil del usuario que está logueado (nombre, teléfono, etc.)

8. putDFical()
Actualiza los datos fiscales del usuario logueado (NIT, nombre fiscal, dirección fiscal).
*/
 ?>