<!doctype html>
<html lang="en">
 <head>
  <meta charset="UTF-8">
  <script src="//cdn.bootcss.com/jquery/3.2.1/jquery.min.js"></script>
 </head>
 <body>
 <input id='ttt' value='123'>
 <script>
function DataBinder( object_id ) {
  var pubSub = $({});

  var data_attr = "bind-" + object_id;
  var message = object_id + ":change";

  $( document ).on( "change", "[data-" + data_attr + "]", function( evt ) {
    var $input = $( this );
    pubSub.trigger( message, [ $input.data( data_attr ), $input.val() ] );
  });

  pubSub.on( message, function( evt, prop_name, new_val ) {
    $("[data-" + data_attr + "=" + prop_name + "]").each( function() {
      var $bound = $( this );

      if ( $bound.is("input, textarea, select") ) {
        $bound.val( new_val );
      } else {
        $bound.html( new_val );
      }
    });
  });

  return pubSub;
}

 $(function () {
    var x = DataBinder('xxx');
    $('#ttt').attr('data-bind-xxx', 'xxx');
    x.trigger( "xxx:change", [ 'xxx', 777, this ] );
 });
 </script>
 </body>
</html>
