<?php
class Kodlama
{
	/**
	 * Verilen değişkenin içindeki string ifadelerin karakter kodlaması
	 * düzeltilir ve TR kodlamaya dönüştürülür
	 */
	public static function KarakterKodlamaDuzelt(& $nesne, $yeniSatirDuzelt = true, $quoteDuzelt = true, $richTextDuzelt = false)
	{
		if ($richTextDuzelt)
		{
			Kodlama::KarakterKodlamaDuzelt($nesne, true, false);
			$nesne = trim(str_replace(array("\r\n", "\n", "\t"), array(" ", " ", ""), $nesne));
			$nesne = addslashes($nesne);
		}
		else if (is_object($nesne))
		{
			$vars = get_object_vars($nesne);
			foreach($vars as $name=>$value)
			{
				if (is_string($value))
				{
					$newVal = self::KodlamaDuzelt($value);
					$newVal = self::HtmlDuzelt($newVal, $yeniSatirDuzelt, $quoteDuzelt);
					$nesne->{$name} = $newVal;
				}
				else if (is_array($value) || is_object($value))
				{
					self::KarakterKodlamaDuzelt($value, $yeniSatirDuzelt, $quoteDuzelt);
					$nesne->{$name} = $value;
				}
			}
		}
		else if (is_array($nesne))
		{
			$keys = array_keys($nesne);
			foreach($keys as $name)
			{
				if (is_array($nesne[$name]) || is_object($nesne[$name]))
					self::KarakterKodlamaDuzelt($nesne[$name], $yeniSatirDuzelt, $quoteDuzelt);
				else
				{
					$newVal = self::KodlamaDuzelt($nesne[ $name ]);
					$newVal = self::HtmlDuzelt($newVal, $yeniSatirDuzelt, $quoteDuzelt);
					$nesne[ $name ] = $newVal;
				}
			}
		}
		else if (is_string($nesne))
		{
			$nesne = self::KodlamaDuzelt($nesne);
			$nesne = self::HtmlDuzelt($nesne, $yeniSatirDuzelt, $quoteDuzelt);
		}
	}

	/**
	 * Genel olarak stringin ISO-8859-9 formatında olduğu varsayılarak verilen
	 * stringi UTF8 kodlamaya dönüştürür.
	 */
	public static function UTF8($str, $convertHtmlEntities = false)
	{
		if (is_array($str) || is_object($str))
		{
			foreach($str as &$value)
				$value = self::UTF8($value);
			return $str;
		}
		if ($convertHtmlEntities)
		{
			$table = array(
				'İ'	=> '&#304;',
				'ı'	=> '&#305;',
				'Ö'	=> '&#214;',
				'ö'	=> '&#246;',
				'Ü'	=> '&#220;',
				'ü'	=> '&#252;',
				'Ç'	=> '&#199;',
				'ç'	=> '&#231;',
				'Ğ'	=> '&#286;',
				'ğ'	=> '&#287;',
				'Ş'	=> '&#350;',
				'ş'	=> '&#351;'
			);

			$out = '';
			for($i =0; $i<strlen($str); $i++)
				if (in_array($str[$i], array_keys($table)))
					$out .= $table[$str[$i]];
				else
					$out .= $str[$i];
			return $out;
		}
		else if (mb_check_encoding($str, 'UTF-8'))
			return $str;
		else if (mb_check_encoding($str, 'ISO-8859-9'))
			return mb_convert_encoding($str, 'UTF-8' , 'ISO-8859-9');
		else
			return mb_convert_encoding($str, 'UTF-8');
	}

	/**
	 * Stringin içindeki TR karakterleri en yakın
	 * Latin karakterine dönüştürür ve döndürür
	 */
	public static function TRCikart($str)
	{
		$tr = array('İ', 'ı', 'Ö', 'ö', 'Ü', 'ü', 'Ç', 'ç', 'Ğ', 'ğ', 'Ş', 'ş');
		$en = array('I', 'i', 'O', 'o', 'U', 'u', 'C', 'c', 'G', 'g', 'S', 's');
		return str_replace($tr, $en, $str);
	}

