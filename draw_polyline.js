function draw_polyline( data )
{
	var path = [];
	$.each( data, function( key, val )
	{
		if( !isNaN( val[0] ) && !isNaN( val[1] ) )
		{
			var position = new google.maps.LatLng( val[0], val[1] );
			path.push( position );
		}
	});

	var polyline = new google.maps.Polyline(
	{
		path: path,
	    	strokeColor: '#FF0000',
	        strokeWeight: 5
	});
	
	google.maps.event.addListener( polyline, 'click', function( event )
	{
		polyline.setMap( null );
	});

	polyline.setMap( map );
}


