<?php
include_once('conf/path.php');
$db = loadJson('dbc.json'); // database info
$gbs = getGBsettings(); // gestbook settings / configuration
$gbid = 1; // use for multiple gb

// connection to database
include ('conf/connect.php');

//set gloabl variables
$user = getUserConfig(); // user configuration
$smilie = getSmilies(); // load smilies
$l = getLanguage(); // load language


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


/* loads a json file from config/ directory
*	@param:
*		$json_file, the exact file name of the json file
*/
function loadJson($json_file){
	global $path;
	if(strpos($_SERVER['REQUEST_URI'], 'hhgb/') !== false){
		$file = file_get_contents('conf/'.$json_file);
	}else {
		$file = file_get_contents($path.'hhgb/conf/'.$json_file);
	}

	$data = json_decode($file, true);
	return $data;
}

function getUserConfig(){
	$data = loadJson('user.json');
	return $data;
}

function getCustomCss(){
	$data = loadJson('custom.css.json');
	return $data;
}

function getSmilies(){
	$data = loadJson('smilies.json');
	return $data;
}
/*load settings from gb.json file about the GB
# @param:
#		no param
*/
function getGBsettings($gbid=1){
	$data = loadJson('gb.json');
	return $data[(string)$gbid];
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

function setLanguage($lnag){
	global $l;
	$l = getLanguage($lnag);
}

function fallBackLanguage($l_new){
	global $path;
	if(strpos($_SERVER['REQUEST_URI'], 'hhgb/') !== false){
		$lang = "conf/lang/en.php";
	} else {
		$lang = $path."hhgb/conf/lang/en.php";
	}

	include($lang);
	foreach($l as $key => $value){
		if (!array_key_exists($key, $l_new)) {
			$l_new[$key] = $value;
		}
	}
	return $l_new;
}
/*load language file for the GB
# @param:
# 		$file, if given it will load this specific language
*/
function getLanguage($file=NULL){
	global $user,$gbs,$path;
	if(isset($file)){
		$lang = $path."hhgb/conf/lang/".$file.".php";
		include($lang);
		$l = fallBackLanguage($l);
		return $l;
	}
	
	if(strpos($_SERVER['REQUEST_URI'], 'hhgb/') !== false){
		$lang = "conf/lang/".$user['language'].".php";
	} else {
		$lang = $path."hhgb/conf/lang/".$gbs['language'].".php";
	}

	include($lang);
	$l = fallBackLanguage($l);
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
	global $gbs,$form,$smilie,$l; 
	$data['action'] = $action;
	include('conf/templates/form_template.php');
}

/*display of one post
# @param:
# 		$data ->  	posted data array to fill the value fields
*/
function showPost($data, $edit=0) {
	global $gbs,$form,$smilie,$l;
	if(is_numeric($edit)){
		$edit='hhgb/index.php?page=edit&id='.$data['id'];
	}
	include('conf/templates/post_template.php');
}

/*load meta data like css an javascript
# @param:
# 		no param
# @info:
#		custom css is here
*/
function loadMeta(){
	global $path;
	$css = getCustomCss();
	echo '
<!-- Add jQuery library -->
<script type="text/javascript" src="'.$path.'hhgb/conf/js/jquery.js"></script>
<!-- Bootstrap JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
<!-- Font-Awesome CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="'.$path.'hhgb/conf/css/bootstrap.gb.css">
<!-- Add fancyBox main JS and CSS files -->
<script type="text/javascript" src="'.$path.'hhgb/conf/js/jquery.fancybox.pack.js"></script>
<link rel="stylesheet" type="text/css" href="'.$path.'hhgb/conf/css/jquery.fancybox.css" media="screen" />

<!-- Thumbnails and Smilies JavaScript -->
<script type="text/javascript">
$(document).ready(function() {
		/*
		 *  Simple image gallery. Uses default settings
		 */

		$(\'.fancybox\').fancybox();
	});
	
function insertTheSmiley(input) {
document.getElementById(\'nachricht\').value += input;
}

function putTheSmiley(input) {
window.opener.insertTheSmiley(input);
}
</script>';

	if ($css['use_custom_css'] == 1){
		echo "\n".'<link rel="stylesheet" href="'.$path.'hhgb/conf/css/gb.custom.css">'."\n";
	}
}

/* display submit form and save post to database
# @param:
# 		$file ->  	url to the file where the GB is displayed
*/
function showGBookForm($file){
	global $db,$gbs,$form,$conn,$smilie,$user,$path, $l; 
	$cond = getCondition();
	if(!isset($_POST['submit'])){
		
		showForm($form, $file);
	 
	} else {
		if($_SESSION['captcha_code'] == md5($_POST['captcha'])){
			if($_POST['name'] == "" or $_POST['nachricht'] == ""){
				$_POST['info_msg'] = '<p class="alert alert-danger">'. $gbs['error'].'<br>'. $gbs['msg'] . '</p>';
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
					$nachricht = str_replace(':'.$value.':', '<img src="'.$path.'hhgb/smilies/'.$smilie['set'].'/'.$value.'" alt="('.$value.')">', $nachricht);
				}
				$nachricht = str_replace("'", "\'", str_replace('"', '\"',$nachricht));
				$query = sprintf('INSERT INTO '.$db['table'].' (name,email,homepage,betreff,bild_url,nachricht,public,gb) VALUES ("%s","%s","%s","%s","%s","%s","%s","%s")',$name,$email,$homepage,$betreff,$bild_url,$nachricht,$public,$gbid);
			
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
	global $gbs,$form,$conn,$l,$db; 
	$page = 1;
	$gbid = $gbs['id'];
	$where = getCondition();
	if(isset($_GET['page'])){
		$page = (int)$_GET['page'];
	}
	$offset = ($page - 1) * $gbs['posts'];
	$abfrage = "SELECT * FROM $db[table] $where AND gb = $gbid ORDER BY datum DESC LIMIT $gbs[posts] OFFSET $offset";
	$abfrage_antwort = mysqli_query($conn, $abfrage);
	
	if ( ! $abfrage_antwort )
	{
	  die($l['bad_query'].': ' . mysqli_error($conn));
	} 
	
	while ($zeile = mysqli_fetch_assoc($abfrage_antwort))
	{
		$zeile['datum'] = date_create($zeile['datum']);
		showPost($zeile);
	}
	mysqli_free_result( $abfrage_antwort );
}

function getPostCount($and=''){
	global $db,$gbs,$conn;
	$myCond = getCondition();
	if($and==''){
		$q = "SELECT count($gbs[id]) FROM $db[table] $myCond";
	}else{
		$q = "SELECT count($gbs[id]) FROM $db[table] WHERE public = 0";
	}
    $count = mysqli_query( $conn, $q );
	$count = mysqli_fetch_row($count);
	return $count[0];
}


/* display page navigation of GB
# @param:
# 		no param needed
*/
function showGBpageNavi(){
	global $gbs,$form,$conn,$user,$l; 
	$count = getPostCount();
	
	$p=1;
	if(isset($_GET['page']) && ((int)$_GET['page'] > 1)){
		$p = ((int)$_GET['page'])-1;
	}
	echo '<div class="row"><div class="col-md-12">
			<center><nav><ul class="pagination pagination-custom">
				<li><a href="?page='.$p.'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
	$x = 0;
	$last_page = (($count/$gbs['posts'])+1);
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
