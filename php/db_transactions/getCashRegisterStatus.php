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
	$currentSession = $database->select("cr_status", "*", ["open"  => 1,"LIMIT" => 1])[0];
	if(empty($currentSession)){
		$payLoad["cashRegister"] = array("open" => false);
	}
	else{
		$currentSession["open"] = (bool)$currentSession["open"];
		$payLoad["cashRegister"] = $currentSession;
	}
	$payLoad["status"] = "success";
}
catch(Exception $e){
	$payLoad["status"] = "sqlError";
}
finally{
	echo json_encode($payLoad, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
}
?>
