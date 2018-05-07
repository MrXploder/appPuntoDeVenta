<?php
// Headers HTML para prevenir que el navegador guarde en caché el contenido de la pagina
Header('Content-type: text/javascript');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
// Notificar solamente errores de ejecución
error_reporting(E_ERROR);

require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/generalSettings.php';

/******************************************************************************/
/******THIS PIECE OF CODE (FROM ECP/POS LIBRARY) CANNOT BE INCLUDED************/
/******FROM A EXTERNAL FILE, SO YOU HAVE TO COPY/PASTE WHENEVER YOU************/
/******NEED IT*****************************************************************/
/******************************************************************************/
require $_SERVER['DOCUMENT_ROOT'].'/autoload.php';													/**/	
use Mike42\Escpos\Printer;																									/**/
use Mike42\Escpos\CapabilityProfile;																				/**/
use Mike42\Escpos\PrintConnectors\FilePrintConnector;												/**/
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;										/**/	
if($modeControll === 'dev') $connector = new FilePrintConnector("POS.txt");	/**/
else $connector = new WindowsPrintConnector("POS");													/**/
$printer = new Printer($connector);																					/**/
/******************************************************************************/
/******************************************************************************/
/******************************************************************************/

//Recuperamos el mensaje JSON del cuerpo de la solicitud (POST)
$postdata = file_get_contents("php://input");
//Si hay algo, seguimos.
if(!empty($postdata)){
	//Transformamos la request a un Array Asociativo de PHP
	$request = json_decode($postdata, true);
	try{
		$database->update("cr_status", [
			"till"	 		   => date("d/m/Y H:i:s"),
			"open"			   => 0,
			"end_cash_20k" => $request["end_cash_20k"],
			"end_cash_10k" => $request["end_cash_10k"],
			"end_cash_5k"  => $request["end_cash_5k"],
			"end_cash_2k"  => $request["end_cash_2k"],
			"end_cash_1k"  => $request["end_cash_1k"],
			"end_cash_500" => $request["end_cash_500"],
			"end_cash_100" => $request["end_cash_100"],
			"end_cash_50"  => $request["end_cash_50"],
			"end_cash_10"  => $request["end_cash_10"]
		], ["cr_status.sess_id" => $request["sess_id"]]);

		$payLoad["status"] = "success";

		$currentSession = $database->select("cr_status", "*", ["open" => 1, "LIMIT" => 1])[0];
		if(empty($currentSession)){
			$payLoad["cashRegister"] = array("open" => false);
		}
		else{
			$currentSession["open"] = (bool)$currentSession["open"];
			$payLoad["cashRegister"] = $currentSession;
		}
		
		$crIdToReprint   = $request["sess_id"];
		$crData					 = $database->select("cr_status", "*", ["cr_status.sess_id" => $crIdToReprint])[0];
		$ticketData 		 = $database->select("ticket_data_log", "*", ["ticket_data_log.id_crstatus" => $crIdToReprint]);
		$ticketQuantity  = count($ticketData);
		$summation 			 = 0;
		$profits 				 = 0;
		$calculatedEarns = 0;
		
		$summation += $crData["end_cash_20k"] * 20000;
		$summation += $crData["end_cash_10k"] * 10000;
		$summation += $crData["end_cash_5k"]  * 5000;
		$summation += $crData["end_cash_2k"]  * 2000;
		$summation += $crData["end_cash_1k"]  * 1000;
		$summation += $crData["end_cash_500"] * 500;
		$summation += $crData["end_cash_100"] * 100;
		$summation += $crData["end_cash_50"]  * 50;
		$summation += $crData["end_cash_10"]  * 10;
		$profits = $summation - $crData["start_cash"];
		foreach ($ticketData as $data){
			$calculatedEarns += $data["total"];
		}
		if(($calculatedEarns - ($summation - $crData["start_cash"])) < 0){
			$crCheck = "LA CAJA NO CUADRA\nSEGUN MIS CALCULOS EL EFECTIVO INGRESADO\nAL CIERRE DE CAJA ES MENOR O MAYOR QUE\nEL CALCULADO POR LAS VENTAS";
		}
		else{
			$crCheck = "LA CAJA CUADRA PERFECTAMENTE";
		}

			//PRINTER ROUTINE///
		$printer -> initialize();
		$printer -> setJustification(Printer::JUSTIFY_CENTER);
		$printer -> text("RESUMEN DE CAJA");
		$printer -> feed();
		$printer -> text("NUMERO DE SESION: #".$crIdToReprint);
		$printer -> initialize();
		$printer -> feed();
		$printer -> feed();
		$printer -> text("FECHA APERTURA CAJA: ".$crData["since"]);
		$printer -> feed();
		$printer -> text("FECHA CIERRE CAJA:   ".$crData["till"]);
		$printer -> feed();
		$printer -> text("EFECTIVO DE APERTURA: $".$crData["start_cash"]);
		$printer -> feed();
		$printer -> text("::::DETALLE EFECTIVO DE CIERRE::::::");
		$printer -> feed();
		$printer -> text("DENOMINACIÓN        CANTIDAD");
		$printer -> feed();
		$printer -> text("$20.000            ".$crData["end_cash_20k"]);
		$printer -> feed();
		$printer -> text("$10.000            ".$crData["end_cash_10k"]);
		$printer -> feed();
		$printer -> text("$5.000             ".$crData["end_cash_5k"]);
		$printer -> feed();
		$printer -> text("$2.000             ".$crData["end_cash_2k"]);
		$printer -> feed();
		$printer -> text("$1.000             ".$crData["end_cash_1k"]);
		$printer -> feed();
		$printer -> text("$500               ".$crData["end_cash_500"]);
		$printer -> feed();
		$printer -> text("$100               ".$crData["end_cash_100"]);
		$printer -> feed();
		$printer -> text("$50                ".$crData["end_cash_50"]);
		$printer -> feed();
		$printer -> text("$10                ".$crData["end_cash_10"]);
		$printer -> feed();
		$printer -> text("TOTAL DE CIERRE DE CAJA: $".$summation);
		$printer -> feed();
		$printer -> text("GANANCIA DEL TURNO: $".$profits);
		$printer -> feed();
		$printer -> text("CANTIDAD DE VENTAS GENERADAS: ".$ticketQuantity);
		$printer -> feed();
		$printer -> text("GANANCIA DEL TURNO (RECOMPROBADA): $".$calculatedEarns);
		$printer -> feed();
		$printer -> text("NOTA: ".$crCheck);
		$printer -> feed();
		$printer -> cut(Printer::CUT_FULL, 1);
		$printer -> close();
	}
	catch(Exception $e){
		$payLoad["status"] = "sqlError";
	}
	finally{
		echo json_encode($payLoad, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
	}
}
?>