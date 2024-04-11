<?php
session_start();

// Generate a random CAPTCHA code if it's not already generated
if (!isset($_SESSION['captcha'])) {
    $_SESSION['captcha'] = substr(md5(mt_rand()), 0, 6);
}

$image = imagecreatetruecolor(120, 30);
$bgColor = imagecolorallocate($image, 255, 255, 255); // white bg
imagefilledrectangle($image, 0, 0, 120, 30, $bgColor);

$textColor = imagecolorallocate($image, 0, 0, 0);// text black

// Add the CAPTCHA code to the image
imagestring($image, 5, 10, 5, $_SESSION['captcha'], $textColor);

// Output the image as PNG
header("Content-type: image/png");
imagepng($image);

imagedestroy($image);
?>