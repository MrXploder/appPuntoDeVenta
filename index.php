<?php require $_SERVER['DOCUMENT_ROOT'].'/php/functions/versionControll.php'; ?>
<!DOCTYPE html>
<html ng-app="appPuntoDeVenta" ng-controller="appController">
<head>
	<title></title>
	<!--META-->
	<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<!--NOSCRIPT-->
	<noscript><meta http-equiv="Refresh" content="0; URL=./nojs.html"></noscript>
	<link rel="manifest" href="../manifest.json">
	<!--No descuidar el orden de los archivos CCS y JS-->
	<!--CSS DEPENDENCIES-->
	<link rel="stylesheet" href="../css/materialize.css">
	<link rel="stylesheet" href="../css/materialize-stickyfooter.css">
	<link rel="stylesheet" href="../css/loading-bar.css">
	<link rel="stylesheet" href="../css/spinkit.css">
	<link rel="stylesheet" href="../css/fontawesome.css">
	<link rel="stylesheet" href="../css/webfont.css">
	<link rel="stylesheet" href="../css/custom.css?v=<?php echo $versionControll ?>">
	<!--JAVASCRIPT DEPENDENCIES-->
	<script src="../js/dependencies/jquery.js"></script>
	<script src="../js/dependencies/angular.js"></script>
	<script src="../js/dependencies/materialize.js"></script>
	<script src="../js/dependencies/angular-html5storage.js"></script>
	<script src="../js/dependencies/angular-loadingBar.js"></script>
	<script src="../js/dependencies/angular-dirPagination.js"></script>
	<script src="../js/dependencies/angular-materialize.js"></script>
	<script src="../js/dependencies/angular-locale_es-419.js"></script>
	<script src="../js/dependencies/FileSaver.js"></script>
	<script src="../js/dependencies/xlsx.full.min.js"></script>
	<script src="../js/dependencies/emulatetab.joelpurra.js"></script>
	<!--ANGULARJS-APP-->
	<!--ANGULAR MODULES-->
	<script src="../js/modules/appPuntoDeVenta.js?v=<?php echo $versionControll ?>"></script>
	<!--ANGULAR FILTERS-->
	<script src="../js/filters/chunk.js?v=<?php echo $versionControll ?>"></script>
	<script src="../js/filters/mysqlDate.js?v<?php echo $versionControll ?>"></script>
	<!--ANGULAR RUNS-->
	<script src="../js/runs/navigatorOnline.js?v=<?php echo $versionControll ?>"></script>
	<!--ANGULAR DIRECTIVES-->
	<script src="../js/directives/stringToNumber.js?v=<?php echo $versionControll ?>"></script>
	<script src="../js/directives/sgNumberInput.js?v=<?php echo $versionControll ?>"></script>
	<!--ANGULAR CONTROLLERS-->
	<script src="../js/controllers/app.js?v=<?php echo $versionControll ?>"></script>
</head>
<body ondragstart="return false;" ondrop="return false;">
	<header>
		<nav>
			<nav class="white">
				<div class="nav-wrapper">
					<a href="#"><img src="../img/logo.png" style="width: 150px; height: 63px"></img></a>
					<ul id="nav-mobile" class="right" ng-if="!isRouteLoading">
						<li ng-class="(tableToDisplay == 'crearBoleta' || tableToDisplay == 'pagarBoleta') ? 'active':''"><a ng-click="setTableToDisplay('crearBoleta')">VENDER</a></li>
						<li ng-class="tableToDisplay == 'administrarBoletas' ? 'active':''" ><a ng-click="setTableToDisplay('administrarBoletas')">BOLETAS</a></li>
						<li ng-class="tableToDisplay == 'administrarProductos' ? 'active':''"><a ng-click="setTableToDisplay('administrarProductos')">PRODUCTOS</a></li>
						<li ng-class="tableToDisplay == 'administrarCaja' ? 'active':''"><a ng-click="setTableToDisplay('administrarCaja')">CAJA</a></li>
						<li ng-class="tableToDisplay == 'abrirCerrarCaja' ? 'active':''"><a ng-click="setTableToDisplay('abrirCerrarCaja')">ABRIR/CERRAR</a></li>
					</ul>
				</div>
			</nav>
		</div>
	</nav>
