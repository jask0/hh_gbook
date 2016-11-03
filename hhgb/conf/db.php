<?php

require_once 'connect.php';


class DB {
    private $conn;
    private $table_posts;
    private $table_config;
    
    public function __construct() {
        global $dbc;    //read from config.php
        $c = new CONNECT;
        $this->conn = $c->getConnection();
        $this->table_posts = $dbc['table']."posts";
        $this->table_config = $dbc['table']."config";
        
    }
    
    private function getConn(){
        return $this->conn;
    }

    private function getvariable($array){
        $data = array();
        foreach ($array as $key1 => $raw) {
            foreach ($raw as $key2 => $value) {
                $data[$raw[1]] = $raw[2];
            }
        }
        return $data;
    }
    
    private function selectFromTable($query){
        $result = mysqli_query($this->conn, $query);
        
        if ( ! $result )
        {
          die(mysqli_error($this->conn));
        } 
        $return_result = array();
        while ($row = mysqli_fetch_assoc($result)){
            $row['datum'] = date_create($row['datum']);
            array_push($return_result, $row);
        }
        mysqli_free_result( $result );
        return $return_result;
    }
    
    public function getConfig($category = "2"){
        /* Categorys:
         * 0 - Global System parameter
         * 1 - User specific data
         * 2 - Guestbook settings
         */
        $sql = "SELECT * FROM $this->table_config WHERE meta = $category;";
        $result = @mysqli_query($this->conn, $sql);
	
	if ( ! $result )
	{
	  echo ('Error on reading user configs: ' . mysqli_error($this->conn));
	} 
	$data = $this->getvariable(mysqli_fetch_all($result));
        
	mysqli_free_result( $result );
        return $data;
    }

    public function getSystemConfig(){
        //select all user data in config table
        return $this->getConfig("0");
    }
    
    public function getUserConfig(){
        //select all user data in config table
        return $this->getConfig("1");
    }
    
     public function getGbConfig(){
        //select all user data in config table
        return $this->getConfig("2");
    }
    
    public function getValue($variable, $category="2"){
        $data = $this->getConfig($category);
        return $data[$variable];
    }
    
    public function getSalt(){
        $sys = $this->getSystemConfig();
        return $sys['salt']+"#+abhGD.!aseivtrn457AGFDH";
    }
    
    # Manipulation with posts
    public function getCountOfUnpablicPosts(){
        $sql = "SELECT count(public) FROM $this->table_posts WHERE public = 0 ORDER BY id DESC";
	$result = mysqli_query($this->conn, $sql);
	$count = mysqli_fetch_row($result);
        mysqli_free_result( $result );
        return (int)$count[0];
    }
    
    public function getPostCountbyCondition($myCond){
	$q = "SELECT count(id) FROM $this->table_posts $myCond";
        $count = mysqli_query( $this->conn, $q );
	$count = mysqli_fetch_row($count);
	return $count[0];
    }
    
    public function getLastPosts($limit = 3){
        $sql = "SELECT * FROM $this->table_posts ORDER BY id DESC LIMIT $limit";
	$result = mysqli_query($this->conn, $sql);
	
	if ( ! $result ){
            die('Request error: ' . mysqli_error($this->conn));
            
        }
        return $result;
    }
    
    public function readPost($id){
        $query = sprintf("SELECT * FROM $this->table_posts WHERE id = %s",$id);
        return $this->selectFromTable($query);
    }

    public function readPosts($public="1") {
        $query = "SELECT * FROM $this->table_posts WHERE public = $public";
        return $this->selectFromTable($query);
    }
    
    public function readPostsForPage($limit, $offset, $isAdmin){
        if ($isAdmin){
            $where = ' WHERE public*1 in (0,1) ';
        } else {
            $where = 'WHERE public = 1';
        }
        $query = "SELECT * FROM $this->table_posts $where ORDER BY datum DESC LIMIT $limit OFFSET $offset";
        return $this->selectFromTable($query);
    }

    #update configuration
    public function updateConfigTable($data){
        foreach ($data as $key => $value) {
            $q = "UPDATE $this->table_config SET value='$value' WHERE variable='$key';";
            if(!mysqli_query($this->conn, $q)){
                    return 0;
            }
            mysqli_commit($this->conn); 
        }
        return 1;
    }

    public function getCustomCSS(){
        $gbc = $this->getGbConfig();
        return $gbc['custom_css'];
    }
    
    public function insertInToPosts($form){
        $form['kommentar'] = (isset($form['kommentar']) ? $form['kommentar'] : '');
	$form['public'] = $this->getGbConfig()['post_instant_online'];
        $query = sprintf('INSERT INTO '.$this->table_posts.' (name,email,homepage,betreff,bild_url,nachricht,kommentar,public,gb) VALUES ("%s","%s","%s","%s","%s","%s","%s","%s","%s")',$form['name'],$form['email'],$form['homepage'],$form['betreff'],$form['bild_url'],$form['nachricht'],$form['kommentar'],$form['public'],1);

        if (mysqli_query($this->conn, $query)) {
            return true;
        } else {
            return false;
        }
    }
    public function updatePost($id, $form){
        $form['datum'] = (isset($form['datum']) ? 'datum="'.$form['datum'].'",' : '');
        $form['kommentar'] = (isset($form['kommentar']) ? $form['kommentar'] : '');
        $query = 'UPDATE '.$this->table_posts.'
                  SET   '.$form['datum'].''
                     . 'name="'.$form['name'].'",
                        email="'.$form['email'].'",
                        homepage="'.$form['homepage'].'",
                        betreff="'.$form['betreff'].'",
                        bild_url="'.$form['bild_url'].'",
                        nachricht="'.$form['nachricht'].'",
                        kommentar="'.$form['kommentar'].'",
                        public='.$form['public'].'
                    WHERE id='.$id.';';

        if (mysqli_query($this->conn, $query)) {
            return true;
        } else {
            return false;
        }
    }
    
    public function deletePost($id){
        $query = sprintf("DELETE FROM $this->table_posts WHERE id = %d",$id);

        if ( mysqli_query($this->conn, $query) )
        {
                return true;
        }else{
               return false;
        }
    }
}
