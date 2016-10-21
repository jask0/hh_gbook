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
    	// BBcode array
	$find = array(
		'<',
		'>',
		';?>',
		';?>',
		'<?',
		'<?php',
		'DELETE FROM',
		'INSERT INTO',
		'~UPDATE(*.?)SET(*.?)WHERE~s'
	);
	// HTML tags to replace BBcode
	$replace = array(
		'-',
		'-',
		'',
		'',
		'',
		'',
		'del from',
		'insert in to',
		''
	);
	// Replacing the BBcodes with corresponding HTML tags
	return preg_replace($find,$replace,$text_old);
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