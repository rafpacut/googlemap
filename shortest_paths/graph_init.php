<?php
include "dijkstra.php";

class Vertex
{
	var $ngb_edges = array();
	var $distance = 999999;
	var $parent = null; 
	var $number = null;
	var $lat = null;
	var $lng = null;
}

class edge
{
	var $ngb = null;
	var $weight = null;
}


/*
Calculates the great-circle distance between two points, with
  the Haversine formula.
  */
function haversineDistance( $latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo )
{
	$earthRadius = 6371000;
  // convert from degrees to radians
  	$latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);
  
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
  
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                 cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
} 



if( isset( $_GET['source_id'] ) )
{

	$graph = array();

	$circle_radius = 250;

	mysql_connect( 'mysql11.000webhost.com', 'a7024616_rafal', 'rootpassword1');
	mysql_select_db( 'a7024616_googlem' );
	$query = 'select * from marker_pos';


	$db_struct = mysql_query( $query) or die( mysql_error() );
	while( $row = mysql_fetch_array( $db_struct ) )
	{
		$marker = new Vertex;
		$marker->number = $row['id'];
		$marker->lat = $row['latitude'];
		$marker->lng = $row['longitude'];
		array_push( $graph, $marker );
	}

	foreach( $graph as $actual )
	{
		foreach( $graph as $vert )
		{
			if( $actual->number != $vert->number )
			{
				$distance = haversineDistance( $actual->lat, $actual->lng, $vert->lat, $vert->lng) / 1000;
				if( $distance <= $circle_radius )
				{
					$edge = new edge;
					$edge->ngb = $vert;
					$edge->weight = $distance;
					array_push( $actual->ngb_edges, $edge );
				}
			}
		}
	}


	foreach( $graph as $vert )
	{
		if( $vert->number === $_GET['source_id'] )
			$source = $vert;
		if( $vert->number === $_GET['dest_id'] )
			$target = $vert;
	}	


	dijkstra( $graph, $source, $target );



	
	$path = array();
	$trace = $target;

	$path[0] = array();
	$path[0][0] = $trace->lat;
	$path[0][1] = $trace->lng;

	while( $trace->parent != null )
	{
		$trace = $trace->parent;
		$arr = array();
		array_push( $path, $arr );
		$el_number = count( $path ) - 1;
		$path[ $el_number ][0] = $trace->lat;
		$path[ $el_number ][1] = $trace->lng;
	}

	$obj = json_encode( $path );
	echo $obj;
}


				


	

?>
