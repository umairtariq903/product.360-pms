<?php
class Mailer
{
	const T_PHP_MAIL = 0;
	const T_PEAR_MAIL = 1;
	const T_PHPMAILER = 2;

	public static $TYPE = self::T_PHP_MAIL;

	public static function UsePear($host, $user, $pass, $auth = false, $port = 0)
	{
		self::$TYPE = self::T_PEAR_MAIL;
		PearAyar::Set($host, $user, $pass, $auth, $port);
	}

	public static function UsePhpMailer($host, $user, $pass, $auth = false, $port = 0, $sslMethod = '')
	{
		self::$TYPE = self::T_PHPMAILER;
		PhpMailerWrap::Set($host, $user, $pass, $auth, $port, $sslMethod);
	}

	public static function SetFromArray($a)
	{
		if (! is_array($a) || count($a) == 0)
			return;
		self::UsePhpMailer($a['host'], $a['user'], $a['pass'], $a['auth'], intval($a['port']), $a['sslm']);
	}

	public static function Send($to, $subject, $body, $headers = '', $printErrors = true, $filePath = '', $SITE_ADI_ALT = "", $CC = "")
	{
		global $SiteEmailFrom, $SITE_ADI;
		if (!$SiteEmailFrom)
			$SiteEmailFrom = Config('app.MAIL_FROM');
		if (!$SiteEmailFrom)
			$SiteEmailFrom = @$GLOBALS['DGR_PROJE_MAIL'];
		if (! $SITE_ADI)
			$SITE_ADI = App::$Kod;
		if ($SITE_ADI_ALT != "")
		    $SITE_ADI = $SITE_ADI_ALT;
		$emailFrom = "$SITE_ADI <$SiteEmailFrom>";

		if($headers == '')
			$headers = "To: $to\nFrom: $emailFrom\nContent-type: text/html; charset=utf-8\n";

		if (App::$Encoding)
			$headers = preg_replace ("/charset=([a-z0-9-]+)/i", "charset=" . App::$Encoding, $headers);

		switch(self::$TYPE)
		{
			case self::T_PEAR_MAIL:
				$sonuc = PearAyar::SendEmail($to, $subject, $body, $headers, $printErrors);
				break;
			case self::T_PHPMAILER:
				$sonuc = PhpMailerWrap::SendEmail($to, $subject, $body, $headers, $printErrors, $filePath, $CC);
				break;
			default:
				$sonuc = @mail($to, $subject, $body, $headers);
				break;
		}
		return $sonuc;
	}
}
