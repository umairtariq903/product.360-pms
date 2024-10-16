<?php
class ImapAyar
{
	/** @var string Email sunucu adresi */
	public $Host = '';
	/** @var string Alternatif Email sunucu adresi */
	public $AlternativeHost = '';
	/** @var integer Email sunucu portu */
	public $Port = 995;
	/** @var string bağlantı için gerekli bayraklar */
	public $Flags = '';
	/** @var string @universite.edu.tr uzantısı */
	public $EmailExt = '';
	/** @var boolean bağlantıda tam adres kullanılacak mı */
	public $FullEmail = false;

	public static function Get()
	{
		global $ImapAyar;
		if(! isset($ImapAyar))
			$ImapAyar = new ImapAyar();
		return $ImapAyar;
	}

	public static function Set($host, $port, $flags, $email_ext, $full_email = false, $alternative_host = '')
	{
		$ImapAyar = ImapAyar::Get();
		$ImapAyar->Host = $host;
		$ImapAyar->Port = $port;
		$ImapAyar->Flags = $flags;
		$ImapAyar->EmailExt = $email_ext;
		$ImapAyar->FullEmail = $full_email;
		$ImapAyar->AlternativeHost = $alternative_host;
	}

	public static function SetFromArray($a)
	{
		self::Set($a['host'], $a['port'], $a['flags'], $a['ext'], $a['full'] == '1', $a['ahost']);
	}

	public static function Dogrula($kullanici, $parola, $alternative = 0)
	{
		$ImapAyar = ImapAyar::Get();
		$host = $alternative ? $ImapAyar->AlternativeHost : $ImapAyar->Host;
		if ($ImapAyar->Flags != '')
			$emailKutusuStr = "{" . $host . ":" . $ImapAyar->Port . "/" . $ImapAyar->Flags . "}";
		else
			$emailKutusuStr = "{" . $host . ":" . $ImapAyar->Port . "}";

		if ($ImapAyar->FullEmail)
			$email = $kullanici . ($alternative ? "@$ImapAyar->AlternativeHost" : $ImapAyar->EmailExt);
		else
			$email = $kullanici;
		$imap = imap_open($emailKutusuStr, $email , $parola);

		$errors = imap_errors();

		$success = 0;
        if ($imap)
			$success = 1;

		imap_close($imap);

		// Eğer alternatif bir sunucu varsa onu da kontrol et
		if (! $success && ! $alternative && $ImapAyar->AlternativeHost != '')
			return ImapAyar::Dogrula ($kullanici, $parola, 1);

		//if (!$success && count($errors) > 0 && Debug::$IsAktif)
		//	print_r($errors);

		return $success;
	}
}

