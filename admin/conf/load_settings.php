<?php
// connection to database
include ('connect.php');

/* placeholder array,
#	@info:
#		input field values of the GB, by default ''
*/
$form = Array(
'name' => '',
'email' => '',
'homepage' => '',
'betreff' => '',
'bild_url' => '',
'nachricht' => '',
'public'=>'');

/* smilie array, 
#	@key:
#		lsit: 24 smilies 
#		set:  silie set name, and either the folder where the smilies are stored (admin/smilies/{set}/)
#		ext:  extension of the smilie files 
#	@info:
#		The smilies shoud be stored in admin/smilies/set_name, and all smilies shoud have names like icon_(xy).ext
#
*/
$smilie = Array (
'list' => Array ('(1)','(2)','(3)','(4)','(5)','(6)','(7)','(8)','(9)','(10)','(11)','(12)','(13)','(14)','(15)','(16)','(17)','(18)','(19)','(20)','(21)','(22)','(23)','(24)'),
'set' => 'yellow',
'ext' => 'gif'
);

/*load settings from database about the GB
# @param:
# 		$conn, connection to database
#		$$gdid, load the settings of GB with id=1 (needed by multiple GB)
*/
function getGBsettings($conn, $gbid=1){
	$q = "SELECT * FROM hh_gbsettings WHERE id = $gbid";
	$r = mysqli_query($conn, $q);
	$data = mysqli_fetch_assoc($r);
	return $data;
}

/*pruf is the admin loged in
# @param:
# 		no param, use the session variable or cookie
*/
function getCondition(){
	global $sgs,$form; 
	if(isset($_SESSION['username']) && $_SESSION['username'] == $sgs['username']){
		return ' WHERE public*1 in (0,1) ';
	} else {
		return ' WHERE public = 1 ';
	}
}

/* display submit form
# @param:
# 		$data ->  	posted data array to fill the value fields
#		$action ->	url for the action atributt
*/
function showForm($data, $action) {
	global $sgs,$form,$smilie; 
	$data['action'] = $action;
	include('form_template.php');
}

/*display of one post
# @param:
# 		$data ->  	posted data array to fill the value fields
*/
function showPost($data) {
	global $sgs,$form,$smilie; 
	include('post_template.php');
}

