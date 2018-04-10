<?php
/* Call this file 'hello-world.php' */



$connector = new WindowsPrintConnector("brother");
$printer = new Printer($connector);

/* Text */
$printer -> text("Hello world");
$printer -> close();

?>