<?php
function getIdFromIp(){
	if (!empty($_SERVER['HTTP_CLIENT_IP'])) { $outsider_ip = $_SERVER['HTTP_CLIENT_IP']; }
	else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { $outsider_ip = $_SERVER['HTTP_X_FORWARDED_FOR']; }
	else { $outsider_ip = $_SERVER['REMOTE_ADDR']; }

	//Inicializamos la variable
	$matchId = -1;

	//Intentamos recuperar de la base de datos el listado de DNS de nuestros Clientes
	try {
		$datos = DB::query("SELECT `dns`, `id_cliente` FROM `clientes`");
	} 
	catch (MeekroDBException $e) {
		echo '{"status":"mysqlError","code":"'.$e->getMessage().'"}';
		exit();
	}

	//Recorremos los datos recibidos buscando una coincidencia.
	//Se resuelve una por una las DNS para obtener la IP, luego se coteja con la de la base de datos
	//Si hay coincidencia se guarda el ID del cliente en $matchId
	foreach ($datos as $dato) {
		$resolved_ip = gethostbyname($dato["dns"]);
		if($outsider_ip === $resolved_ip){
			$matchId = $dato["id_cliente"];
		}
	}

	return $matchId;
}
?>