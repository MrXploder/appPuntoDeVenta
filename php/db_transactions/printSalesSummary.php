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

$sinceAngularDate = $_GET["since"];
$tillAngularDate  = $_GET["till"];

$sinceMysqlFormat = date($sinceAngularDate); 
$tillMysqlFormat  = date($tillAngularDate);

$startId = 0;
$endId = 0;
try{
	$data = $database->select("cr_status", "*", ["cr_status.open" => 0, "ORDER" => ["cr_status.sess_id" => "DESC"]]);
	foreach ($data as $item){
		$loopDate = date("d-m-Y", strtotime(str_replace("/", "-", $item["since"])));

		if($sinceMysqlFormat == $loopDate){
			$startId = $item["sess_id"];
		}
		if($tillMysqlFormat == $loopDate){
			$endId = $item["sess_id"];
		}
	}
	if($endId == 0) $endId = $startId;

	if(empty($_GET["key"])){
		$listadoDeProductos = $database->select("products", "*");
	}
	else{
		$listadoDeProductos = $database->select("products", "*", ["products.nom_prod" => $_GET["key"]]);
	}
	for($x = $startId; $x <= $endId; $x++){
		$listadoDeVentas = $database->select("ticket_data_log", "*", ["ticket_data_log.id_crstatus" => $x]);
		foreach ($listadoDeVentas as $detalle){
			$listadoDetalles = $database->select("ticket_detail_log", "*", ["ticket_detail_log.id_ticketdata" => $detalle["id"]]);
			foreach ($listadoDetalles as $item){
				foreach ($listadoDeProductos as $producto){
					if($producto["nom_prod"] == $item["nom_prod"]){
						$toPrintList[$producto["nom_prod"]]["key"] = $producto["nom_prod"];
						$toPrintList[$producto["nom_prod"]]["cant"] += $item["cant"];
						$toPrintList[$producto["nom_prod"]]["prec"] += $item["prec"];
					}
				}
			}
		}
	}

	$printer -> initialize();
	$printer -> text("FECHA: ".date("d-m-Y")."     HORA: ".date("H:i:s"));
	$printer -> feed();
	$printer -> text("-------------------------------------");
	$printer -> feed();
	$printer -> text("         RESUMEN DE VENTAS           ");
	$printer -> feed();
	$printer -> text("-------------------------------------");
	$printer -> feed();
	$printer -> text("DESDE: ".$sinceMysqlFormat);
	$printer -> feed();
	$printer -> text("HASTA: ".$tillMysqlFormat);
	$printer -> feed();
	$printer -> text("-------------------------------------");
	$printer -> feed();
	$printer -> text("PRODUCTO         CANT           TOTAL");
	$printer -> feed();
	$printer -> text("-------------------------------------");
	$printer -> feed();
	foreach ($toPrintList as $selection){
		$mainTotal += $selection["prec"];
		$printer -> text($selection["key"]."       ".$selection["cant"]."         ".$selection["prec"]);
		$printer -> feed();
	}
	$printer -> feed();
	$printer -> text("TOTAL VENTAS: $".$mainTotal);
	$printer -> feed();
	$printer -> feed();
	$printer -> cut(Printer::CUT_FULL, 1);
	$printer -> close();
	$payLoad["status"] = "success";
}
catch(Exception $e){
	$payLoad["status"] = "error";
}
finally{
	echo json_encode($payLoad, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
}
?>