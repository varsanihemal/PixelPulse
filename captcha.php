<?php
session_start();

// Generate a random CAPTCHA string
$captchaString = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 6);

// Store the CAPTCHA string in the PHP session
$_SESSION['captcha'] = $captchaString;

// Generate CAPTCHA image
$captchaImage = imagecreatetruecolor(120, 50);
$bgColor = imagecolorallocate($captchaImage, 255, 255, 255);
$textColor = imagecolorallocate($captchaImage, 0, 0, 0);
imagefilledrectangle($captchaImage, 0, 0, 120, 50, $bgColor);
imagettftext($captchaImage, 20, 0, 10, 35, $textColor, 'arial.ttf', $captchaString);

// Output the CAPTCHA image
header('Content-type: image/png');
imagepng($captchaImage);
imagedestroy($captchaImage);