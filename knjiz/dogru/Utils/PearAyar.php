<?php
class PearAyar
{
	/** @var boolean mail gönderim için PEAR kullanılcak mı? */
	public $Enabled = true;
	/** @var string Email sunucu adresi */
	public $Host = '';
	/** @var string Email kullanıcı adı */
	public $User = '';
	/** @var string Email şifresi */
	public $Pass = '';
	/** @var boolean kimlik doğrulama yapılacak mı*/
	public $Auth = false;
	/** @var int Sunucunun portu, 0 bırakılırsa varsayılan kullanılacak */
	public $Port = 0;
	/** @var string Sunucu şifreleme metodu ("", "ssl" veya "tls" olmalı) */
	public $SSLMethod = '';

	public static function Get()
	{
		global $PearAyar;
		if(! isset($PearAyar))
			$PearAyar = new PearAyar();
		return $PearAyar;
	}

	public static function Set($host, $user, $pass, $auth = false, $port = 0, $sslMethod = '')
	{
		$pear = PearAyar::Get();
		$pear->Host = $host;
		$pear->User = $user;
		$pear->Pass = $pass;
		$pear->Auth = $auth;
		$pear->Port = $port;
		$pear->SSLMethod = $sslMethod;
	}

	public static function GetHeaderArray($headers)
	{
		// Headers stringini dizi haline getirmemiz grekiyor
		$headerArray = explode("\n", $headers);
		$headers = array();
		foreach($headerArray as $hed)
		{
			if (strpos($hed, ':') === false)
				continue;

			$parts = explode(':', trim($hed));
			if (strtoupper(trim($parts[0])) == 'FROM')
			{
				$fromParts = explode('<', $parts[1]);
				if (strstr($parts[1], "<"))
					$parts[1] = '"' . Kodlama::TRCikart($fromParts[0]) . '"<' . $fromParts[1];
			}
			$headers[trim($parts[0])] = trim($parts[1]);
		}

		if ( in_array('From', array_keys($headers)) && ! in_array('Reply-to', array_keys($headers)))
			$headers['Reply-to'] = $headers['From'];
		return $headers;
	}

	public static function SendEmail($to, $subject, $body, $headers, $printErrors)
	{
		$pear = PearAyar::Get();
		$headers = self::GetHeaderArray($headers, $subject);
		$headers['Subject'] = Kodlama::TRCikart($subject);

		// Host ile ilgili parametreler
		$params = array(
			'host'		=> $pear->Host,
			'username'	=> $pear->User,
			'password'	=> $pear->Pass,
			'auth'		=> $pear->Auth,
			'debug'		=> false
		);

		if ($pear->Port > 0 )
			$params['port'] = $pear->Port;

		// Pear bileşenini yükle
		$path = KNJIZ_DIR . '/others/pear/';
		ini_set('include_path', FullPath($path));
		require_once ('Mail.php');
		$smtp = Mail::factory('smtp', $params);
		$mail = $smtp->send($to, $headers, $body);
		if (PEAR::isError($mail) && $printErrors)
			echo($mail->getMessage() );
		return PEAR::isError($mail);
	}
}

