<?php
class UsuariosModel extends Mysql {
    private $intIdUsuario;
    private $strNombre;
    private $strApellido;
    private $strEmail;
    private $strPassword;

    public function __construct() {
        parent::__construct();
    }

    // Insertar usuario
    public function insertUsuario(string $nombre, string $apellido, string $email, string $password) {
        $this->strNombre = $nombre;
        $this->strApellido = $apellido;
        $this->strEmail = $email;
        $this->strPassword = hash("SHA256", $password); // encriptar

        $sql = "SELECT * FROM persona WHERE email = '{$this->strEmail}'";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $query_insert = "INSERT INTO persona(nombre, apellido, email, password) VALUES(?,?,?,?)";
            $arrData = array($this->strNombre, $this->strApellido, $this->strEmail, $this->strPassword);
            $request_insert = $this->insert($query_insert, $arrData);
            return $request_insert;
        } else {
            return "exist";
        }
    }

    // Login
    public function loginUser(string $email, string $password) {
        //$strPassword = hash("SHA256", $password);
        $sql = "SELECT * FROM persona WHERE email = '$email' AND password = '$password'";
        $request = $this->select($sql);
        return $request;
    }

    // Actualizar contraseña
    public function setPassword(int $idUsuario, string $password) {
        $strPassword = hash("SHA256", $password);
        $sql = "UPDATE persona SET password = ? WHERE id_persona = ?";
        $arrData = array($strPassword, $idUsuario);
        $request = $this->update($sql, $arrData);
        return $request;
    }

    // Obtener usuario por id
    public function getUsuario(int $idUsuario) {
        $sql = "SELECT * FROM persona WHERE id_persona = $idUsuario";
        $request = $this->select($sql);
        return $request;
    }
}
?>
