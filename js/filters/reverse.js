angular.module('appPuntoDeVenta').filter('reverse', function() {
	return function(items) {
		return items.slice().reverse();
	};
});