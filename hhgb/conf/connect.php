<?php
	
if(strpos($_SERVER['REQUEST_URI'], 'admin/') !== false){
	$file = file_get_contents('conf/dbc.json');
} else {
	$file = file_get_contents('admin/conf/dbc.json');
}
global $db;
$db = json_decode($file, true);

// Create connection
$conn = mysqli_connect($db['servername'], $db['username'], $db['password'], $db['dbname']);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

?>