# appPuntoDeVenta
Esta es una aplicacion basica de punto de venta que usa una impresora POS Epson (o compatibles)

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