/*load meta data like css an javascript
# @param:
# 		no param
# @info:
#		custom css is here
*/
function loadMeta(){
	echo '<meta name="viewport" content="width=device-width, initial-scale=1">
<!-- Font-Awesome CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<!-- Bootstrap JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<!-- Smilies JavaScript -->
<script>
function insertTheSmiley(input) {
document.getElementById(\'nachricht\').value += input;
}

function putTheSmiley(input) {
window.opener.insertTheSmiley(input);
}
</script>
<style><!--/*
.panel-costum {
	background-color: #fff;
	border: 1px solid #8a6d3b;
	border-radius: 4px;
	-webkit-box-shadow: 0 1px 1px rgba(0,0,0,.05);
	box-shadow: 0 1px 1px rgba(0,0,0,.05);
}

.panel-costum > .panel-heading {
    color: #8a6d3b;
    background-color: #DECDAC;
    border-color: #B7A98F;
}
.panel-costum > .panel-footer {
	padding: 10px 15px;
	background-color: #FFFF99;
	border-top: 1px solid #ddd;
	border-bottom-right-radius: 3px;
	border-bottom-left-radius: 3px;
}
.pagination-custom > li > a{
	color: #7F0000;
	background-color: #eee;
	border-color: #ddd;
}
.pagination-custom > li > a:hover{
	color: #7F0000;
	background-color: #ccc;
}
.pagination > .active-custom > a {
	color: #fff;
	cursor: default;
	background-color: #8E6345;
	border-color: #7F3300;
}
.pagination > .active-custom > a:hover {
	background-color: #AD7854;
	border-color: #7F3300;
}*/-->
</style>';
}

/* display submit form and save post to database
# @param:
# 		$file ->  	url to the file where the GB is displayed
*/
function showGBookForm($file){
	global $sgs,$form,$conn,$smilie; 
	$cond = getCondition();
	if(!isset($_POST['submit'])){
		
		showForm($form, $file);
	 
	} else {
		if($_SESSION['captcha_code'] == md5($_POST['captcha'])){
			if($_POST['name'] == "" or $_POST['nachricht'] == ""){
				echo '<p class="alert alert-danger">'. $sgs['error'].'<br>'. $sgs['msg'] . '</p>';
				showForm($_POST, $file);
			} else {
				$name = htmlentities($_POST['name']);
				$email = htmlentities($_POST['email']);
				$homepage = htmlentities($_POST['homepage']);
				$betreff = htmlentities($_POST['betreff']);
				$bild_url = htmlentities($_POST['bild_url']);
				$nachricht = htmlentities($_POST['nachricht']);
				$public = $_POST['public'];
				$gbid = $_POST['gbid'];
				foreach ($smilie['list'] as $key => $value) {
					$nachricht = str_replace(':'.$value.':', '<img src="admin/smilies/'.$smilie['set'].'/icon_'.$value.'.'.$smilie['ext'].'" alt=":'.$value.':">', $nachricht);
				}
				$nachricht = str_replace("'", "\'", str_replace('"', '\"',$nachricht));
				$query = sprintf('INSERT INTO hh_gbook (name,email,homepage,betreff,bild_url,nachricht,public,gb) VALUES ("%s","%s","%s","%s","%s","%s","%s","%s")',$name,$email,$homepage,$betreff,$bild_url,$nachricht,$public,$gbid);
			
				if (mysqli_query($conn, $query)) {
					echo '<p class="alert alert-success">Die Nachricht wurde erfolgreich gesendet!</p>';
					showForm($form, $file);
				} else {
					echo '<p class="alert alert-danger">FEHLER: Die Nachricht konnte nicht gespeichert werden!</p>';
					echo  mysqli_error($conn);
				}
			}
		} else {
			echo '<p class="alert alert-danger">FEHLER: Sicherheitscode falsch, bitte noch einmal versuchen!</p>';
			showForm($_POST, $file);
		}
	}
}

/* display posts from database to website
# @param:
# 		no param
*/
function showGBookPosts(){
	global $sgs,$form,$conn; 
	$page = 1;
	$gbid = $sgs['id'];
	$where = getCondition();
	if(isset($_GET['page'])){
		$page = (int)$_GET['page'];
	}
	$offset = ($page - 1) * $sgs['posts'];
	$abfrage = "SELECT * FROM hh_gbook $where AND gb = $gbid ORDER BY id DESC LIMIT $sgs[posts] OFFSET $offset";
	$abfrage_antwort = mysqli_query($conn, $abfrage);
	
	if ( ! $abfrage_antwort )
	{
	  die('Abfrage Fehler: ' . mysqli_error($conn));
	} 
	
	while ($zeile = mysqli_fetch_array( $abfrage_antwort, MYSQL_ASSOC))
	{
		showPost($zeile);
	}
	mysqli_free_result( $abfrage_antwort );
}

/* display page navigation of GB
# @param:
# 		no param needed
*/
function showGBpageNavi(){
	global $sgs,$form,$conn; 
	$myCond = getCondition();
	
	$q = "SELECT count($sgs[id]) FROM hh_gbook $myCond";
    $count = mysqli_query( $conn, $q );
	$count = mysqli_fetch_array($count);
	
	$p=1;
	if(isset($_GET['page']) && ((int)$_GET['page'] > 1)){
		$p = ((int)$_GET['page'])-1;
	}
	echo '<center><nav><ul class="pagination pagination-custom">
			<li><a href="?page='.$p.'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
	$x = 0;
	$last_page = (($count[0]/$sgs['posts'])+1);
	for ($x = 1; $x < $last_page; $x++){
			$active ='';
			$sr_info='';
			if(isset($_GET['page']) && $_GET['page'] == $x) {
				$active =' class="active active-custom"';
				$sr_info=' <span class="sr-only">(current)</span>';
			}
			$nav = '<li'.$active.'><a href="?page='.$x.'">'.$x.$sr_info.'</a></li>';
			echo $nav;
		}
	if(isset($_GET['page']) && (((int)$_GET['page']) < ($x-1))){
		$p = (int)$_GET['page']+1;
	} elseif(!isset($_GET['page'])) {
		$p = 2;
	} else {
		$p = $x-1;
	}
	echo '<li><a href="?page='.$p.'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li></ul></nav></center>';
}
?>