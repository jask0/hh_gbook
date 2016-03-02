<?php
// connection to database
include ('connect.php');
if(strpos($_SERVER['REQUEST_URI'], 'admin/') !== false){
	include ('conf/user.php');
} else {
	include ('admin/conf/user.php');
}
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

function getCustomCss(){
	$json_css = file_get_contents('conf/css/custom.css.json');
	$data = json_decode($json_css, true);
	return $data;
}

function isFileExtension($file, $ext) {
	 if(strtolower(substr($file, strrpos($file, '.') + 1))== $ext)
		{return True;}
	else
		{return False;}
}

function getLanguageFiles(){
	$page = scandir("conf/lang/");
	$files = array();
	foreach ($page as $key => $value){
		if(isFileExtension($value,'php')){
			echo $value;
			$lang = explode('.php', $value);
			array_push($files, $lang[0]);
		}
	}
	return $files;
}
/*load language of the GB
# @param:
# 		no param
*/
function getLanguage(){
	global $user;
	if(strpos($_SERVER['REQUEST_URI'], 'admin/') !== false){
		$lang = "conf/lang/".$user['language'].".php";
	} else {
		$lang = "admin/conf/lang/".$user['language'].".php";
	}
	include($lang);
	return $l;
}
/*pruf is the admin loged in
# @param:
# 		no param, use the session variable or cookie
*/
function getCondition(){
	global $user,$form; 
	if(isset($_SESSION['username']) && $_SESSION['username'] == $user['username']){
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
	$l = getLanguage();
	$data['action'] = $action;
	include('form_template.php');
}

/*display of one post
# @param:
# 		$data ->  	posted data array to fill the value fields
*/
function showPost($data, $edit=0) {
	global $sgs,$form,$smilie;
	$l = getLanguage();
	if(is_numeric($edit)){
		$edit='admin/index.php?page=edit&id='.$data['id'];
	}
	include('post_template.php');
}

/*load meta data like css an javascript
# @param:
# 		no param
# @info:
#		custom css is here
*/
function loadMeta(){
	global $user;
	echo '
<!-- Font-Awesome CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="admin/conf/css/bootstrap.gb.css">
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
</script>';

	if ($user['custom_css']){
		echo "\n".'<link rel="stylesheet" href="admin/conf/css/gb.custom.css">'."\n";
	}
}

/* display submit form and save post to database
# @param:
# 		$file ->  	url to the file where the GB is displayed
*/
function showGBookForm($file){
	global $sgs,$form,$conn,$smilie,$user; 
	$cond = getCondition();
	$l = getLanguage();
	if(!isset($_POST['submit'])){
		
		showForm($form, $file);
	 
	} else {
		if($_SESSION['captcha_code'] == md5($_POST['captcha'])){
			if($_POST['name'] == "" or $_POST['nachricht'] == ""){
				$_POST['info_msg'] = '<p class="alert alert-danger">'. $sgs['error'].'<br>'. $sgs['msg'] . '</p>';
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
					$_POST['info_msg'] = '<p class="alert alert-success">'.$l['msg_successful_send'].'</p>';
					if($user['mail_msg'] == 1){
						mail($user['email'], $l['new_gb_msg'], $nachricht);
					}
					showForm($form, $file);
				} else {
					$_POST['info_msg'] = '<p class="alert alert-danger">'.$l['error_msg_dont_send'];
					$_POST['info_msg'] .=  mysqli_error($conn).'</p>';
				}
			}
		} else {
			$_POST['info_msg'] = '<p class="alert alert-danger">'.$l['error_wrong_captcha'].'</p>';
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
	$l = getLanguage();
	$where = getCondition();
	if(isset($_GET['page'])){
		$page = (int)$_GET['page'];
	}
	$offset = ($page - 1) * $sgs['posts'];
	$abfrage = "SELECT * FROM hh_gbook $where AND gb = $gbid ORDER BY id DESC LIMIT $sgs[posts] OFFSET $offset";
	$abfrage_antwort = mysqli_query($conn, $abfrage);
	
	if ( ! $abfrage_antwort )
	{
	  die($l['bad_query'].': ' . mysqli_error($conn));
	} 
	
	while ($zeile = mysqli_fetch_assoc($abfrage_antwort))
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
	global $sgs,$form,$conn,$user; 
	$myCond = getCondition();
	$l = getLanguage();
	$q = "SELECT count($sgs[id]) FROM hh_gbook $myCond";
    $count = mysqli_query( $conn, $q );
	$count = mysqli_fetch_row($count);
	
	$p=1;
	if(isset($_GET['page']) && ((int)$_GET['page'] > 1)){
		$p = ((int)$_GET['page'])-1;
	}
	echo '<div class="row"><div class="col-md-12">
			<center><nav><ul class="pagination pagination-custom">
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
		if($last_page < 2)
			$p = 1;
		else
			$p = 2;
	} else {
		$p = $x-1;
	}
	echo '<li><a href="?page='.$p.'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li></ul></nav></center></div></div>';
}
?>