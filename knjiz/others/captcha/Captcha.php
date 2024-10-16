<?php
if (count($_SESSION) == 0)
	session_start();

class Captcha
{
	public static function GetLastGeneratedWord()
	{
		return $_SESSION['captchaCheck'];
	}

	public static function GenerateCaptcha($width = null, $height = null, $chars = null)
	{
		$w = 150;
		$h = 50;
		$c = 5;

		if(isset($_GET['width'])) { $w = $_GET['width']; }
		if(isset($_GET['height'])) { $h = $_GET['height']; }
		if(isset($_GET['chs'])) { $c = $_GET['chs']; }

		$f = __DIR__."/DejaVuSans.ttf";

		if ($width)
			$w = $width;
		if ($height)
			$h = $height;
		if ($chars)
			$c = $chars;

		$captcha = self::GenerateCaptchaText($c);
		$image = @imagecreate($w, $h)
		    or die('Cannot create main image!');

		$bg_color = imagecolorallocate($image, 255, 255, 255);
		$captcha_color = imagecolorallocate($image, 0, 0, 0);


		$line_color = imagecolorallocate($image, mt_rand(0,255), 0, 255);
		$dots_color = imagecolorallocate($image, mt_rand(0,255),255,mt_rand(0,255));
		$rect_color = imagecolorallocate($image, 0,mt_rand(50,127),50);

		for( $i=0; $i<($w * $h); $i++ ) {
			imagefilledellipse($image, mt_rand(0,$w), mt_rand(0,$h), mt_rand(0,3), mt_rand(0,3), $dots_color);
		}

		for( $i=0; $i<($w + $h)/3; $i++ ) {
			imageline($image, mt_rand(0,$w), mt_rand(0,$h), mt_rand(0,$w), mt_rand(0,$h), $line_color);
		}

		for( $i=0; $i<($w + $h)/3; $i++ ) {
			imagerectangle($image, mt_rand(0,$w), mt_rand(0,$h), mt_rand(0,$w), mt_rand(0,$h), $rect_color);
		}

		$tb = imagettfbbox($h * 0.80, 0, $f, $captcha)
		   or die('Cannot create bounding box in pixels for this TrueType text!');

		$urcX = ($w - $tb[4])/2;
		$urcY = ($h - $tb[5])/2;

		imagettftext($image, mt_rand($h * 0.50,$h * 0.80), 0, $urcX, $urcY, $captcha_color, $f , $captcha)
		   or die('Cannot write the given text  into the image using TrueType font!');

    	imagefilter($image,IMG_FILTER_NEGATE);
	    imagefilter($image,IMG_FILTER_SMOOTH,1);

		$_SESSION['captchaCheck'] = $captcha;

		header('Content-Type: image/jpeg');
		imagejpeg($image);

		imagedestroy($image);
		exit;
	}

	protected static function GenerateCaptchaText($c)
	{
		$captcha = '';
	    $supportedCharacter = array('1','2','3','4','5','6','7','8','9','0','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z');

		for ($i = 1; $i <= $c; $i++) {
			$position = mt_rand(0,sizeof($supportedCharacter) - 1);
			$captcha .= $supportedCharacter[$position];
		}

		return $captcha;
	}
}