</header>
<main ng-init="isRouteLoading = true">
	<div class="screenCentered" ng-show="isRouteLoading">
		<div class='sk-folding-cube'>
			<div class='sk-cube1 sk-cube'></div>
			<div class='sk-cube2 sk-cube'></div>
			<div class='sk-cube4 sk-cube'></div>
			<div class='sk-cube3 sk-cube'></div>
		</div>   
	</div>
	<div id="preloaderScreen" class="modal modalLikeLoader vMiddle hCenter" ng-cloak>
		<br><br><br><br>
		<div class="preloader-wrapper big active">
			<div class="spinner-layer spinner-blue">
				<div class="circle-clipper left">
					<div class="circle"></div>
				</div><div class="gap-patch">
					<div class="circle"></div>
				</div><div class="circle-clipper right">
					<div class="circle"></div>
				</div>
			</div>
			<div class="spinner-layer spinner-red">
				<div class="circle-clipper left">
					<div class="circle"></div>
				</div><div class="gap-patch">
					<div class="circle"></div>
				</div><div class="circle-clipper right">
					<div class="circle"></div>
				</div>
			</div>
			<div class="spinner-layer spinner-yellow">
				<div class="circle-clipper left">
					<div class="circle"></div>
				</div><div class="gap-patch">
					<div class="circle"></div>
				</div><div class="circle-clipper right">
					<div class="circle"></div>
				</div>
			</div>
			<div class="spinner-layer spinner-green">
				<div class="circle-clipper left">
					<div class="circle"></div>
				</div><div class="gap-patch">
					<div class="circle"></div>
				</div><div class="circle-clipper right">
					<div class="circle"></div>
				</div>
			</div>
		</div>
		<br><br><br><br>
	</div>
	<div id="contentScreen" ng-show="!isRouteLoading" ng-cloak>
		<!--BEGIN MODALS-->
		<div id="detalleTicketModal" class="modal modal-fixed-footer">
			<div class="modal-content modal-topbordered green-topbordered">
				<table class="cbordered">
					<thead class="grey">
						<tr>
							<th colspan="3" ng-bind="'NUMERO DE BOLETA: '+selectedTicket.id"></th>
						</tr>
						<tr>
							<th>CANTIDAD</th>
							<th>PRODUCTO</th>
							<th>PRECIO SUBTOTAL</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td colspan="2" style="text-align: right;">TOTAL: </td>
							<td ng-bind="selectedTicket.total | currency: '$':0"></td>
						</tr>
						<tr>
							<td colspan="2" style="text-align: right;">PAGO EFECTIVO: </td>
							<td ng-bind="selectedTicket.cashPay | currency: '$':0"></td>
						</tr>
						<tr>
							<td colspan="2" style="text-align: right;">VUELTO: </td>
							<td ng-bind="(selectedTicket.cashPay - selectedTicket.total) | currency: '$':0"></td>
						</tr>
					</tfoot>
					<tbody>
						<tr ng-repeat="item in selectedTicket.detail">
							<td ng-bind="item.cant" class="vMiddle hCenter"></td>
							<td ng-bind="item.nom_prod" class="vMiddle hCenter"></td>
							<td ng-bind="item.prec |currency: '$':0" class="vMiddle hCenter"></td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>

		<div id="detalleProductModal" class="modal modal-fixed-footer">
			<div class="modal-content modal-topbordered purple-topbordered">
				<ng-form name="dp_form">
					<table class="cbordered" style="table-layout: inherit; width: 100%">
						<tr class="grey">
							<th>ID</th>
							<th>NOMBRE</th>
						</tr>
						<tr>
							<td ng-bind="selectedProduct.id"></td>
							<td><input type="text" ng-model="selectedProduct.nom_prod"></td>
						</tr>
					</table>
					<table class="cbordered" style="table-layout: inherit; width: 100%">
						<thead class="grey">
							<tr>
								<th colspan="5">PRECIOS</th>
							</tr>
							<tr>
								<th>1x</th>
								<th>2x</th>
								<th>3x</th>
								<th>4x</th>
								<th>5x</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><input type="text" currency-input="" ng-model="selectedProduct.cant_1"></td>
								<td><input type="text" currency-input="" ng-model="selectedProduct.cant_2"></td>
								<td><input type="text" currency-input="" ng-model="selectedProduct.cant_3"></td>
								<td><input type="text" currency-input="" ng-model="selectedProduct.cant_4"></td>
								<td><input type="text" currency-input="" ng-model="selectedProduct.cant_5"></td>
							</tr>
						</tbody>
					</table>
				</ng-form>
			</div>
			<div class="modal-footer">
				<div class="row">
					<button class="btn waves-effect waves-light green" type="submit" ng-click="editExistingProduct(selectedProduct)">GUARDAR CAMBIOS<i class="fas fa-share right"></i></button>
				</div>
			</div>
		</div>

		<div id="newProductModal" class="modal modal-fixed-footer">
			<div class="modal-content modal-topbordered purple-topbordered">
				<ng-form name="np_form">
					<table class="cbordered" style="table-layout: inherit; width: 100%">
						<tr class="grey"><th>NOMBRE</th></tr>
						<tr><td><input type="text" ng-model="newProduct.nom_prod"></td></tr>
					</table>
					<table class="cbordered" style="table-layout: inherit; width: 100%">
						<thead class="grey">
							<tr>
								<th colspan="5">PRECIOS</th>
							</tr>
							<tr>
								<th>1x</th>
								<th>2x</th>
								<th>3x</th>
								<th>4x</th>
								<th>5x</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><input type="text" currency-input="" ng-model="newProduct.cant_1"></td>
								<td><input type="text" currency-input="" ng-model="newProduct.cant_2"></td>
								<td><input type="text" currency-input="" ng-model="newProduct.cant_3"></td>
								<td><input type="text" currency-input="" ng-model="newProduct.cant_4"></td>
								<td><input type="text" currency-input="" ng-model="newProduct.cant_5"></td>
							</tr>
						</tbody>
					</table>
				</ng-form>
			</div>
			<div class="modal-footer">
				<div class="row">
					<button class="btn waves-effect waves-light green" type="submit" ng-click="saveNewProduct(newProduct)">GUARDAR CAMBIOS<i class="fas fa-share right"></i></button>
				</div>
			</div>
		</div>
		<!--END MODALS-->

		<!--BEGIN TABLES-->
		<div class="ccontainer">
			<br><br>
			<div class="row">
				<div class="col l6 offset-l3">
					<table id="tablaNuevaBoleta" name="tablaNuevaBoleta" class="cbordered" style="table-layout: inherit; width: 100%" ng-show="tableToDisplay == 'crearBoleta'">
						<thead>
							<tr class="green">
								<th>Nombre</th>
								<th style="width: 10%">Cantidad</th>
								<th style="width: 10%">Precio</th>
							</tr>
						</thead>
						<tbody>
							<!-- THE CODE SELECTOR -->
							<tr class="black white-text">
								<td colspan="5">
									<label for="codeSelectorInputElement" class="active">Codigo de Producto</label>
									<input type="text" ng-model="codeSelector" name="codeSelectorInputElement" id="codeSelectorInputElement" ng-keydown="$event.keyCode === 13 && whenEnterKeyPressOnCodeSelectorDoSomething($event)" autofocus>
								</td>
							</tr>
							<!--THE SCAPEGOAT-->
							<tr ng-repeat="scapegoat in filteredListaDeProductos = (listaDeProductos | filter: {id: codeSelector} | limitTo: 1)" ng-if="codeSelector != null && codeSelector != undefined && codeSelector != ''">
								<td><input type="text" name="scapegoatNomProducto" id="scapegoatNomProducto" ng-value="scapegoat.nom_prod"/></td>
								<td style="width: 10%">
									<input type="text" name="scapegoatCantidadProducto" id="scapegoatCantidadProducto" list="scapegoatCantidadProductoList" ng-model="scapegoat.choosenCantidad" ng-keypress="$event.keyCode === 13 && insertNewProductOnProductsList(scapegoat)"/>
									<datalist id="scapegoatCantidadProductoList">
										<option value="1">1 x {{scapegoat.cant_1}}</option>
										<option value="2">2 x {{scapegoat.cant_2}}</option>
										<option value="3">3 x {{scapegoat.cant_3}}</option>
										<option value="4">4 x {{scapegoat.cant_4}}</option>
										<option value="5">5 x {{scapegoat.cant_5}}</option>										
									</datalist>
								</td>
								<td>
									<input type="text" name="scapegoatPrecioProducto" id="scapegoatPrecioProducto" ng-value="getValueFromScapegoatAndChoosenCant(scapegoat)">
								</td>
							</tr>
							<!--ACTUALLY THE REAL BILL-->
							<tr ng-repeat="item in nuevaBoleta track by $index" class="grey lighten-4">
								<td><input type="text" name="nomProductNo{{$index+1}}" id="nomProductNo{{$index+1}}" ng-model="item.nom_prod" ng-keydown="$event.keyCode === 46 && deleteElement(item)"/></td>
								<td style="width: 10%"><input type="text" name="cantProductNo{{$index+1}}" id="cantProductNo{{$index+1}}" ng-model="item.choosenCantidad" ng-keydown="$event.keyCode === 46 && deleteElement(item)"/></td>
								<td style="width: 10%"><input type="text" name="precProductNo{{$index+1}}" id="precProductNo{{$index+1}}" ng-value="getValueFromScapegoatAndChoosenCant(item)" ng-keydown="$event.keyCode === 46 && deleteElement(item)"/></td>
							</tr>
						</tbody>
					</table>
					<table id="tablaPagarBoleta" name="tablaPagarBoleta" class="cbordered" style="table-layout: inherit; width: 100%" ng-show="tableToDisplay == 'pagarBoleta'">
						<thead>
							<tr class="red">
								<th colspan="3">PAGAR BOLETA</th>
							</tr>
							<tr>
								<th class="red">TOTAL: </th>
								<td><input type="text" ng-value="getTotal() | currency: '$':0" readonly></td>
							</tr>
							<tr>
								<th class="red">PAGO EFECTIVO: </th>
								<td><input type="text" id="cashPaymentInputElement" name="cashPaymentInputElement" ng-model="cashPayment" ng-keydown="$event.keyCode === 13 && commitPayment($event)" currency-input=""></td>
							</tr>
							<tr>
								<th class="red">SU CAMBIO: </th>
								<td>
									<input type="text" ng-value="getChange()" ng-model="cashChange" readonly>
								</td>
							</tr>
						</thead>
					</table>
					<div id="tablaAbrirCerrarCaja" name="tablaAbrirCerrarCaja" ng-show="tableToDisplay == 'abrirCerrarCaja'">
						<table class="cbordered" style="table-layout: inherit; width: 100%" ng-show="cashRegister.open">
							<thead class="amber">
								<tr>
									<th>#</th>
									<th>APERTURA</th>
									<th>EFECTIVO INICIAL</th>
								</tr>		
							</thead>
							<tbody>
								<tr ng-if="cashRegister.open">
									<td ng-bind="cashRegister.sess_id" class="vMiddle hCenter"></td>
									<td ng-bind="cashRegister.since" class="vMiddle hCenter"></td>
									<td ng-bind="cashRegister.start_cash | currency: '$':0" class="vMiddle hCenter"></td>								
								</tr>
							</tbody>
						</table>
						<table class="cbordered" style="table-layout: inherit; width: 100%" ng-show="cashRegister.open">
							<thead><tr><th colspan="2">INGRESAR DETALLE DE EFECTIVO PARA CERRAR LA CAJA - INGRESAR CUANTOS DE CADA BILLETE/MONEDAS TIENES EN LA CAJA</th></tr></thead>
							<tfoot>
								<tr>
									<td><a class="btn waves-effect waves-light red" ng-click="endCashRegister()">CERRAR CAJA</a></td>
									<td>Total: {{calculateEndCash() | currency: '$':0}}</td>
								</tr>
							</tfoot>
							<tbody>
								<tr>
									<th><img src="../img/billete-20k.jpg"></th>
									<td><input type="number" ng-model="cashRegister.end_cash_20k"></td>
								</tr>
								<tr>
									<th><img src="../img/billete-10k.jpg"></th>
									<td><input type="number" ng-model="cashRegister.end_cash_10k"></td>
								</tr>
								<tr>
									<th><img src="../img/billete-5k.jpg"></th>
									<td><input type="number" ng-model="cashRegister.end_cash_5k"></td>
								</tr>
								<tr>
									<th><img src="../img/billete-2k.jpg"></th>
									<td><input type="number" ng-model="cashRegister.end_cash_2k"></td>
								</tr>
								<tr>
									<th><img src="../img/billete-1k.jpg"></th>
									<td><input type="number" ng-model="cashRegister.end_cash_1k"></td>
								</tr>
								<tr>
									<th><img src="../img/moneda-500.jpg"></th>
									<td><input type="number" ng-model="cashRegister.end_cash_500"></td>
								</tr>
								<tr>
									<th><img src="../img/moneda-100.jpg"></th>
									<td><input type="number" ng-model="cashRegister.end_cash_100"></td>
								</tr>
								<tr>
									<th><img src="../img/moneda-50.jpg"></th>
									<td><input type="number" ng-model="cashRegister.end_cash_50"></td>
								</tr>
								<tr>
									<th><img src="../img/moneda-10.jpg"></th>
									<td><input type="number" ng-model="cashRegister.end_cash_10"></td>
								</tr>
							</tbody>
						</table>
						<table class="cbordered" style="table-layout: inherit; width: 100%" ng-show="!cashRegister.open">
							<thead>
								<tr><th>EFECTIVO INICIAL (CON CUANTO DINERO EMPEZARÁS A TRABAJAR)</th></tr>
							</thead>
							<tfoot>
								<tr><td><a class="btn waves-effect waves-light green" ng-click="startCashRegister()">ENVIAR</a></td></tr>
							</tfoot>
							<tbody>
								<tr><td><input type="text" ng-model="cashRegister.start_cash" currency-input=""></td></tr>
							</tbody>
						</table>
					</div>
					<table id="tablaAdministrarBoletas" name="tablaAdministrarBoletas" class="cbordered" style="table-layout: inherit; width: 100%" ng-show="tableToDisplay == 'administrarBoletas'">
						<thead class="amber">
							<tr>
								<th># BOLETA</th>
								<th>FECHA EMISIÓN</th>
								<th>REIMPRIMIR</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="3" class="vMiddle hCenter">
									<dir-pagination-controls boundary-links="true" template-url="dirPagination.tpl.html" pagination-id="ticketsPaginationId"></dir-pagination-controls>
								</td>
							</tr>
						</tfoot>
						<tbody>
							<tr dir-paginate="item in tickets | itemsPerPage: 5" pagination-id="ticketsPaginationId">
								<td><a class="btn waves-effect waves-light blue" ng-bind="item.id" ng-click="showTicketDetail(item)"></a></td>
								<td ng-bind="item.date | mysqlDate" class="vMiddle hCenter"></td>
								<td><a class="btn waves-effect waves-light grey" ng-click="reprintOldTicket(item)"><i class="fas fa-print"></i></a></td>
							</tr>
						</tbody>
					</table>
					<table id="tablaAdministrarProductos" name="tablaAdministrarProductos" class="cbordered" style="table-layout: inherit; width: 100%" ng-show="tableToDisplay == 'administrarProductos'">
						<thead class="amber">
							<tr>
								<th>CODIGO</th>
								<th>NOMBRE</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="2"><a class="btn waves-effect waves-light green" ng-click="addNewProduct()">CREAR NUEVO PRODUCTO</a></td>
							</tr>
						</tfoot>
						<tbody>
							<tr ng-repeat="product in listaDeProductos">
								<td><a class="btn waves-effect waves-light blue" ng-bind="product.id" ng-click="showProductDetail(product)"></a></td>
								<td ng-bind="product.nom_prod"></td>
							</tr>
						</tbody>
					</table>
					<table id="tablaAdministrarCaja" name="tablaAdministrarCaja" class="cbordered" style="table-layout: inherit; width: 100%" ng-show="tableToDisplay == 'administrarCaja'">
						<thead class="amber">
							<tr>
								<th>#</th>
								<th>FECHA APERTURA</th>
								<th>FECHA CIERRE</th>
								<th>REIMPRIMIR</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td colspan="4" class="vMiddle hCenter">
									<dir-pagination-controls boundary-links="true" template-url="dirPagination.tpl.html" pagination-id="cashRegisterListPaginationId"></dir-pagination-controls>
								</td>
							</tr>
						</tfoot>
						<tbody>
							<tr dir-paginate="item in cashRegisterList | itemsPerPage: 5" pagination-id="cashRegisterListPaginationId">
								<td><a class="btn waves-effect waves-light blue" ng-bind="item.sess_id"></a></td>
								<td ng-bind="item.since" class="vMiddle hCenter"></td>
								<td ng-bind="item.till" class="vMiddle hCenter"></td>
								<td><a class="btn waves-effect waves-light grey" ng-click="reprintOldCashRegisterStatus(item)"><i class="fas fa-print"></i></a></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		<!--END TABLES-->
	</div>
</main>

<footer class="page-footer footer grey darken-3">
	<div class="container">
		<div class="footer-copyright grey darken-3">
			<div class="container">
				<div class="row">
					<h6>SUPRIMIR = Estando sobre un producto puedes eliminarlo.</h6>
					<h6>B        = Cuando estes pagando, aprieta B para volver</h6>
					<h6>ENTER    = Cuando estes en el selector de Codigo y no haya nada escrito, aprieta Enter para ir a pagar</h6>
					<h6>L        = Presiona L en cualquier momento para ver el listado de productos</h6>
					<h6>ESC      = Presiona Esc cuando estes viendo el listado de productos para salir.</h6>
				</div>
				<div class="row">
					<a href="mailto: l.arancibiaf@gmail.com">© MrXploder AngularJS Dev</a>
					<a class="grey-text text-lighten-4 right" href="#!">Compilación: #<?php echo $versionControll ?></a>
				</div>
			</div>
		</div>
	</div>
</footer>
</body>
</html>
