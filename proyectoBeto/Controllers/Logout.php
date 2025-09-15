<?php
	class Logout
	{
		public function __construct()
		{
			session_start();
			session_unset();
			session_destroy();
			header('location: '.base_url().'/login');
		}
	}

	/*
	| Acción               | Resultado                                   |
| -------------------- | ------------------------------------------- |
| `session_start()`    | Inicia sesión para poder eliminarla         |
| `session_unset()`    | Borra las variables de sesión               |
| `session_destroy()`  | Elimina la sesión completa                  |
| `header('Location')` | Redirige al login una vez cerrada la sesión |

	
	*/
 ?>