<?php

require_once __DIR__ . '/../conf/functions.php';

handle_errors();
if (!configured()) {
    fail(404, 'Guestbook not configured.');
}
start_session();
$alphabet = '2345789ABCDEFHKL';
$code = '';
for ($i = 0; $i < 6; $i++) {
    $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
}
$_SESSION['hhgb_captcha'] = ['hash' => hash('sha256', $code), 'expires' => time() + 300];
session_write_close();

$image = imagecreatefrompng(__DIR__ . '/pozadina.png');
$font = __DIR__ . '/Anke_Print.TTF';
$colors = [imagecolorallocate($image, 0, 0, 0), imagecolorallocate($image, 127, 0, 0), imagecolorallocate($image, 1, 29, 127)];
imagettftext($image, 18, 15, 3, 24, $colors[0], $font, substr($code, 0, 2));
imagettftext($image, 16, 0, 33, 17, $colors[1], $font, substr($code, 2, 2));
imagettftext($image, 14, -15, 63, 18, $colors[2], $font, substr($code, 4, 2));

header('Cache-Control: no-store');
header('Content-Type: image/jpeg');
header('X-Content-Type-Options: nosniff');
imagejpeg($image);
