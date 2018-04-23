# appPuntoDeVenta
Esta es una aplicacion basica de punto de venta que usa una impresora POS Epson (o compatibles)

#PROCEDIMIENTO:
#PASO 1: CONFIGURAR MYSQL
Valores por defecto de la base de datos:

1. Nombre de la base de datos: `puntodeventa_db`
2. Nombre de usuario de mysql: `root`
3. Contraseña de mysql:        `root`

Si se desea cambiar estas configuraciones se debe editar la libreria `MeekroDB` ubicada en `../php/dependencies/meekrodb.class.php`

#PASO 2: CARGAR LOS ARCHIVOS EN APACHE (U OTRO SERVIDOR)
Esta app usa PHP por lo que esta pensada para usarse con APACHE y PHP.

#PASO 3: CONFIGURAR LA IMPRESORA
1. Se debe ir al panel de Windows "Agregar impresora" y agregar la impresora a la lista de impresoras de windows
2. Se debe ir a "configurar impresora" y cambiarle el nombre a: `POS`
3. Luego se debe compartir la impresora en la red bajo el mismo nombre (`POS`)

#PASO 4: CONFIGURAR MODO PRODUCCION

El proyecto tiene un archivo PHP llamado `generalSettings.php` ubicado en `../php/dependencies/generalSettings.php`, en el podras encontrar dos variables:
1. la variable `$modeControll`, esta puede tener 2 valores: "DEV" o "PROD": a) Si es "DEV", todas las impresiones se haran a un archivo de texto. b) Si es "PROD", todas las impresiones se harán a la impresora compartida con nombre "POS".
2. La variable `$versionControll` que es para mantener un versionado de la aplicacion (recomiendo dejarlo en `rand()` si no se sabe lo que se hace.

#ABOUT
Cualquier duda me hablan por correo para resolver dudas.
