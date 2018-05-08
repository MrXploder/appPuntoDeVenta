appPuntoDeVenta.filter('chunk', function () {
	function cacheIt(func) {
		var cache = {};
		return function(arg) {
        // if the function has been called with the argument
        // short circuit and use cached value, otherwise call the
        // cached function with the argument and save it to the cache as well then return
        return cache[arg] ? cache[arg] : cache[arg] = func(arg);
      };
    }

    // unchanged from your example apart from we are no longer directly returning this   ​
    function chunk(items, chunk_size) {
    	var chunks = [];
    	if (angular.isArray(items)) {
    		if (isNaN(chunk_size))
    			chunk_size = 4;
    		for (var i = 0; i < items.length; i += chunk_size) {
    			chunks.push(items.slice(i, i + chunk_size));
    		}
    	} else {
    		console.log("items is not an array: " + angular.toJson(items));
    	}
    	return chunks;
    }
return cacheIt(chunk);
});