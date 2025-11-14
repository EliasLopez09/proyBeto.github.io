<?php 

class UsuariosModel extends Mysql
{
    private $intIdUsuario;
    private $strNombre;
    private $strApellido;
    private $strEmail;
    private $strPassword;

    public function __construct()
    {
        parent::__construct();
    }	

    // Insertar nuevo usuario
    public function insertUsuario(string $nombre, string $apellido, string $email, string $password){
        $this->strNombre = $nombre;
        $this->strApellido = $apellido;
        $this->strEmail = $email;
        $this->strPassword = $password;
        
        $return = 0;

        // Verificar si ya existe el correo
        $sql = "SELECT * FROM persona WHERE email = '{$this->strEmail}' ";
        $request = $this->select_all($sql);

        if(empty($request)){
            $query_insert  = "INSERT INTO persona(nombre,apellido,email,password) VALUES(?,?,?,?)";
            $arrData = array($this->strNombre,
                             $this->strApellido,
                             $this->strEmail,
                             $this->strPassword);
            $request_insert = $this->insert($query_insert,$arrData);
            $return = $request_insert;
        }else{
            $return = "exist";
        }
        return $return;
    }

    // Listar usuarios
    public function selectUsuarios(){
        $sql = "SELECT id_persona,nombre,apellido,email,fyh_creacion 
                FROM persona";
        $request = $this->select_all($sql);
        return $request;
    }

    // Seleccionar un usuario por ID
    public function selectUsuario(int $idUsuario){
        $this->intIdUsuario = $idUsuario;
        $sql = "SELECT id_persona,nombre,apellido,email,fyh_creacion 
                FROM persona 
                WHERE id_persona = $this->intIdUsuario";
        $request = $this->select($sql);
        return $request;
    }

    // Actualizar usuario
    public function updateUsuario(int $idUsuario, string $nombre, string $apellido, string $email, string $password){
        $this->intIdUsuario = $idUsuario;
        $this->strNombre = $nombre;
        $this->strApellido = $apellido;
        $this->strEmail = $email;
        $this->strPassword = $password;

        // Verificar email único
        $sql = "SELECT * FROM persona WHERE email = '{$this->strEmail}' AND id_persona != $this->intIdUsuario";
        $request = $this->select_all($sql);

        if(empty($request)){
            if($this->strPassword != ""){
                $sql = "UPDATE persona SET nombre=?, apellido=?, email=?, password=? 
                        WHERE id_persona = $this->intIdUsuario ";
                $arrData = array($this->strNombre,
                                 $this->strApellido,
                                 $this->strEmail,
                                 $this->strPassword);
            }else{
                $sql = "UPDATE persona SET nombre=?, apellido=?, email=? 
                        WHERE id_persona = $this->intIdUsuario ";
                $arrData = array($this->strNombre,
                                 $this->strApellido,
                                 $this->strEmail);
            }
            $request = $this->update($sql,$arrData);
        }else{
            $request = "exist";
        }
        return $request;
    }

    // Eliminar usuario
    public function deleteUsuario(int $idUsuario){
        $this->intIdUsuario = $idUsuario;
        $sql = "DELETE FROM persona WHERE id_persona = $this->intIdUsuario ";
        $request = $this->delete($sql);
        return $request;
    }
}
?>
