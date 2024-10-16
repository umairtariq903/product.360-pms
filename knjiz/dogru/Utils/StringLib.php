<?php
class StringLib
{
	public static $Alfabe = ["A","B","C","D","E","F","G","H","I","İ","J","K","L","M","N","O","Ö","P","R","S","Ş","T","U","Ü","V","Y","Z"];

	/**
	 * "isim soyisim" veya "isim ikinci_isim soyisim"
	 * formatında verilen metni "İsim SOYİSİM" formatına
	 * çevirir
	 */
	public static function AdSoyadFormat($name)
	{
		$name = preg_replace('/\.([^ ])/i', '. $1', $name);
		$parts = explode(' ', $name);

		$parts2= array();
		foreach($parts as $p)
			if (trim($p) != '')
				$parts2[] = $p;

		for($i=0; $i<count($parts2); $i++)
			if ($i < count($parts2) - 1)
				$parts2[$i] = TitleCase($parts2[$i]);
			else
				$parts2[$i] = UpperCase($parts2[$i]);

		return implode(' ', $parts2);
	}

	/**
	 *
	 */
	public static function ShortSurname($adSoyad)
	{
		$parts = explode(' ', $adSoyad);
		$sonuc = '';
		foreach($parts as $ad)
			if($ad != '')
			{
				if($sonuc)
					$sonuc .= mb_substr($ad, 0, 1) .'.';
				else
					$sonuc .= $ad .' ';
			}

		return self::AdSoyadFormat($sonuc);
	}

	/**
	 *
	 */
	public static function ShortName($adSoyad)
	{
		$parts = explode(' ', $adSoyad);
		$sonuc = '';
		foreach($parts as $ad)
			if($ad)
				$sonuc = $sonuc . mb_substr($ad, 0, 1) . '.';
		return UpperCase($sonuc);
	}

	/**
	 * @param int $formatType
	 * 1 : (XXX)XXX-XXXX formatına getirir
	 * -1: XXXXXXXXXX formatına getirir (formatı temizler)
	 */
	public static function CepTelefonFormatla($tel, $formatType = 1)
	{
		// Önce temizle sonra formatla
		$tel = preg_replace("/[^0-9]/", "", $tel);
		if ($tel != '' && $tel[0] == '0')
			$tel = substr($tel, 1);

		// Yeterli sayıda rakam yoksa geriye
		// boş string döndür
		if (strlen($tel) != 10)
			return '';

		if ($formatType == -1)
			return $tel;
		else if ($formatType == 2)
			return preg_replace("/^([0-9]{3})([0-9]{3})([0-9]{4})$/", "($1)$2-$3", $tel);
		else
			return preg_replace("/^([0-9]{3})([0-9]{3})([0-9]{4})$/", "+90 $1 $2 $3", $tel);
	}

	//--------------------------------------------------------
	// Verilen para miktarını yazı olarak döndürür
	// Ör: 10,50 	-> On TL
	// Ör: 1983,25	-> BinDokuzYüzSeksenÜç TL
	//--------------------------------------------------------
	public static function TLToString($amount)
	{
		$limits = array(
			1e9	=> 'MİLYAR',
			1e6	=> 'MİLYON',
			1e3	=> 'BİN',
			1e2	=> 'YÜZ'
		);

		$numbers = array(
			90	=> 'DOKSAN',
			80	=> 'SEKSEN',
			70	=> 'YETMİŞ',
			60	=> 'ALTMIŞ',
			50	=> 'ELLİ',
			40	=> 'KIRK',
			30	=> 'OTUZ',
			20	=> 'YİRMİ',
			10	=> 'ON',
			1	=> 'BİR',
			2	=> 'İKİ',
			3	=> 'ÜÇ',
			4	=> 'DÖRT',
			5	=> 'BEŞ',
			6	=> 'ALTI',
			7	=> 'YEDİ',
			8	=> 'SEKİZ',
			9	=> 'DOKUZ',
			0	=> 'SIFIR',
		);

		$amount = intval($amount);

		if ($amount == 0)
			return 'SIFIR';

		$str = array();

		while($amount > 0)
		{
			if ($amount >= 100)
			{
				foreach($limits as $limit => $limitStr)
				{
					if ($amount > $limit )
					{
						$bolum = intval($amount / $limit);
						$amount -= $bolum * $limit;
						if ($bolum > 10)
							$str[] = StringLib::TLToString($bolum) . $limitStr;
						else if ($bolum > 1 || $limit >= 1e6)
							$str[] = $numbers[ $bolum ] . $limitStr;
						else
							$str[] = $limitStr;
					}
					else if ($amount == $limit)
					{
						$str[] = $limitStr;
						$amount-=$limit;
					}
				}
			}
			else if ($amount <= 10)
			{
				$str[] = $numbers[ $amount ];
				$amount = 0;
			}
			else /* 11 <= amount <= 99 */
			{

				$ilk = intval($amount / 10) * 10;
				$ikinci = $amount - $ilk;

				if ($ikinci > 0)
					$str[] = $numbers[$ilk] . $numbers[$ikinci];
				else if (isset($numbers[$ilk]))
					$str[] = $numbers[$ilk];

				$amount = 0;
			}
		}

		return implode('', $str);
	}

	public static function TekrarsizAdVer($ad)
	{
		$ad = Kodlama::TRCikart($ad);
		$ad = preg_replace('/[^a-z0-9]/i', '_', $ad);
		$ad = strtolower($ad);
		$ad = substr($ad, 0, 15). '-' . (time() - 60 * 60 * 24 * 365 * 40);

		return $ad;
	}

