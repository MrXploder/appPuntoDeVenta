<?php
// Headers HTML para prevenir que el navegador guarde en caché el contenido de la pagina
Header('Content-type: text/javascript');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
// Notificar solamente errores de ejecución
error_reporting(E_ERROR);

require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/generalSettings.php';

try{
	$payLoad["tickets"] = $database->select("ticket_data_log", "*", ["ORDER" => ["ticket_data_log.id" => "DESC"]]);
	for($i = 0; $i < count($payLoad["tickets"]); $i++){
		$payLoad["tickets"][$i]["detail"] = $database->select("ticket_detail_log", "*", ["id_ticketdata" => $payLoad["tickets"][$i]["id"]]);
	}
	$payLoad["status"] = "success";
}
catch(Exception $e){
	$payLoad["status"] = "sqlError";
}
finally{
	echo json_encode($payLoad, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
}
?>