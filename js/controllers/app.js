/* by MrXploder */
appPuntoDeVenta.controller('appController', ["$scope", "$rootScope", "$http", "$localStorage", "$timeout", "$interval", "$window", "$filter", "$location", "$document",  function($scope, $rootScope, $http, $localStorage, $timeout, $interval, $window, $filter, $location, $document){
	//INITIALIZE MATERIALIZECSS JQUERY COMPONENTS
	$(document).ready(function(){
		$('select').material_select();    
		$('.collapsible').collapsible();
		$("#preloaderScreen").modal({dismissible:!1,opacity:.5,inDuration:300,outDuration:200,startingTop:"30%",endingTop:"30%"});
		$("#detalleTicketModal").modal({dismissible:true,opacity:.5,inDuration:300,outDuration:200,startingTop:"4%",endingTop:"10%",ready:function(o,i){},complete:function(){}});
		$("#detalleProductModal").modal({dismissible:true,opacity:.5,inDuration:300,outDuration:200,startingTop:"4%",endingTop:"10%",ready:function(o,i){},complete:function(){}});
		$("#newProductModal").modal({dismissible:true,opacity:.5,inDuration:300,outDuration:200,startingTop:"4%",endingTop:"10%",ready:function(o,i){},complete:function(){}});
		$('.datepicker').pickadate({selectMonths: true,selectYears: 15,today: 'HOY',clear: 'BORRAR',close: 'OK',closeOnSelect: true});
		$scope.updateArrayLength();
	});
	///////////////////////////////////////////////

	$scope.tableToDisplay = "";
	$scope.nuevaBoleta = [];
	$scope.currentX = 0;
	$scope.currentY = 0;
	var backupListaDeProductos = [];

	$scope.newProduct = {
		"nom_prod": null,
		"cant_1": null,
		"cant_2": null,
		"cant_3": null,
		"cant_4": null,
		"cant_5": null,
	};



	//catch all keyup events
	$document.bind('keyup', function(e){
		console.log(e.which);
		//console.log($scope.filteredListaDeProductos[0]);
		/* UP = 38; DOWN = 40; LEFT = 37; RIGHT = 39; SUPR = 46; A = 65*/
		if($scope.tableToDisplay === "crearBoleta"){
			$scope.updateArrayLength();
			if(e.which === 40){
				if(($scope.currentY < ($scope.temparray.length - 1)) && $scope.currentX === 0){
					$scope.currentY++;
					$scope.focusXY($scope.currentX, $scope.currentY);
				}
			}
			else if(e.which === 38){
				if($scope.currentY > 0 && $scope.currentX === 0){
					$scope.currentY--;
					$scope.focusXY($scope.currentX, $scope.currentY);
					
				}
			}
			else if(e.which === 37){
				if($scope.currentX > 0){
					$scope.currentX--;
					$scope.focusXY($scope.currentX, $scope.currentY);
				}
			}
			else if(e.which === 39){
				if($scope.currentX < 2){
					$scope.currentX++;
					$scope.focusXY($scope.currentX, $scope.currentY);
				}
			}
			else if(e.which === 65){
				if(confirm("¿Esta seguro de eliminar todos los productos de la nueva boleta?")){
					$scope.setTableToDisplay("crearBoleta");
					$scope.cashPayment = null;
					$scope.codeSelector = null;
					$scope.nuevaBoleta = new Array;
					$scope.listaDeProductos = angular.copy(backupListaDeProductos);
					$scope.focusXY(0,0);
				}
			}
		}
		/* B = 66 */
		else if($scope.tableToDisplay === "pagarBoleta"){
			if(e.which === 66){
				$scope.setTableToDisplay("crearBoleta");
				$timeout(function(){document.getElementById("codeSelectorInputElement").focus();},100);
			}
		}
	});

	var getProductListFromServer = function(){
		$http.get('../php/db_transactions/getProductList.php').then(function successCallback(response){
			if(response.data.status === "success"){
				$scope.listaDeProductos = response.data.products;
				backupListaDeProductos  = angular.copy($scope.listaDeProductos);
				$scope.isRouteLoading   = false;
			}
		}, function errorCallback(response){

		});
	}

	var getStatusOfCashRegister = function(){
		$http.get('../php/db_transactions/getCashRegisterStatus.php').then(function successCallback(response){
			if(response.data.status === "success"){
				$scope.cashRegister = response.data.cashRegister;
				$scope.setTableToDisplay("crearBoleta");
			}
		}, function errorCallback(response){

		});
	};

	$scope.deleteElement = function(element){
		var found = $scope.nuevaBoleta.indexOf(element);
		$scope.nuevaBoleta.splice(found, 1);
		$scope.codeSelector = null;
		$scope.focusXY(0,0);
	}

	$scope.insertNewProductOnProductsList = function(object){
		if($scope.filteredListaDeProductos[0].choosenCantidad != null){
			//console.log(object);
			$scope.nuevaBoleta.unshift(object);
			$scope.codeSelector = null;
			$scope.listaDeProductos = angular.copy(backupListaDeProductos);
			$scope.focusXY(0,0);
		}
	};

	$scope.whenEnterKeyPressOnCodeSelectorDoSomething = function(e){
		e.stopImmediatePropagation();
		e.preventDefault();
		if($scope.codeSelector != null && $scope.codeSelector > 0){
			if($scope.filteredListaDeProductos.length > 0){
				//$scope.focusXY(1,1);
				angular.element(document.getElementById("scapegoatCantidadProducto")).focus();
			}
			else{
				$scope.codeSelector = null;
			}
		}
		else if($scope.codeSelector == null || $scope.codeSelector <= 0){
			if($scope.nuevaBoleta.length > 0){
				$scope.setTableToDisplay("pagarBoleta");
				$timeout(function(){document.getElementById("cashPaymentInputElement").focus();},0);
			}
		}
	};

	$scope.whenEnterKeyPressDoSomething = function(e){
		if(e.keyCode === 13){
			var currentFocusElement = document.activeElement;
			if(document.getElementById("scapegoatProporcionesProducto") === currentFocusElement && $scope.filteredListaDeProductos[0].choosenProporcion != null){
				$scope.focusXY(2,1);
			}
		}
	};

	$scope.focusXY = function(coorX, coorY){
		var dirX = parseInt(coorX);
		var dirY = parseInt(coorY);

		$scope.updateArrayLength();
		//console.log("coors", $scope.temparray[0][0]);
		//console.log("temparray", $scope.temparray);
		document.getElementById($scope.temparray[dirY][dirX].id).focus();
		$scope.currentX = dirX;
		$scope.currentY = dirY;
	};

	$scope.updateArrayLength = function(){
		var myInputFields = $("#tablaNuevaBoleta tbody tr input[type='text'], #tablaNuevaBoleta tbody tr input[type='number']");
		var x,i,j,chunk = 3;
		$scope.temparray = new Array;
		$scope.temparray[0] = new Array; 
		$scope.temparray[0][0] = myInputFields[0];
		for (x=1, i=1, j=myInputFields.length; i<j; x++, i+=chunk) {
			$scope.temparray[x] = myInputFields.slice(i,i+chunk);
		}
	};

	$scope.getTotal = function(){
		var summation = 0;
		$scope.nuevaBoleta.forEach(function(item, index){
			switch(item.choosenCantidad){
				case "1": summation += item.cant_1;
				break;

				case "2": summation += item.cant_2;
				break;

				case "3": summation += item.cant_3;
				break;

				case "4": summation += item.cant_4;
				break;

				case "5": summation += item.cant_5;
				break;
			}
		});

		return summation;
	};

	$scope.getChange = function(){
		if($scope.cashPayment < $scope.getTotal()){
			return "DINERO INSUFICIENTE";
		}
		else if($scope.cashPayment == null){
			return "INGRESE EFECTIVO";
		}
		else{
			var difference = $scope.cashPayment - $scope.getTotal();
			return "$" + difference;
		}
	};

	$scope.commitPayment = function(e){
		e.stopImmediatePropagation();
		if(e.keyCode === 13 && $scope.cashPayment >= $scope.getTotal()){
			commitPaymentOnServer($scope.getTotal(), $scope.cashPayment, $scope.cashChange, $scope.nuevaBoleta);
		}
		else if(e.keyCode === 13 && $scope.cashPayment < $scope.getTotal()){
			Materialize.toast("Dinero insuficiente", 5000, "red");
			$scope.cashPayment = null;
		}
	};

	var commitPaymentOnServer = function(totalBoleta, pagoEfectivo, suCambio, listaDeProductos){
		var dataToSend = {"totalBoleta": totalBoleta, "pagoEfectivo": parseInt(pagoEfectivo), "suCambio": suCambio, "listaDeProductos": listaDeProductos};
		$http.post('../php/db_transactions/payAndPrint.php', dataToSend).then(function successCallback(response){
			if(response.data.status === "success"){
				Materialize.toast("Boleta impresa con exito", 5000, "green");
				$scope.setTableToDisplay("crearBoleta");
				$scope.cashPayment = null;
				$scope.codeSelector = null;
				$scope.nuevaBoleta = new Array;
				$scope.listaDeProductos = angular.copy(backupListaDeProductos);
				$scope.focusXY(0,0);

			}
			else{
				Materialize.toast("Error interno al generar boleta", 5000, "red");
			}
		}, function errorCallback(response){
			Materialize.toast("Error interno al generar boleta", 5000, "red");
		});
	};

	$scope.setTableToDisplay = function(tableName){
		if($scope.cashRegister.open === false && tableName === "crearBoleta"){
			$scope.tableToDisplay = "abrirCerrarCaja";
		}
		else{
			$scope.tableToDisplay = tableName;
			getTicketsListFromServer();
			getProductListFromServer();
			getCashRegisterListFromServer();
		}
		if($scope.tableToDisplay === 'pagarBoleta'){
			$scope.cashPayment = $scope.getTotal();
		}
	};

	$scope.startCashRegister = function(){
		$http.put('../php/db_transactions/startCashRegister.php', $scope.cashRegister).then(function successCallback(response){
			if(response.data.status === "success"){
				$scope.cashRegister = response.data.cashRegister;
				$scope.setTableToDisplay("crearBoleta");
			}
		}, function errorCallback(response){

		});
	};

	$scope.endCashRegister = function(){
		if($scope.calculateEndCash() >= $scope.cashRegister.start_cash){
			$http.put('../php/db_transactions/endCashRegister.php', $scope.cashRegister).then(function successCallback(response){
				if(response.data.status === "success"){
					$scope.cashRegister = response.data.cashRegister;
					$scope.setTableToDisplay("crearBoleta");
				}
			}, function errorCallback(response){

			});
		}
		else{
			Materialize.toast("El efectivo de Cierre de Caja, no puede ser menor al de Apertura.", 5000, "red");
		}
	};

	$scope.calculateEndCash = function(){
		let object = angular.copy($scope.cashRegister);
		let summation = 0;
		summation += object.end_cash_20k * 20000;
		summation += object.end_cash_10k * 10000;
		summation += object.end_cash_5k  * 5000;
		summation += object.end_cash_2k  * 2000;
		summation += object.end_cash_1k  * 1000;
		summation += object.end_cash_500 * 500;
		summation += object.end_cash_100 * 100;
		summation += object.end_cash_50  * 50;
		summation += object.end_cash_10  * 10;
		return summation;
	};

	var getTicketsListFromServer = function(){
		$http.get('../php/db_transactions/getTicketList.php').then(function successCallback(response){
			if(response.data.status === "success"){
				$scope.tickets = response.data.tickets;
			}
		}, function errorCallback(response){

		});
	};

	$scope.showTicketDetail = function(selection){
		$scope.selectedTicket = angular.copy(selection);
		angular.element(document.getElementById("detalleTicketModal")).modal('open');
	};

	$scope.reprintOldTicket = function(ticket){
		$http.get('../php/db_transactions/reprintOldTicket.php', {params:{id: ticket.id}}).then(function successCallback(response){
			if(response.data.status === "success"){
				Materialize.toast("Boleta Reimpresa con exito", 5000, "green");
			}
		}, function errorCallback(response){

		});
	};

	$scope.reprintOldTicket = function(ticket){
		$http.get('../php/db_transactions/reprintOldTicket.php', {params:{id: ticket.id}}).then(function successCallback(response){
			if(response.data.status === "success"){
				Materialize.toast("Boleta Reimpresa con exito", 5000, "green");
			}
		}, function errorCallback(response){

		});
	};

	$scope.reprintOldCashRegisterStatus = function(item){
		$http.get('../php/db_transactions/reprintOldCashRegisterStatus.php', {params:{id: item.sess_id}}).then(function successCallback(response){
			if(response.data.status === "success"){
				Materialize.toast("Caja Reimpresa con exito", 5000, "green");
			}
		}, function errorCallback(response){

		});
	};

	$scope.showProductDetail = function(item){
		$scope.selectedProduct = angular.copy(item);
		$scope.selectedProduct.old_id = angular.copy(item.id);
		//console.log($scope.selectedProduct);
		angular.element(document.getElementById("detalleProductModal")).modal('open');
	};

	$scope.editExistingProduct = function(item){
		$http.put('../php/db_transactions/updateProductInfo.php', $scope.selectedProduct).then(function successCallback(response){
			if(response.data.status === "success"){
				Materialize.toast('Cambios realizados con exito', 5000, 'green');
				angular.element(document.getElementById("detalleProductModal")).modal('close');
				getProductListFromServer();
			}
		}, function errorCallback(response){

		});
	};

	$scope.saveNewProduct = function(newProduct){
		$http.post('../php/db_transactions/createNewProduct.php', newProduct).then(function successCallback(response){
			if(response.data.status === "success"){
				$scope.newProduct = null;
				Materialize.toast('Cambios realizados con exito', 5000, 'green');
				angular.element(document.getElementById("newProductModal")).modal('close');
				getProductListFromServer();
			}
		}, function errorCallback(response){

		});
	};

	$scope.addNewProduct = function(){
		angular.element(document.getElementById("newProductModal")).modal('open');
	};

	$scope.getValueFromScapegoatAndChoosenCant = function(scapegoat){
		switch(scapegoat.choosenCantidad){
			case "1": return scapegoat.cant_1;
			break;

			case "2": return scapegoat.cant_2;
			break;

			case "3": return scapegoat.cant_3;
			break;

			case "4": return scapegoat.cant_4;
			break;

			case "5": return scapegoat.cant_5;
			break;

			default: return 0;
		}
	};

	var getCashRegisterListFromServer = function(){
		$http.get('../php/db_transactions/getCashRegisterList.php').then(function successCallback(response){
			if(response.data.status === "success"){
				$scope.cashRegisterList = response.data.cashRegisterList;
				//console.log($scope.cashRegisterList);
			}
		}, function errorCallback(response){

		});
	};

	$scope.printSalesSummary = function(item){
		$http.get('../php/db_transactions/printSalesSummary.php', {params:{since: item.since, till: item.till}}).then(function successCallback(response){
			if(response.data.status === "success"){
			}
		}, function errorCallback(response){

		});
	};

	getStatusOfCashRegister();
	getProductListFromServer();
	getTicketsListFromServer();
	getCashRegisterListFromServer();
}]);//close controller