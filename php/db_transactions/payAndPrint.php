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
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/escpos.autoload.php';		/**/	
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

$postdata = file_get_contents("php://input");
if(!empty($postdata)){
	$request = json_decode($postdata, true);

	try{
		$printer->initialize();
		$printer->text("Fecha: ".date("d/m/Y")."      Hora: ".date("H:i:s"));
		
		$cashRegisterSessId = $database->select("cr_status", "sess_id", ["cr_status.open" => 1])[0];
		
		$printer->feed();
		$printer->feed();
		
		$database->insert("ticket_data_log", [
			"id_crstatus" => $cashRegisterSessId,
			"date"				=> date("Y-m-d H:i:s"),
			"cashPay"			=> $request["pagoEfectivo"],
			"total"				=> $request["totalBoleta"]
		]);

		$ticketDataLogId = $database->id();
		
		$printer->text("NOTA DE PEDIDO           NP: ".$ticketDataLogId);
		$printer -> feed();
		$printer -> text("--------------------------------------------");
		$printer -> feed();
		$printer -> text("CANTIDAD         DESCRIPCION          PRECIO");
		$printer -> feed();
		$printer -> text("--------------------------------------------");
		$printer -> feed();
		
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
			$database->insert("ticket_detail_log", [
				"id_ticketdata"	=> $ticketDataLogId,
				"nom_prod"		  => $item["nom_prod"],
				"cant"				  => $item["choosenCantidad"],
				"prec"				  => $prec
			]);

			$printer -> text("${item['choosenCantidad']}     ${item['nom_prod']}     $${prec}");
			$printer -> feed();
		}
		unset($item);

		$printer -> feed();
		$printer -> feed();
		$printer -> text("              TOTAL: $".$request["totalBoleta"]);
		$printer -> feed();
		$printer -> text("SU PAGO EN EFECTIVO: $".$request["pagoEfectivo"]);
		$printer -> feed();
		$printer -> text("SU VUELTO:           $".($request["pagoEfectivo"] - $request["totalBoleta"]));         
		$printer -> feed();
		$printer -> feed();
		$printer -> feed();         
		$printer -> cut(Printer::CUT_FULL, 1);
		$printer -> close();
		$payLoad["status"] = "success";
	}
	catch(Exception $e){
		$payLoad["status"] = "sqlError";
	}
	finally{
		echo json_encode($payLoad, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
	}
}
?>