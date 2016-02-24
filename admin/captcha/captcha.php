<?php
//pokretanje session
session_start();
//brisanje cookie
unset($_SESSION['captcha_code']);

//headeri, koji sprecavaju cache naseg sadrzaja
header( "Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header( "Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT" );
header( "Cache-Control: no-store, no-cache, must-revalidate" );
header( "Cache-Control: post-check=0, pre-check=0", false );
header( "Pragma: no-cache" );

//header koji kaze ovo je slika
header('Content-type: image/jpeg');

//stvaranje koda
$skupZnakova = "2345789ABCDEFHKL";
$podStr1 = substr(str_shuffle($skupZnakova), 0, 2);
$podStr2 = substr(str_shuffle($skupZnakova), 0, 2);
$podStr3 = substr(str_shuffle($skupZnakova), 0, 2);
$stringCode = $podStr1.$podStr2.$podStr3;

//memorisanje coda u cookie
$_SESSION['captcha_code'] = md5($stringCode);

//stvaranje grafika

//lodiranje fonta
$fontArray = array("Anke_Print.TTF","ASSIMILA.TTF","AUTOMOBI.TTF");

//lodrianje memorije za sliku
$Slika = imagecreatefrompng("pozadina.png");

//stvaranje boje
$fontBoja1 = imagecolorallocate($Slika, 0, 0, 0); //crna
$fontBoja2 = imagecolorallocate($Slika, 127, 0, 0);  //tamno crvena
$fontBoja3 = imagecolorallocate($Slika, 255, 0, 220);  //pink

//pisanje coda u sliku
imagettftext($Slika, 18, 15, 3, 24, $fontBoja1, $fontArray[0], $podStr1);
imagettftext($Slika, 16, 0, 35, 15, $fontBoja2, $fontArray[0], $podStr2);
imagettftext($Slika, 14, -15, 63, 18, $fontBoja3, $fontArray[0], $podStr3);

//pakovanje slike
imagejpeg($Slika);

//unistavanje slika i oslobadjanje memorije
imagedestroy($Slika);
?>