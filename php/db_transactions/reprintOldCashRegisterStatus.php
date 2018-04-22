<?php
// Headers HTML para prevenir que el navegador guarde en caché el contenido de la pagina
Header('Content-type: text/javascript');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
// Notificar solamente errores de ejecución
error_reporting(E_ERROR);
require $_SERVER['DOCUMENT_ROOT'].'/php/functions/versionControll.php';
require $_SERVER['DOCUMENT_ROOT'].'php/functions/sanitizeInput.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/meekrodb.class.php';
require $_SERVER['DOCUMENT_ROOT'].'/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

if($modeControll === 'dev'){
	$connector = new FilePrintConnector("reprintOldCashRegisterStatus.txt");
}
else{
	$connector = new WindowsPrintConnector("POS");
}
$printer   = new Printer($connector);

//Recuperamos el mensaje JSON del cuerpo de la solicitud (POST)
$crIdToReprint = sanitizeInput($_GET["id"]);
//Si hay algo, seguimos.
if(!empty($crIdToReprint)){
	try{
		$crData         = DB::queryFirstRow("SELECT * FROM `cr_status` WHERE `sess_id` = %d", $crIdToReprint);
		$ticketData     = DB::query("SELECT * FROM `ticket_data_log` WHERE `id_crstatus` = %d", $crIdToReprint); 
		$ticketQuantity = DB::count();
		$summation = 0;
		$profits = 0;
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
		echo json_encode(array("status" => "success"));
	}
	catch(MeekroDBException $e){
		echo '{"status":"mysqlError", "code":"'.$e->getMessage().'"}';
	}
}
?>