	/**
	 * CPAINT tarafından eklenen \r ve \n karakterlerini, zararlı olabilecek tek
	 * tırnak ve çift tırnak karakterlerini düzeltir
	 */
	public static function HtmlDuzelt($string, $yeniSatir = true, $quotes = true,$tabs=true)
	{
		$rep = array();
		if ($yeniSatir)
		{
			$rep['\\n'] = "\n";
			$rep['\\r'] = "\r";
		}

		if ($quotes)
		{
			$rep["'"]  = '&#39;';
			$rep['"']  = '&quot;';
		}

		if ($tabs)
		{
			$rep['\\t'] = "\t";
		}

		if (count($rep) > 0 )
			return str_replace(
				array_keys($rep),
				array_values($rep),
				$string);
		else
			return $string;
	}

	/**
	 * Verilen stringin kodlamasının UTF veya ISO olup olmamasına bakarak gerekli
	 * dönüşümü yapar ve geriye ISO-8859-9 kodlamasında stringi döndürür
	 */
	public static function KodlamaDuzelt($str, $force = false)
	{
		if (PageController::IsUTF8() && ! $force)
			return $str;
		if (mb_check_encoding($str, 'UTF-8'))
			return mb_convert_encoding($str, 'ISO-8859-9', 'UTF-8');
		if (mb_check_encoding($str, 'ISO-8859-9'))
			return $str;
		return mb_convert_encoding($str, 'ISO-8859-9');
	}

	/**
	 * verilen değeri JSON stringine dönüştürür
	 */
	public static function JSON($nesne, $htmlDuzelt = true, $cloneObject = false)
	{
		if ($cloneObject && is_object($nesne))
			$nesne = ObjectLib::CloneObj($nesne, 2);
		self::ClearRecursion($nesne);
		self::UTF8Donustur($nesne, $htmlDuzelt, false, array());
		return $sonuc = json_encode($nesne);
//		Bazı yerlerde aşağıdaki kod problem oldu. Bundan dolayı eski haline getirildi.
//		return str_replace(
//			array('\\n','\\r','\\t'),
//			array('\\\\n','\\\\r','\\\\t'), $sonuc);
	}

	public static function ClearRecursion($obj, $list = array())
	{
		if(! is_object($obj))
			return;
		$list[] = $obj;
		// PHP 5.5 altında foreach-referans kullanımı problem çıkarıyor
		foreach($obj as $index=>$value)
			if(is_object($value))
			{
				if(in_array($value, $list))
					$obj->{$index} = null;
				else
					self::ClearRecursion($value, $list);
			}
	}

	public static function JSONTryParse($str)
	{
		$obj = json_decode($str);
		if (json_last_error() != JSON_ERROR_NONE)
			return null;
		if (! PageController::IsUTF8())
			self::KarakterKodlamaDuzelt($obj, false, false, false);
		return $obj;
	}

	public static function JsonVar($jsVarName, $item, $isHtml = false)
	{
		$json = addslashes(Kodlama::JSON($item));
		$json = $json ? "JSON.parse('$json')" : "''";
		if (!$isHtml)
			$json = 'String.DecodeEntities(' . $json . ')';
		return "$jsVarName = $json;";
	}

	public static function UTF8Donustur(&$nesne, $htmlDuzelt = true, $newLineDuzelt = false, $parents = array())
	{
		$oldUseRep = ModelDb::$USE_DB_REPOSITORY;
		ModelDb::$USE_DB_REPOSITORY = TRUE;
		if (is_array($nesne) || $nesne instanceof ModelBaseArray)
		{
			if (is_array($nesne) )
				$keys = array_keys($nesne);
			else
				$keys = array_keys($nesne->getArrayCopy());
			foreach($keys as $name)
			{
				$val = $nesne[$name];
				unset($nesne[$name]);
				$name = Kodlama::UTF8($name);
				$nesne[$name] = $val;
				self::UTF8Donustur($nesne[$name], $htmlDuzelt, $newLineDuzelt, $parents);
			}
		}
		else if (is_object($nesne))
		{
			$parents[] = $nesne;
			$vars = array_keys(get_object_vars($nesne));
			foreach($vars as $name)
				if (is_object($nesne->{$name}) && in_array($nesne->{$name}, $parents))
					$nesne->{$name} = null;
				else
					self::UTF8Donustur($nesne->{$name}, $htmlDuzelt, $newLineDuzelt, $parents);
		}
		else if (is_string($nesne))
		{
			$nesne = self::UTF8($nesne);
			if ($htmlDuzelt)
				$nesne = self::HtmlDuzelt($nesne);
			if ($newLineDuzelt)
				$nesne = str_replace(
					array("\r", "\n", "\t"),
					array("\\r", "\\n", "\\t"), $nesne);
		}
		ModelDb::$USE_DB_REPOSITORY = $oldUseRep;
	}
}