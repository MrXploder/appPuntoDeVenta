<?php
date_default_timezone_set('America/Santiago');
$modeControll = "dev";
$versionControll = rand();

	/*
	version alert!{
	 - Ahora se migró todo el proyecto a github por lo que ahi se mantendrá un control de versión
	 - De igual manera se deberá crear una nueva version para producción al pasar de DEV a PROD para evitar el cacheo de archivos
	 - Esas versiones deberan ir con la fecha de entrega de PROD al cliente. ejemplo: "version 1.20 { 10-10-2018 }"
	}
	version rand(){
		- is for development to prevent browser cache the files
	}
	version 1.12 - {
		- ahora al pasar el mouse sobre la descripcion de una tarea se puede leer completa sin abrir el modal.
		-
	}
	version 1.10 - 31.01.2018{
		- Corregidos los bugs introducidos en 1.9 al no poder loguearse como Admin
		- Agregadas nuevas opciones de filtro en Administrar Tareas
	} 
	version 1.9 - 29.01.2018{
		- corregido el error al mostrar caracteres especiales (ñ y tildes)
	}
	version 1.8 - 29.01.2018{
		- El reporte de facturación ahora se ordena por fecha de ejecucion: mas reciente a menos reciente descendiente
	}
	version 1.7 - 29.01.2018{
		- Cambiado el estilo de las tablas
		- Para editar un ticket, proyecto, etc se debe hacer click en el boton azul en la primera columna
		- Agregado el boton eliminar ticket, proyecto, etc.
		- Reescrito app.js
	}
	version 1.6 - 24.01.2018{
	}
	*/
	?>