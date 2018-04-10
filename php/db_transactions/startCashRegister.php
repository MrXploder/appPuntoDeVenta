<?php
// Headers HTML para prevenir que el navegador guarde en caché el contenido de la pagina
Header('Content-type: text/javascript');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
// Notificar solamente errores de ejecución
error_reporting(E_ERROR);

require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/meekrodb.class.php';

//Recuperamos el mensaje JSON del cuerpo de la solicitud (POST)
$postdata = file_get_contents("php://input");
//Si hay algo, seguimos.
if(!empty($postdata)){
	//Transformamos la request a un Array Asociativo de PHP
	$request = json_decode($postdata, true);
	try{
		DB::insert('cr_status', array(
			"since"	 		 => date("d/m/Y H:m:s"),
			"open"			 => 1,
			"start_cash" => $request["start_cash"]
		));
		$payLoad["status"] = "success";
	//Buscar si hay una sesion abierta
		$currentSession = DB::queryFirstRow("SELECT * FROM `cr_status` WHERE `open` = 1 LIMIT 1");
		if(empty($currentSession)){
			$payLoad["cashRegister"] = array("open" => false);
		}
		else{
			$currentSession["open"] = (bool)$currentSession["open"];
			$payLoad["cashRegister"] = $currentSession;
		}
		echo json_encode($payLoad, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
	}
	catch(MeekroDBException $e){
		echo json_encode(array("status" => "mysqlError", "code" => $e->getMessage()), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
	}
}
?>