<?php
class Login extends Controllers {

    public function __construct() {
        session_start();
        if (isset($_SESSION['login'])) {
            header('Location: ' . base_url() . '/dashboard');
        }
        parent::__construct();
    }

    // Vista login
    public function login() {
        $data['page_tag'] = "Login - LIGA DE FUTBOL";
        $data['page_title'] = "Liga de futbol";
        $data['page_name'] = "login";
        $data['page_functions_js'] = "functions_login.js";
        $this->views->getView($this, "login", $data);
    }
    // función para validar el login
    public function loginUser(){
        if ($_POST) {
            // Validar datos vacíos
            if (empty($_POST['txtEmail']) || empty($_POST['txtPassword'])) {
                $arrResponse = array('status' => false, 'msg' => 'Debes ingresar usuario y contraseña.');
            } else {
                // Normalizar datos
                $strUsuario = strtolower(strClean($_POST['txtEmail']));
                $strPassword = $_POST['txtPassword'];

                // Consultar en el modelo
                $requestUser = $this->model->loginUser($strUsuario, $strPassword);

                if (empty($requestUser)) {
                    // Usuario no encontrado
                    $arrResponse = array('status' => false, 'msg' => 'Usuario o contraseña incorrectos.');
                } /*else {
                    $arrData = $requestUser;

                    // OJO: asegúrate que estos campos EXISTAN en tu tabla `persona`
                    // id_persona → PK del usuario
                    // status → campo activo/inactivo
                    if (isset($arrData['status']) && intval($arrData['status']) == 1) {
                        // Crear sesión
                        $_SESSION['idUser'] = $arrData['id_persona']; // ⚠️ Usa el nombre correcto
                        $_SESSION['login'] = true;

                        // Guardar datos de sesión
                        $arrData = $this->model->sessionLogin($_SESSION['idUser']);
                        $_SESSION['userData'] = $arrData;

                        $arrResponse = array('status' => true, 'msg' => 'ok');
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'Usuario inactivo.');
                    }
                }*/
            }

            // Siempre devolver JSON válido
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
            die();
        }
    }




}

?>
