<?php
class ClubesModel extends Mysql
{
    private $id;
    private $nombre;
    private $status;

    public function __construct()
    {
        parent::__construct();
    }

    // Obtener todos los clubes
    public function selectClubes()
    {
        $sql = "SELECT * FROM clubes";
        $request = $this->select_all($sql);
        return $request;
    }

    // Obtener un solo club por ID
    public function selectClub(int $id)
    {
        $this->id = $id;
        $sql = "SELECT * FROM clubes WHERE id = $this->id";
        $request = $this->select($sql);
        return $request;
    }

    // Insertar un nuevo club
    public function insertClub(string $nombre)
    {
        $this->nombre = $nombre;

        // Verificar si ya existe
        $sql = "SELECT * FROM clubes WHERE nombre = '{$this->nombre}'";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $query_insert = "INSERT INTO clubes(nombre) VALUES(?)";
            $arrData = array($this->nombre);
            $request_insert = $this->insert($query_insert, $arrData);
            return $request_insert;
        } else {
            return "exist";
        }
    }

    // Actualizar un club
    public function updateClub(int $id, string $nombre)
    {
        $this->id = $id;
        $this->nombre = $nombre;

        // Validar duplicados en nombre (excluyendo este mismo ID)
        $sql = "SELECT * FROM clubes WHERE nombre = '{$this->nombre}' AND id != $this->id";
        $request = $this->select_all($sql);

        if (empty($request)) {
            $sql = "UPDATE clubes SET nombre = ? WHERE id = ?";
            $arrData = array($this->nombre, $this->id);
            $request = $this->update($sql, $arrData);
            return $request;
        } else {
            return "exist";
        }
    }

    // Desactivar club (eliminación lógica)
    public function deleteClub(int $id)
    {
    $sql = "UPDATE clubes SET status = 0 WHERE id = ?";
    $arrData = [$id];
    $request = $this->update($sql, $arrData);
    return $request;
    }


    // Activar club (si usas esto)
    public function restoreClub(int $id)
    {
        $sql = "UPDATE clubes SET status = 1 WHERE id = ?";
        $arrData = [$id];
        $request = $this->update($sql, $arrData);
        return $request;
    }

}
?>
