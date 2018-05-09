# appPuntoDeVenta
Esta es una aplicacion basica de punto de venta que usa una impresora POS Epson (o compatibles)
Esta aplicacion se hizo para un cliente que no queria una interfaz grafica elegante, sino que queria que las operaciones de venta fuesen lo mas rapido posible y solo con el teclado. Fue un gran desafio ya que yo solo manejo desarrollo web. Los codigos de los productos son los numeros del teclado para que sean faciles de aprender, por ejemplo: bebida gaseosa codigo "1". De esta forma cuando escribes en el programa te aparece de inmediato el producto a insertar en la boleta. El programa tiene dos cosas que no me gustan: 1) Las cantidades estan en duro, por lo que solo se pueden tener hasta 5 cantidades de cada producto, esto se hizo asi para considerar las promociones de 2x o 3x, El problema es que al crear un nuevo producto se deben poner los 5 precios disponibles (1x, 2x... 5x). 2) La libreria de la impresora tiene muy pocas opciones, de hecho no es realmente un conector, lo que hace simplemente es "copiar" un texto a la direccion compartida de la impresora (en Windows), por lo que se debe compartir la impresora en Windows para que esta tenga una direccion local y el programa pueda copiar alli el texto generado. Este es un problema ya que requiere una configuracion por parte del usuario, que muchas veces termina en dolores de cabeza. 

#PROCEDIMIENTO:

#PASO 1: 

#PASO 2: CARGAR LOS ARCHIVOS EN APACHE (U OTRO SERVIDOR)

Esta app usa PHP por lo que esta pensada para usarse con APACHE y PHP.
Como esta aplicacion usa una libreria de PHP para conectarse a la impresora, todo el proyecto debe estar en el mismo computador al cual está conectada la impresora. Este proyecto en su forma actual NO brinda ninguna clase de soporte a hostearse en un servidor web.
Se recomienda instalar WAMP u otro servidor web en Windows.

#PASO 3: CONFIGURAR LA IMPRESORA
1. Se debe ir al panel de Windows "Agregar impresora" y agregar la impresora a la lista de impresoras de windows
2. Se debe ir a "configurar impresora" y cambiarle el nombre a: `POS`
3. Luego se debe compartir la impresora en la red bajo el mismo nombre (`POS`)

#PASO 4: CONFIGURAR MODO PRODUCCION

El proyecto tiene un archivo PHP llamado `generalSettings.php` ubicado en `../php/dependencies/generalSettings.php`, en el podras encontrar dos variables:
1. la variable `$modeControll`, esta puede tener 2 valores: "DEV" o "PROD": 
->1.1 Si es "DEV", todas las impresiones se haran a un archivo de texto. 
->1.2 Si es "PROD", todas las impresiones se harán a la impresora compartida con nombre "POS".

2. La variable `$versionControll` que es para mantener un versionado de la aplicacion (recomiendo dejarlo en `rand()` si no se sabe lo que se hace.

#ABOUT

Cualquier duda me hablan por correo.
Tambien acepto sugerencias. cheers!
