<?php
// Headers HTML para prevenir que el navegador guarde en caché el contenido de la pagina
Header('Content-type: text/javascript');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
// Notificar solamente errores de ejecución
error_reporting(E_ERROR);

require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/meekrodb.class.php';

try{
	$payLoad["cashRegisterList"] = DB::query("SELECT * FROM `cr_status` WHERE `open` = 0 ORDER BY `sess_id` DESC");
	$payLoad["status"]  = "success";
}
catch(MeekroDBException $e){
	$payLoad["status"] = "mysqlError";
	$payLoad["code"]   = $e->getMessage();
	$payLoad["query"]  = $e->getQuery();
}
finally{
	echo json_encode($payLoad, JSON_UNESCAPED_UNICODE | JSON_NUMERIC_CHECK);
}
?>