appPuntoDeVenta.filter('mysqlDate', function mysqlDate($filter){
  return function(text){
    var  tempdate = new Date(text.replace(/-/g,"/"));
    return $filter('date')(tempdate, "dd-MM-yyyy HH:mm:ss");
  }
});