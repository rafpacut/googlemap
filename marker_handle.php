<?php


mysql_connect( 'mysql11.000webhost.com', 'a7024616_rafal', 'rootpassword1');
mysql_select_db( 'a7024616_googlem' );

if( isset( $_GET['marker_id'] ) )
{
	$query = 'insert into marker_pos (id, latitude, longitude) values(' . $_GET['marker_id'] . ',' . $_GET['lat'] . ', ' . $_GET['lng'] . ')';
	mysql_query( $query ) or die( mysql_error() );
}
else if( isset( $_GET['id'] ) )
{
	$query = 'delete from marker_pos where id =' .$_GET['id'];
	mysql_query( $query ) or die( mysql_error() );
}
else
{
	$query = 'select * from marker_pos';
	$db_struct = mysql_query( $query ) or die( mysql_error() );
	$markers = array();
	while( $row = mysql_fetch_array( $db_struct ) )
	{
		$markers[ $row['id'] ] = array();
		$markers[ $row['id'] ][0] = $row['latitude'];
		$markers[ $row['id'] ][1] = $row['longitude'];
	}
	$obj = json_encode( $markers );
	echo $obj;

}



?>


































