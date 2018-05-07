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
	$payLoad["products"] = $database->select("products", "*");
	for($i = 0; $i < count($payLoad["products"]); $i++){
		$payLoad["products"][$i]["choosenCantidad"]   = null;
	}
	$payLoad["status"] = "success";
}
catch(Exception $e){
	$payLoad["status"] = "mysqlError";
}
finally{
	echo json_encode($payLoad, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
}
?>