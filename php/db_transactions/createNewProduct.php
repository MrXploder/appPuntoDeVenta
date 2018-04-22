<?php
Header('Content-type: text/javascript');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
error_reporting(E_ERROR);

require $_SERVER['DOCUMENT_ROOT'].'/php/functions/sanitizeInput.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/generalSettings.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/meekrodb.class.php';

//Recuperamos el mensaje JSON del cuerpo de la solicitud (POST)
$postData = file_get_contents("php://input");
//Si hay algo, seguimos.
if(!empty($postData)){
	$request = json_decode($postData, true);
	try{
		DB::replace('products', array(
			"id"       => $request["id"],
			"nom_prod" => $request["nom_prod"],
			"cant_1"   => $request["cant_1"],
			"cant_2"   => $request["cant_2"],
			"cant_3"   => $request["cant_3"],
			"cant_4"   => $request["cant_4"],
			"cant_5"   => $request["cant_5"],
		));
		$payLoad["status"] = "success";
	}
	catch(MeekroDBException $e){
		$payLoad["status"] = "mysqlError";
		$payLoad["code"]   = $e->getMessage();
		$payLoad["query"]  = $e->getQuery();
	}
	catch(Exception $e){
		$payLoad["status"] = "unknownError";
		$payLoad["code"]   = $e->getMessage();
	}
	finally{
		echo json_encode($payLoad);
	}
}
?>