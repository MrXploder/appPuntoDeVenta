<?php
////INCLUDES//////
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/Medoo.php';
use Medoo\Medoo;

///TIMEZONE///
date_default_timezone_set('America/Santiago');

///GLOBAL VARS///
$modeControll = "prod"; 			//dev = development; prod = production;
$versionControll = rand(); 	//change this to a fixed number on production
$payLoad = [];							//output payload

///DB CONNECTION CONFIGURATION///
$database = new Medoo([
	'database_type' => 'sqlite',
	'database_file' => $_SERVER['DOCUMENT_ROOT'].'/puntodeventa.db'
]);
?>