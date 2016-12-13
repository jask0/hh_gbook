<?php

function bbCodeToHtml($text) {
	// BBcode array
	$find = array(
		'~\[b\](.*?)\[/b\]~s',
		'~\[i\](.*?)\[/i\]~s',
		'~\[u\](.*?)\[/u\]~s',
		'~\[quote\](.*?)\[/quote\]~s',
		'~\[size=(.*?)\](.*?)\[/size\]~s',
		'~\[color=(.*?)\](.*?)\[/color\]~s',
		'~\[url\]((?:ftp|https?)://.*?)\[/url\]~s',
		'~\[url=((?:ftp|https?)://.*?)\](.*?)\[/url\]~s',
		'~\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]~s',
		'~\[smillie\](.*?\.(?:jpg|jpeg|gif|png|bmp))\[/smillie\]~s'
	);
	// HTML tags to replace BBcode
	$replace = array(
		'<b>$1</b>',
		'<i>$1</i>',
		'<span style="text-decoration:underline;">$1</span>',
		'<pre>$1</'.'pre>',
		'<span style="font-size:$1px;">$2</span>',
		'<span style="color:$1;">$2</span>',
		'<a href="$1" target="_blank">$1</a>',
		'<a href="$1" target="_blank">$2</a>',
		'<img class="img-responsive img-thumbnail" src="$1" alt="" />',
		'<img src="$1" alt="" />'
	);
	// Replacing the BBcodes with corresponding HTML tags
	return preg_replace($find,$replace,$text);
}

function getFormDefault(){
    return Array(
    'name' => '',
    'email' => '',
    'homepage' => '',
    'betreff' => '',
    'bild_url' => '',
    'nachricht' => '',
    'public'=>''
    );
}

function securityCheck($text_old){
    	$text = str_replace('<','-',$text_old);
	$text = str_replace('>','-',$text);
	$text = str_replace(';?>',' ',$text);
	$text = str_replace('<?',' ',$text);
	$text = str_replace('<?php',' ',$text);
	$text = str_replace('DELETE FROM','del from',$text);
	$text = str_replace('INSERT INTO','insert in to',$text);
	$text = str_replace('SET','_set_',$text);
	$text = str_replace('UPDATE','_update_',$text);

	return $text;
}

/* fixes string encoding and prevent code iniaction
*	@param:
*		$string, the string to be fixed
*/
function fixUmlautEntry($string) {
	
	$text = str_replace('ä','&auml;',$string);
	$text = str_replace('ö','&ouml;',$text);
	$text = str_replace('ü','&uuml;',$text);
	$text = str_replace('Ä','&Auml;',$text);
	$text = str_replace('Ö','&Ouml;',$text);
	$text = str_replace('Ü','&Uuml;',$text);
	$text = str_replace('ß','&szlig;',$text);
	$text = securityCheck($text);
	return $text;
}

function isFileExtension($filename, $ext) {
	 if(strtolower(substr($filename, strrpos($filename, '.') + 1))== $ext)
		{return True;}
	else
		{return False;}
}

function testPosition($pos){
    if(strpos($pos, 'hhgb/') !== false){
		return "";
	} else {
		return "hhgb/";
	}
}

function escape_data ($data, $c=NULL) {

if (function_exists('mysqli_real_escape_string')) {
	$data = mysqli_real_escape_string ($c, trim($data));
	$data = strip_tags($data);
} else {
	$data = mysqli_escape_string (trim($data));
	$data = strip_tags($data);
}
return $data;

}

function escape_input($data, $c=NULL, $min=2, $max=255){
	if (preg_match('%^[A-Za-zöäüßÄÖÜ\.\' \-]{'.$min.','.$max.'}$%', stripslashes(trim($data)))) {
		$nd =  escape_data($data, $c);
	} else {
		$nd = FALSE;
	}
	
	return $nd;
}

function escape_email($email, $c=NULL){
	if (preg_match ('%^[A-Za-z0-9._\%-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,4}$%', stripslashes(trim($email)))) {
		$e = escape_data($email, $c);
	} else {
		$e = FALSE;
	}
	
	return $e;
}

function escape_url($url, $c=NULL){
	if (preg_match ('@^(?:http://)?([^/]+)@i', stripslashes(trim($url)))) {
		$e = escape_data($url, $c);
	} else {
		$e = FALSE;
	}
	
	return $e;
}
