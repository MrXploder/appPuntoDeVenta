<?php
Header('Content-type: text/javascript');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
error_reporting(E_ERROR);

require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/generalSettings.php';

//Recuperamos el mensaje JSON del cuerpo de la solicitud (POST)
$postData = file_get_contents("php://input");
//Si hay algo, seguimos.
if(!empty($postData)){
	$request = json_decode($postData, true);
	try{
		$foundedProduct = $database->select("products", "id", ["id" => $request["id"]])[0];
		if(empty($foundedProduct)){
			$database->insert("products", [
				"id"       => $request["id"],
				"nom_prod" => $request["nom_prod"],
				"cant_1"   => $request["cant_1"],
				"cant_2"   => $request["cant_2"],
				"cant_3"   => $request["cant_3"],
				"cant_4"   => $request["cant_4"],
				"cant_5"   => $request["cant_5"]
			]);
		}
		else{
			$database->update("products", [
				"id"       => $request["id"],
				"nom_prod" => $request["nom_prod"],
				"cant_1"   => $request["cant_1"],
				"cant_2"   => $request["cant_2"],
				"cant_3"   => $request["cant_3"],
				"cant_4"   => $request["cant_4"],
				"cant_5"   => $request["cant_5"]
			], ["products.id" => $request["id"]]);
		}
		$payLoad["status"] = "success";
	}
	catch(Exception $e){
		$payLoad["status"] = "sqlError";
	}
	finally{
		echo json_encode($payLoad);
	}
}
?>