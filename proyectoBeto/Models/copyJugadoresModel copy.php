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
	private $fechaJuego;

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
	//obtener todos los jugadores
	public function selectJugadores()
	{
		$sql = "SELECT j.id, c.nombre as club, j.codigo, j.nombre, j.apellido, j.carnet, j.status, j.fechajuego
			FROM jugadores j
			INNER JOIN clubes c ON j.club_id = c.id";
		$request = $this->select_all($sql);
		return $request;
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

	//insertar nuevo jugador 
	public function insertJugador(string $club,string $nombre, string $apellido, string $carnet, int $status, string $fechaJuego)
	{
		$this->club = $club;
		$this->generateCodigoJugador(); // Código generado automáticamente
		$this->nombre = $nombre;
		$this->apellido = $apellido;
		$this->carnet = $carnet;
		$this->status = $status;
		$this->fechaJuego = $fechaJuego;

		// Verificar si ya existe por carnet
		$sql = "SELECT * FROM jugadores WHERE carnet = '{$this->carnet}'";
		$request = $this->select_all($sql);

		if (empty($request)) {
			$query_insert  = "INSERT INTO jugadores(club_id, codigo, nombre, apellido, carnet, status, fechajuego) 
							VALUES(?,?,?,?,?,?,?)";
			$arrData = array(
				$this->club,
				$this->codigo,
				$this->nombre,
				$this->apellido,
				$this->carnet,
				$this->status,
				$this->fechaJuego
			);
			$request_insert = $this->insert($query_insert, $arrData);
			return $request_insert;
		} else {
			return "exist";
		}
	}

	//Actualizar un jugador
	public function updateJugador(int $id, string $codigo, int $club, string $nombre, string $apellido, string $carnet, int $status, string $fechaJuego)
	{
		$this->id = $id;
		$this->codigo = $codigo;
		$this->club = $club;
		$this->nombre = $nombre;
		$this->apellido = $apellido;
		$this->carnet = $carnet;
		$this->status = $status;
		$this->fechaJuego = $fechaJuego;

		// Verificar si otro jugador tiene el mismo carnet
		$sql = "SELECT * FROM jugadores WHERE carnet = '{$this->carnet}' AND id != $this->id";
		$request = $this->select_all($sql);

		if (empty($request)) {
			$sql = "UPDATE jugadores SET club_id=?, codigo=?, nombre=?, apellido=?, carnet=?, status=?, fechajuego=? 
					WHERE id = ?";
			$arrData = array(
				$this->club,
				$this->codigo,
				$this->nombre,
				$this->apellido,
				$this->carnet,
				$this->status,
				$this->fechaJuego,
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


	
}
?>
