<?php
// Headers HTML para prevenir que el navegador guarde en caché el contenido de la pagina
Header('Content-type: text/javascript');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
// Notificar solamente errores de ejecución
error_reporting(E_ERROR);

require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/meekrodb.class.php';
require $_SERVER['DOCUMENT_ROOT'].'/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

//$connector = new WindowsPrintConnector("pos");
$connector = new FilePrintConnector("payAndPrint.txt");
$printer   = new Printer($connector);

//Recuperamos el mensaje JSON del cuerpo de la solicitud (POST)
$postdata = file_get_contents("php://input");
//Si hay algo, seguimos.
if(!empty($postdata)){
	//Transformamos la request a un Array Asociativo de PHP
	$request = json_decode($postdata, true);

	//Primero debemos obtener el ID del status de la caja actual.
	try{
		$printer -> initialize();
		$printer -> text("Fecha: ".date("d/m/Y")."      Hora: ".date("H:m:s"));
		
		$cashRegisterSessId = DB::queryFirstField("SELECT `sess_id` FROM `cr_status` WHERE `open` = 1");
		
		$printer -> text("\n\n");
		
		DB::insert('ticket_data_log', array(
			"id_crstatus" => $cashRegisterSessId,
			"cashPay"			=> $request["pagoEfectivo"],
			"total"				=> $request["totalBoleta"]
		));

		$ticketDataLogId = DB::insertId();
		
		$printer -> text("NOTA DE PEDIDO           NP: ".$ticketDataLogId);
		$printer -> text("\n--------------------------");
		$printer -> text("\nCANT    DESC     PREC");
		$printer -> text("\n--------------------------\n");
		
		foreach($request["listaDeProductos"] as $item){
			switch($item["choosenCantidad"]){
				case "1": $prec = $item["cant_1"];
				break;

				case "2": $prec = $item["cant_2"];
				break;

				case "3": $prec = $item["cant_3"];
				break;

				case "4": $prec = $item["cant_4"];
				break;

				case "5": $prec = $item["cant_5"];
				break;
			}
			DB::insert('ticket_detail_log', array(
				"id_ticketdata"	=> $ticketDataLogId,
				"nom_prod"		  => $item["nom_prod"],
				"cant"				  => $item["choosenCantidad"],
				"prec"				  => $prec
			));
			$printer -> text($item["choosenCantidad"]."   ".$item["nom_prod"]."  $".$prec);
			$printer -> feed();
		}
		unset($item);

		$printer -> text("\n\n");
		$printer -> text("              TOTAL: ".$request["totalBoleta"]."\n");
		$printer -> text("SU PAGO EN EFECTIVO: ".$request["pagoEfectivo"]."\n");         
		$printer -> cut();
		$printer -> pulse();
		$printer -> close();
		echo '{"status":"success"}';
	}
	catch(MeekroDBException $e){
		echo '{"status":"mysqlError", "code":"'.$e->getMessage().'"}';
	}
}
?>