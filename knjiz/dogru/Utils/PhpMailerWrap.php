<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PhpMailerWrap extends PearAyar
{
	/**
	 * Verilen e-mail adresini iki parçalı olarak döndürür
	 * Örnek: <br>
	 * Ali Veli <ali@veli.com> => array('ali@veli.com', 'Ali Veli') şeklinde
	 * dönüştürülür
	 * @param string $str
	 */
	protected static function GetEmailAddr($str)
	{
		$matches = array();
		if (preg_match("/\"?([^\"]*)\"?.*<(.*)>/", $str, $matches))
			return array($matches[2], Kodlama::TRCikart($matches[1]));
		return array($str, '');
	}

	public static function SendEmail($to, $subject, $body, $headers, $printErrors, $filePath='', $CC='')
	{
		set_time_limit(0);
		require_once KNJIZ_DIR . '/others/PHPMailer2/src/Exception.php';
		require_once KNJIZ_DIR . '/others/PHPMailer2/src/PHPMailer.php';
		require_once KNJIZ_DIR . '/others/PHPMailer2/src/SMTP.php';
//		require_once KNJIZ_DIR . '/others/PHPMailer2/PHPMailerAutoload.php';
		$ayar = self::Get();
		$headers = self::GetHeaderArray($headers);
		//Create a new PHPMailer instance
		$mail = new PHPMailer();
		//Tell PHPMailer to use SMTP
		$mail->isSMTP();

		//Enable SMTP debugging
		// 0 = off (for production use)
		// 1 = client messages
		// 2 = client and server messages
		$mail->SMTPDebug = Debug::$IsAktif ? 2 : 0;
		$mail->Timeout = 1000;
		//Ask for HTML-friendly debug output
		$mail->Debugoutput = 'html';
		//Set the hostname of the mail server
		$mail->Host = $ayar->Host;
		//Set the SMTP port number - likely to be 25, 465 or 587
		if ($ayar->Port > 0)
			$mail->Port = $ayar->Port;
		//Whether to use SMTP authentication
		if ($ayar->Auth)
			$mail->SMTPAuth = true;
		//Set the encryption system to use - ssl (deprecated) or tls
		if ($ayar->SSLMethod)
		{
			$mail->SMTPSecure = $ayar->SSLMethod;
			$mail->SMTPOptions = array(
				'ssl' => array(
				'verify_peer' => false,
				'verify_peer_name' => false,
				'allow_self_signed' => true
				)
			);
		}
		//Username to use for SMTP authentication
		$imap = ImapAyar::Get();
		$mail->Username = $ayar->User;
		if (! $imap->FullEmail)
			$mail->Username = str_replace($imap->EmailExt, '', $ayar->User);
		//Password to use for SMTP authentication
		$mail->Password = $ayar->Pass;

		//Set who the message is to be sent from
		$from = self::GetEmailAddr($headers['From']);
		$mail->setFrom($from[0], $from[1]);
		if (key_exists('Reply-to', $headers))
		{
			$replyTo = self::GetEmailAddr($headers['Reply-to']);
			$mail->addReplyTo($replyTo[0], $replyTo[1]);
		}
		$recipients = preg_split("/[,;]/", $to);
		foreach($recipients as $rec)
		{
			$rec = self::GetEmailAddr($rec);
			$mail->addAddress($rec[0], $rec[1]);
		}

		//Set the subject line
		$mail->Subject = $subject;
		//Read an HTML message body from an external file, convert referenced images to embedded,
		//convert HTML into a basic plain-text alternative body
		$mail->msgHTML($body);

		$mail->CharSet = 'utf-8';
		if (App::$Encoding)
			$mail->CharSet = App::$Encoding;

        if (is_array($filePath))
        {
            foreach ($filePath as $pth)
                $mail->addAttachment ($pth);
        }
        else if($filePath != "" && is_file($filePath))
			$mail->addAttachment ($filePath);

        if ($CC != '')
            $mail->addCC($CC);

		//send the message, check for errors
		$sonuc = $mail->send();
		if (! $sonuc && $printErrors)
			echo "Mailer Error: " . $mail->ErrorInfo;
		return $sonuc;
	}
}
