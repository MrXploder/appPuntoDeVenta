<?php
require $_SERVER['DOCUMENT_ROOT'].'/php/dependencies/meekrodb.class.php';

try{
	DB::query("CREATE DATABASE IF NOT EXISTS `puntodeventa_db` DEFAULT CHARSET=utf8 COLLATE utf8_spanish_ci;");
	
	//MYSQL
	DB::query("CREATE TABLE IF NOT EXISTS `cr_status` (
		`sess_id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`since` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
		`till` varchar(50) COLLATE utf8_spanish_ci DEFAULT NULL,
		`open` tinyint(1) NOT NULL,
		`start_cash` int(10) NOT NULL,
		`end_cash_20k` int(11) NOT NULL DEFAULT '0',
		`end_cash_10k` int(11) NOT NULL DEFAULT '0',
		`end_cash_5k` int(11) NOT NULL DEFAULT '0',
		`end_cash_2k` int(11) NOT NULL DEFAULT '0',
		`end_cash_1k` int(11) NOT NULL DEFAULT '0',
		`end_cash_500` int(11) NOT NULL DEFAULT '0',
		`end_cash_100` int(11) NOT NULL DEFAULT '0',
		`end_cash_50` int(11) NOT NULL DEFAULT '0',
		`end_cash_10` int(11) NOT NULL DEFAULT '0'
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;");

	/*sqlite3
	CREATE TABLE IF NOT EXISTS `cr_status` (
		`sess_id` INTEGER PRIMARY KEY AUTOINCREMENT,
		`since` varchar(50) NOT NULL,
		`till` varchar(50) DEFAULT NULL,
		`open` tinyint(1) NOT NULL,
		`start_cash` int(10) NOT NULL,
		`end_cash_20k` int(11) NOT NULL DEFAULT '0',
		`end_cash_10k` int(11) NOT NULL DEFAULT '0',
		`end_cash_5k` int(11) NOT NULL DEFAULT '0',
		`end_cash_2k` int(11) NOT NULL DEFAULT '0',
		`end_cash_1k` int(11) NOT NULL DEFAULT '0',
		`end_cash_500` int(11) NOT NULL DEFAULT '0',
		`end_cash_100` int(11) NOT NULL DEFAULT '0',
		`end_cash_50` int(11) NOT NULL DEFAULT '0',
		`end_cash_10` int(11) NOT NULL DEFAULT '0'
	);
	*/
	//mysql
	DB::query("CREATE TABLE IF NOT EXISTS `products` (
		`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`nom_prod` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
		`cant_1` int(11) NOT NULL,
		`cant_2` int(11) NOT NULL,
		`cant_3` int(11) NOT NULL,
		`cant_4` int(11) NOT NULL,
		`cant_5` int(11) NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;");

	/*sqlite3
CREATE TABLE IF NOT EXISTS `products` (
		`id` INTEGER PRIMARY KEY AUTOINCREMENT,
		`nom_prod` varchar(50) NOT NULL,
		`cant_1` int(11) NOT NULL,
		`cant_2` int(11) NOT NULL,
		`cant_3` int(11) NOT NULL,
		`cant_4` int(11) NOT NULL,
		`cant_5` int(11) NOT NULL
	)
	*/
	//mysql
	DB::query("CREATE TABLE IF NOT EXISTS `ticket_data_log` (
		`id` int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`id_crstatus` int(10) NOT NULL,
		`date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`cashPay` int(10) NOT NULL,
		`total` int(10) NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;");

	/*sqlite3
	CREATE TABLE IF NOT EXISTS `ticket_data_log` (
		`id` INTEGER PRIMARY KEY AUTOINCREMENT,
		`id_crstatus` int(10) NOT NULL,
		`date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`cashPay` int(10) NOT NULL,
		`total` int(10) NOT NULL
	)
	*/

	//mysql
	DB::query("CREATE TABLE IF NOT EXISTS `ticket_detail_log` (
		`id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
		`id_ticketdata` int(11) NOT NULL,
		`nom_prod` varchar(50) COLLATE utf8_spanish_ci NOT NULL,
		`cant` int(11) NOT NULL,
		`prec` int(11) NOT NULL
	) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;");
	
	/*sqlite3
	CREATE TABLE IF NOT EXISTS `ticket_detail_log` (
		`id` INTEGER PRIMARY KEY AUTOINCREMENT,
		`id_ticketdata` int(11) NOT NULL,
		`nom_prod` varchar(50) NOT NULL,
		`cant` int(11) NOT NULL,
		`prec` int(11) NOT NULL
	)
	*/
}
catch(MeekroDBException $e){
	echo $e->getMessage();
	exit();
}
?>