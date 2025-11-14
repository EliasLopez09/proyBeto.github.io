<?php 

class JugadoresModel extends Mysql
{
	private $id;
	private $club;
	private $codigo;
	private $nombre;
	private $apellido;
	private $carnet;
	private $status;
	private $fechaNacimiento;

	public function __construct()
	{
		parent::__construct();
	} 

	public function generateCodigoJugador() {
		$sql = "SELECT codigo FROM jugadores ORDER BY id DESC LIMIT 1";
		$request = $this->select($sql);
		
		if (!empty($request)) {
			$num = intval(substr($request['codigo'], 2)) + 1;
		} else {
			$num = 1;
		}

		return 'JG' . str_pad($num, 4, "0", STR_PAD_LEFT);
	}
	public function selectJugadores()
	{
		$sql = "SELECT j.id, c.nombre as club, j.codigo, j.nombre, j.apellido, j.carnet, j.status, j.fechanacimiento
				FROM jugadores j
				INNER JOIN clubes c ON j.club_id = c.id";
		$jugadores = $this->select_all($sql);

		// Agregamos historial a cada jugador
		foreach ($jugadores as &$jugador) {
			$historial = $this->selectHistorialPorJugador($jugador['id']);
			$jugador['historial'] = $historial;
		}

		return $jugadores;
	}

	//obtener un solo jugador por id
	public function selectJugador(int $id){
		$this->id = $id;
		$sql = "SELECT j.*, c.nombre as club_nombre
				FROM jugadores j
				INNER JOIN clubes c ON j.club_id = c.id
				WHERE j.id = $this->id";
		$request = $this->select($sql);
		return $request;
	}

	//obtener historial de juegos por jugador
	public function selectHistorialPorJugador($jugador_id)
	{
		$sql = "SELECT fecha_partido 
				FROM historial_juegos 
				WHERE jugador_id = $jugador_id 
				ORDER BY fecha_partido DESC";
		$request = $this->select_all($sql);
		return $request;
	}



	//insertar nuevo jugador 
	//insertar nuevo jugador
public function insertJugador(string $club, string $codigo, string $nombre, string $apellido, string $carnet, string $fechaNacimiento, string $fechaPartido)
{
    $this->club = $club;
    $this->codigo = $codigo;
    $this->nombre = $nombre;
    $this->apellido = $apellido;
    $this->carnet = $carnet;
    //$this->status = $status;
    $this->fechaNacimiento = $fechaNacimiento;
    $this->fechaPartido = $fechaPartido;

    // Verificar si ya existe el jugador por carnet
    $sql = "SELECT * FROM jugadores WHERE carnet = '{$this->carnet}'";
    $request = $this->select_all($sql);

    if (empty($request)) {
			// Insertar jugador
			$query_insert  = "INSERT INTO jugadores(club_id, codigo, nombre, apellido, carnet, fechanacimiento)
							VALUES(?,?,?,?,?,?)";
			$arrData = array(
				$this->club,
				$this->codigo,
				$this->nombre,
				$this->apellido,
				$this->carnet,
				// $this->status,
				$this->fechaNacimiento
			);
			$request_insert = $this->insert($query_insert, $arrData);

			if ($request_insert > 0) {
				// Insertar fecha del partido en historial_juegos
				$query_historial = "INSERT INTO historial_juegos(jugador_id, fecha_partido)
									VALUES(?, ?)";
				$arrHistorial = array($request_insert, $this->fechaPartido);
				$this->insert($query_historial, $arrHistorial);
			}

			return $request_insert;
		} else {
			return "exist";
		}
	}


	//Actualizar un jugador
	public function updateJugador(int $id, int $club, string $nombre, string $apellido, string $carnet, string $fechaNacimiento)
	{
		$this->id = $id;
		//$this->codigo = $codigo;
		$this->club = $club;
		$this->nombre = $nombre;
		$this->apellido = $apellido;
		$this->carnet = $carnet;
		//$this->status = $status;
		$this->fechaNacimiento = $fechaNacimiento;

		// Verificar si otro jugador tiene el mismo carnet
		$sql = "SELECT * FROM jugadores WHERE carnet = '{$this->carnet}' AND id != $this->id";
		$request = $this->select_all($sql);

		if (empty($request)) {
			$sql = "UPDATE jugadores SET club_id=?, nombre=?, apellido=?, carnet=?, fechanacimiento=? 
					WHERE id = ?";
			$arrData = array(
				$this->club,
				//$this->codigo,
				$this->nombre,
				$this->apellido,
				$this->carnet,
				// $this->status,
				$this->fechaNacimiento,
				$this->id
			);
			$request = $this->update($sql, $arrData);
			return $request;
		} else {
			return "exist";
		}
	}

	public function deleteJugador(int $id)
	{
		$sql = "UPDATE jugadores SET status = 0 WHERE id = ?";
		$arrData = [$id];
		$request = $this->update($sql,$arrData);
		if($request)
		{
			$request = 'ok';	
		}else{
			$request = 'error';
		}
			
		return $request;
	}
	public function restoreJugador(int $id)
	{
		$sql = "UPDATE jugadores SET status = 1 WHERE id = ?";
		$arrData = [$id];
		$request = $this->update($sql, $arrData);
		return $request;
	}

	public function selectJugadoresLibres()
	{
		$sql = "SELECT j.id, c.nombre as club, j.codigo, j.nombre, j.apellido, j.carnet, j.status, j.fechanacimiento
				FROM jugadores j
				INNER JOIN clubes c ON j.club_id = c.id WHERE j.status = 0"; // Solo jugadores inactivos
		$jugadores = $this->select_all($sql);

		// Agregamos historial a cada jugador
		foreach ($jugadores as &$jugador) {
			$historial = $this->selectHistorialPorJugador($jugador['id']);
			$jugador['historial'] = $historial;
		}

		return $jugadores;
	}

	public function selectJugadoresActivos()
	{
		$sql = "SELECT j.id, c.nombre as club, j.codigo, j.nombre, j.apellido, j.carnet, j.status, j.fechanacimiento
				FROM jugadores j
				INNER JOIN clubes c ON j.club_id = c.id WHERE j.status = 1"; // Solo jugadores inactivos
		$jugadores = $this->select_all($sql);

		// Agregamos historial a cada jugador
		foreach ($jugadores as &$jugador) {
			$historial = $this->selectHistorialPorJugador($jugador['id']);
			$jugador['historial'] = $historial;
		}

		return $jugadores;
	}

	// Obtener jugador por número de carnet
	public function selectJugadorByCarnet(string $carnet)
	{
		$this->carnet = $carnet;
		$sql = "SELECT id, club_id, codigo, nombre, apellido, carnet, fechanacimiento, status 
				FROM jugadores 
				WHERE carnet = '{$this->carnet}' LIMIT 1";
		$request = $this->select($sql);
		return $request;
	}

}
?>
