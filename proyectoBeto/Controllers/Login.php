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

    // Login user
    public function loginUser() {
        if ($_POST) {
            if (empty($_POST['txtEmail']) || empty($_POST['txtPassword'])) {
                $arrResponse = array('status' => false, 'msg' => 'Error de datos');
            } else {
                $strEmail = strtolower(strClean($_POST['txtEmail']));
                $strPassword = $_POST['txtPassword'];

                $requestUser = $this->model->loginUser($strEmail, $strPassword);

                if (empty($requestUser)) {
                    $arrResponse = array('status' => false, 'msg' => 'Usuario o contraseña incorrectos.');
                } else {
                    $_SESSION['idUser'] = $requestUser['id_persona'];
                    $_SESSION['login'] = true;
                    $arrResponse = array('status' => true, 'msg' => 'ok');
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    // Cambiar contraseña
    public function setPassword() {
        if ($_POST) {
            if (empty($_POST['idUsuario']) || empty($_POST['txtPassword']) || empty($_POST['txtPasswordConfirm'])) {
                $arrResponse = array('status' => false, 'msg' => 'Error de datos');
            } else {
                $intIdpersona = intval($_POST['idUsuario']);
                $strPassword = $_POST['txtPassword'];
                $strPasswordConfirm = $_POST['txtPasswordConfirm'];

                if ($strPassword != $strPasswordConfirm) {
                    $arrResponse = array('status' => false, 'msg' => 'Las contraseñas no coinciden.');
                } else {
                    $request = $this->model->setPassword($intIdpersona, $strPassword);
                    if ($request) {
                        $arrResponse = array('status' => true, 'msg' => 'Contraseña actualizada con éxito.');
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'No se pudo actualizar la contraseña.');
                    }
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }
}
?>
