﻿<?php
// Headers HTML para prevenir que el navegador guarde en caché el contenido de la pagina
Header('Content-type: text/javascript');
Header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
Header("Cache-Control: post-check=0, pre-check=0", false);
Header("Pragma: no-cache");
// Notificar solamente errores de ejecución
error_reporting(E_ERROR);

require $_SERVER['DOCUMENT_ROOT'].'php/functions/sanitizeInput.php';
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/meekrodb.class.php';
require $_SERVER['DOCUMENT_ROOT'].'/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\FilePrintConnector;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;

if($modeControll === 'dev'){
	$connector = new FilePrintConnector("printSalesSummary.txt");
}
else{
	$connector = new WindowsPrintConnector("POS");
}
$printer   = new Printer($connector);

$sinceAngularDate = sanitizeInput($_GET["since"]);
$tillAngularDate  = sanitizeInput($_GET["till"]);

$sinceMysqlFormat = str_replace("-", "/", $sinceAngularDate);
$tillMysqlFormat  = str_replace("-", "/", $tillAngularDate);

$startId = 0;
$endId = 0;

$data = DB::query("SELECT `sess_id`, `since` FROM `cr_status` WHERE `open` = 0 ORDER BY `sess_id` DESC");
foreach ($data as $item){
	if($sinceMysqlFormat == substr($item["since"], 0, 10)){
		$startId = $item["sess_id"];
	}
	if($tillMysqlFormat >= substr($item["since"], 0, 10)){
		$endId = $item["sess_id"];
	}
}
$listadoDeProductos = DB::query("SELECT `id`, `nom_prod` FROM `products`");

for($x = $startId; $x <= $endId; $x++){
	$listadoDeVentas = DB::query("SELECT `id` FROM `ticket_data_log` WHERE `id_crstatus` = %d", $x);
	foreach ($listadoDeVentas as $detalle){
		$listadoDetalles = DB::query("SELECT `nom_prod`, `cant`, `prec` FROM `ticket_detail_log` WHERE `id_ticketdata` = %d", $detalle["id"]);
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
$printer -> text("FECHA: ".date("d-m-Y")."     HORA: ".date("H:m:s"));
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

?>