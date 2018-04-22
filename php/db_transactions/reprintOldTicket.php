<?php
// Headers HTML para prevenir que el navegador guarde en caché el contenido de la pagina
Header('Content-type: text/javascript');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
// Notificar solamente errores de ejecución
error_reporting(E_ERROR);
require $_SERVER['DOCUMENT_ROOT'].'/autoload.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/functions/sanitizeInput.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/generalSettings.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/meekrodb.class.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\CapabilityProfile;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

if($modeControll === 'dev'){
	$connector = new FilePrintConnector("reprintOldTicket.txt");
}
else{
	$connector = new WindowsPrintConnector("POS");
}
$printer   = new Printer($connector);

//Recuperamos el mensaje JSON del cuerpo de la solicitud (POST)
$ticketIdToReprint = sanitizeInput($_GET["id"]);
//Si hay algo, seguimos.
if(!empty($ticketIdToReprint)){
	try{
		$ticket										  = DB::query("SELECT * FROM `ticket_data_log` WHERE `id` = %d LIMIT 1", $ticketIdToReprint)[0];
		$ticket["listaDeProductos"] = DB::query("SELECT * FROM `ticket_detail_log` WHERE `id_ticketdata` = %d", $ticketIdToReprint);

		$printer -> initialize();
		$printer -> text("Fecha: ".date("d/m/Y", strtotime($ticket["date"]))."      Hora: ".date("H:i:s", strtotime($ticket["date"])));
		$printer -> feed();
		$printer -> feed();
		$printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
		$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
		$printer -> text("         NP: ".$ticketIdToReprint);
		$printer -> initialize();
		$printer -> feed();
		$printer -> text("--------------------------------------------");
		$printer -> feed();
		$printer -> text("CANTIDAD         DESCRIPCION          PRECIO");
		$printer -> feed();
		$printer -> text("--------------------------------------------");
		$printer -> feed();
		
		$printer -> selectPrintMode(Printer::MODE_DOUBLE_HEIGHT);
		$printer -> selectPrintMode(Printer::MODE_DOUBLE_WIDTH);
		foreach($ticket["listaDeProductos"] as $item){

			$printer -> text("${item['cant']}     ${item['nom_prod']}     $${item['prec']}");
			$printer -> feed();
		}
		$printer -> initialize();
		unset($item);

		$printer -> feed();
		$printer -> feed();
		$printer -> text("              TOTAL: $".$ticket["total"]);
		$printer -> feed();
		$printer -> text("SU PAGO EN EFECTIVO: $".$ticket["cashPay"]);
		$printer -> feed();
		$printer -> text("SU VUELTO:           $".($ticket["cashPay"] - $ticket["total"]));
		$printer -> feed();
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