	/**
	 * Verilen iki metnin birbirine benzerliğini sorgular.
	 * Sorgulama sırasında Türkçe karakterler, boşluk, özel karakterler v.b.
	 * göz ardı edilir
	 * @param type $str1
	 * @param type $str2
	 */
	public static function IsSimilar($str1, $str2)
	{
		$str1 = strtolower(Kodlama::TRCikart(trim($str1)));
		$str2 = strtolower(Kodlama::TRCikart(trim($str2)));
		$pattern = "/[^a-z0-9]/i";
		return preg_replace($pattern, '', $str1) == preg_replace($pattern, '', $str2);
	}

	/**
	 *  verilen string i her satırın başındaki fazla boşluk ve tab karakterlerini
	 * temizler, boş satırları siler
	 * @param string $s
	 * @return string
	 */
	public static function RowTrim($s)
	{
		$s = str_replace("\r", '', $s);
		$a = explode("\n", rtrim($s));
		if(count($a) == 1)
			return $s;
		$pre = '';
		$goOn = true;
		foreach($a as $i => $line){
			$line2 = trim($line);
			if($line2 == ''){
				unset($a[$i]);
				continue;
			}
			$c = $line[0];
			if($pre != '' && ($pre != $c || !in_array($c, array(' ', "\n", "\t"))))
			{
				$goOn = false;
				break;
			}
			$pre = $c;
			$a[$i] = substr($a[$i], 1);
		}
		$s1 = implode("\n", $a);
		if($goOn)
			return self::RowTrim($s1);
		else
			return trim($s);
	}

	public static function AddTab($lines, $n = 1)
	{
		if(! is_array($lines))
			$lines = explode("\n", $lines);
		$new = array();
		foreach($lines as $line)
			$new[] = StringLib::AddTab($line, $n);
		return $new;
	}

	public static function AbsTab($lines, $n = 1)
	{
		return self::AddTab(self::RowTrim($lines), $n);
	}

	public static function UcFirst($str)
	{
		$words = ucwords(str_replace(array("_", ','), " ", $str));
		return str_replace(" ", "", $words);
	}

    public static function ToLowerTR($str)
    {
        $str = str_replace(array("İ","I"),array("i","ı"),$str);
        return mb_strtolower($str);
    }

    public static function ToUpperTR($str)
    {
        $str = str_replace(array("i","ı"),array("İ","I"),$str);
        return mb_strtoupper($str);
    }

    public static function UcFirstTurkish($str)
    {
        // İlk harfi büyük harfe çevir
        $firstChar = mb_substr($str,0,1);
        $remainingStr = mb_substr($str, 1, null, "UTF-8");
        $firstChar = self::ToUpperTR($firstChar);
        $remainingStr = self::ToLowerTR($remainingStr);
        return $firstChar . $remainingStr;
    }

	public static function Format($format, $dizi)
	{
		$format = str_replace('\nt', "\n\t", $format);
		foreach($dizi as $value)
			$format = preg_replace("/%s/", $value, $format, 1);
		return $format;
	}

	/**
	 * Dizi içinde verilen $value değerine en çok benzeyen dizi elemanının
	 * anahtar kelimesini döndürür.
	 * @param type $array
	 * @param type $value
	 */
	public static function FindNearest($array, $value, &$simScore = 0)
	{
		$value = strtoupper(Kodlama::TRCikart($value));
		$similarity = -1;
		$sonuc = NULL;
		foreach($array as $key => $val)
		{
			$val = strtoupper(Kodlama::TRCikart($val));
			$s = 0;
			similar_text($value, $val, $s);
			if($s > $similarity)
			{
				$simScore = $similarity = $s;
				$sonuc = $key;
			}
		}
		return $sonuc;
	}

	/**
	 * Verilen metnin içinde sayı dışında tüm karakterleri siler
	 * @param string $str
	 */
	public static function ToInteger($str)
	{
		$str = preg_replace('/[^0-9]/', '', $str);
		if(! $str)
			return 0;
		return $str;
	}

	public static function GetEmailExt($email)
	{
		$pos = strpos($email, '@');
		return $pos === false ? '' : substr($email, $pos + 1);
	}

	public static function Cut($str, $maxLength = 50)
	{
		$newStr = self::SubstrWord($str, $maxLength);
		if(strlen($str) > $maxLength)
			$newStr .= '...';
		return  $newStr;
	}

	//Bu fonksiyon geçici olarak yapılmıştır.Kaldırılacaktır
	public static function SubstrWord($str, $maxLength = 25)
	{
		$parts = preg_split("/[\s]/", strip_tags($str));
		$str = '';
		foreach($parts as $part)
		{
			$str .= "$part ";
			if (strlen($str) >= $maxLength)
				break;
		}
		return $str;
	}

	public static function ClearFontStyle($str)
	{
		$str = preg_replace('/(color\:[^;]*;)/i', '', $str);
		$str = preg_replace('/(font\-family\:[^;]*;)/i', '', $str);
		return preg_replace('/(font\-size\:[^;]*;)/i', '', $str);
	}

	public static function Url($web, $addHttp = true)
	{
		$web = trim($web);
		if (!$web)
			return $web;
		$parts = array();
		$protocol = 'http';
		if (preg_match('#^(http|https)://(.*)#i', $web, $parts))
		{
			$protocol = $parts[1];
			$web = $parts[2];
		}
		return $addHttp ? "$protocol://$web" : $web;
	}
}
