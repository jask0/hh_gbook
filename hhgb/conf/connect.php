<?php
require_once 'config.php';
class CONNECT{
    public $conn;
    
    public function __construct() {
        global $dbc;
        $this->conn = mysqli_connect($dbc['servername'], $dbc['username'], $dbc['password'], $dbc['dbname']);
    }
    
    public function getConnection(){
        return $this->conn;
    }
}
if(isset($dbc)){
	// Create connection
	$conn = new CONNECT;
	// Check connection
	if (!$conn->getConnection()) {
		die("Connection failed: " . mysqli_connect_error());
	}
}
