var map;
var markersArray = [];
// if there is no markers in database, the first marker will have no id, 
// and by clicking on the map the markersArray[0] would be NaN
markersArray[0] = -1; 
var graph_constructing = false;
var source = [];


function add_marker( location, marker_id )
{
	var marker = new google.maps.Marker
	({
		position: location,
		map: map,
		title: "marker",
		id: marker_id
	});


	var circle = new google.maps.Circle
	({
		center: location,
		radius: 250000,
		map: map,
		strokeOpacity: 1,
		clickable: true,
		strokeWeight: 2,
		visible: false
	});


	google.maps.event.addListener( marker, 'mouseover', function( event )
	{
		circle.setVisible(true);
	});

	google.maps.event.addListener( marker, 'mouseout', function( event )
	{
		circle.setVisible(false);
	});



	//marker specific event listener allowing deletion:
	google.maps.event.addListener( marker, 'click', function()
	{
		$.getJSON("./marker_handle.php", { 'id': marker_id });
		marker.setMap( null );
	});


	//send marker position to database via php:
	//w tej chwili unikalnosc markerow jest zachowana dzieki odrzucaniu duplikatow przez baze danych,
	//nie lepiej byloby sprawdzic ja iterujac po tablicy markerow?
	$.getJSON("./marker_handle.php", { 'marker_id': marker_id ,'lat': location.lat() , 'lng': location.lng() });

	google.maps.event.addListener( marker, 'rightclick', function()
	{
		if( graph_constructing )
        	{
			graph_constructing = false;

			$.getJSON("./shortest_paths/graph_init.php",{ 'source_id': source[0], 'source_lat': source[1], 
			       'source_lng': source[2], 'dest_id': marker_id, 'dest_lat': location.lat(), 'dest_lng': location.lng() }, 
		       		function( data ) 
				{
					if( data != 'not found' )	
						draw_polyline( data);
					else
						alert( 'nie znaleziono sciezki.');
				})

		}
		else
		{
			graph_constructing = true;

			source[0] = marker_id;
			source[1] = location.lat();
			source[2] = location.lng();
		}

		
	});

	markersArray.push( marker_id );
}



function initialize()
{
  var mapOptions =
  {
    zoom: 5,
    center: new google.maps.LatLng(-34.397, 150.644),
    mapTypeId: google.maps.MapTypeId.ROADMAP
  };
  map = new google.maps.Map(document.getElementById('map-canvas'),
      mapOptions);

  //re-create all the markers previously set from the database:
	$.getJSON( './marker_handle.php', function( data )
  	{
		$.each( data, function( key, val )
		{
			var new_marker_Latlng = new google.maps.LatLng( val[0], val[1] );
			add_marker( new_marker_Latlng, parseFloat(key) );
		});

		
	});

//create a marker after clicking on map:
google.maps.event.addListener( map, 'click', function( event )
{
	marker_id = markersArray[markersArray.length - 1] + 1;
	  add_marker( event.latLng, marker_id );
});




}



google.maps.event.addDomListener(window, 'load', initialize);



