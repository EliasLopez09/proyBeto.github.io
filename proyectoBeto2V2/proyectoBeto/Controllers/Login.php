<?php 
/*

| Función         | Qué hace                                                                       |
| --------------- | ------------------------------------------------------------------------------ |
| `login()`       | Carga la vista de login                                                        |
| `loginUser()`   | Verifica credenciales y crea sesión                                            |
| `resetPass()`   | Envia correo con token de recuperación                                         |
| `confirmUser()` | Verifica token de recuperación y muestra el formulario para cambiar contraseña |
| `setPassword()` | Actualiza la contraseña del usuario                                            |


*/ 

class Login extends Controllers {
    // Constructor: verifica si ya hay sesión iniciada y redirige al dashboard
    public function __construct() {
        session_start();
        if (isset($_SESSION['login'])) {
            header('Location: ' . base_url() . '/dashboard');
        }
        parent::__construct();
    }
    
    // Carga la vista del login con datos básicos de la página
    public function login() {
        $data['page_tag'] = "Login - Tienda_Virtual";
        $data['page_title'] = "Tienda CBN";
        $data['page_name'] = "login";
        $data['page_functions_js'] = "functions_login.js";
        $this->views->getView($this, "login", $data);
    }

    // Función que autentica al usuario (login)
    public function loginUser() {
        if ($_POST) {
            // Validación de campos vacíos
            if (empty($_POST['txtEmail']) || empty($_POST['txtPassword'])) {
                $arrResponse = array('status' => false, 'msg' => 'Error de datos');
            } else {
                // Limpieza y encriptación de datos
                $strUsuario = strtolower(strClean($_POST['txtEmail']));
                $strPassword = $_POST['txtPassword'];
                // $strPassword = hash("SHA256", $_POST['txtPassword']);
                
                // Consulta en el modelo
                $requestUser = $this->model->loginUser($strUsuario, $strPassword);

                if (empty($requestUser)) {
                    $mi_cadena = 'Esto es un string';
    
                    $arrResponse = array(
                        'status' => false,
                        'msg' => $mi_cadena
                    );
                } else {
                    $arrData = $requestUser;
                    if ($arrData['status'] == 1) {
                        // Crear sesión
                        $_SESSION['idUser'] = $arrData['idpersona'];
                        $_SESSION['login'] = true;

                        // Guardar datos de sesión
                        $arrData = $this->model->sessionLogin($_SESSION['idUser']);
                        sessionUser($_SESSION['idUser']);

                        $arrResponse = array('status' => true, 'msg' => 'ok');
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'Usuario inactivo.');
                    }
                }
            }
            // Enviar respuesta al cliente (JavaScript)
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    // Envía correo para restablecer contraseña
    public function resetPass() {
        if ($_POST) {
            error_reporting(0);

            if (empty($_POST['txtEmailReset'])) {
                $arrResponse = array('status' => false, 'msg' => 'Error de datos');
            } else {
                $token = token();
                $strEmail = strtolower(strClean($_POST['txtEmailReset']));
                $arrData = $this->model->getUserEmail($strEmail);

                if (empty($arrData)) {
                    $arrResponse = array('status' => false, 'msg' => 'Usuario no existente.');
                } else {
                    // Datos del usuario
                    $idpersona = $arrData['idpersona'];
                    $nombreUsuario = $arrData['nombres'] . ' ' . $arrData['apellidos'];

                    // Generar URL de recuperación
                    $url_recovery = base_url() . '/login/confirmUser/' . $strEmail . '/' . $token;
                    $requestUpdate = $this->model->setTokenUser($idpersona, $token);

                    // Preparar datos para el correo
                    $dataUsuario = array(
                        'nombreUsuario' => $nombreUsuario,
                        'email' => $strEmail,
                        'asunto' => 'Recuperar cuenta - ' . NOMBRE_REMITENTE,
                        'url_recovery' => $url_recovery
                    );

                    if ($requestUpdate) {
                        // Enviar correo electrónico
                        $sendEmail = sendEmail($dataUsuario, 'email_cambioPassword');

                        if ($sendEmail) {
                            $arrResponse = array('status' => true, 'msg' => 'Se ha enviado un email a tu cuenta de correo para cambiar tu contraseña.');
                        } else {
                            $arrResponse = array('status' => false, 'msg' => 'No es posible realizar el proceso, intenta más tarde.');
                        }
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'No es posible realizar el proceso, intenta más tarde.');
                    }
                }
            }
            echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        }
        die();
    }

    // Muestra el formulario para cambiar la contraseña (vía token)
    public function confirmUser(string $params) {
        if (empty($params)) {
            header('Location: ' . base_url());
        } else {
            // Separar email y token
            $arrParams = explode(',', $params);
            $strEmail = strClean($arrParams[0]);
            $strToken = strClean($arrParams[1]);

            $arrResponse = $this->model->getUsuario($strEmail, $strToken);

            if (empty($arrResponse)) {
                header("Location: " . base_url());
            } else {
                // Preparar vista para cambiar contraseña
                $data['page_tag'] = "Cambiar contraseña";
                $data['page_name'] = "cambiar_contrasenia";
                $data['page_title'] = "Cambiar Contraseña";
                $data['email'] = $strEmail;
                $data['token'] = $strToken;
                $data['idpersona'] = $arrResponse['idpersona'];
                $data['page_functions_js'] = "functions_login.js";
                $this->views->getView($this, "cambiar_password", $data);
            }
        }
        die();
    }

    // Guarda la nueva contraseña del usuario
    public function setPassword() {
        if (
            empty($_POST['idUsuario']) ||
            empty($_POST['txtEmail']) ||
            empty($_POST['txtToken']) ||
            empty($_POST['txtPassword']) ||
            empty($_POST['txtPasswordConfirm'])
        ) {
            $arrResponse = array('status' => false, 'msg' => 'Error de datos');
        } else {
            // Obtener y limpiar datos
            $intIdpersona = intval($_POST['idUsuario']);
            $strPassword = $_POST['txtPassword'];
            $strPasswordConfirm = $_POST['txtPasswordConfirm'];
            $strEmail = strClean($_POST['txtEmail']);
            $strToken = strClean($_POST['txtToken']);

            // Verificar que ambas contraseñas coincidan
            if ($strPassword != $strPasswordConfirm) {
                $arrResponse = array('status' => false, 'msg' => 'Las contraseñas no son iguales.');
            } else {
                // Verificar si el token es válido
                $arrResponseUser = $this->model->getUsuario($strEmail, $strToken);

                if (empty($arrResponseUser)) {
                    $arrResponse = array('status' => false, 'msg' => 'Error de datos.');
                } else {
                    // Guardar nueva contraseña encriptada
                    $strPassword = hash("SHA256", $strPassword);
                    $requestPass = $this->model->insertPassword($intIdpersona, $strPassword);

                    if ($requestPass) {
                        $arrResponse = array('status' => true, 'msg' => 'Contraseña actualizada con éxito.');
                    } else {
                        $arrResponse = array('status' => false, 'msg' => 'No es posible realizar el proceso, intente más tarde.');
                    }
                }
            }
        }
        echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
        die();
    }
}
 ?>