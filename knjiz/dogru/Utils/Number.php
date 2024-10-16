<?php
class Number
{
	public static $DECIMAL_SEPARATOR = '.';
	public static $THOUSAND_SEPARATOR = ',';

	/**
	 * Floating point sayılarda ortaya çıkan
	 * bir sayının tekrar etmesi problemini yuvarlayarak
	 * çözmeye çalışır. Örnek:
	 *   - 4.12		=> 4.12
	 *   - 4.0891	=> 4.0891
	 *   - 4.12999999999999 => 4.13
	 *   - 4.00000000000001 => 4.0
	 * @param decimal $decimal
	 * @return decimal
	 */
	public static function SmartRound($decimal, $format = true, $alwaysDecimal = true)
	{
	  $parts = explode('.', $decimal);
	  if (count($parts) == 1 && $alwaysDecimal == false)
		  return $decimal;
	  $index = 0;
	  $counter = 0;
	  $floating = strval($parts[1]);
	  $last = '';
	  for($i=0; $i<strlen($floating); $i++)
	  {
		 if ($last == $floating[$i])
		 {
			 if ($counter == 1)
				 $index = $i - 1;
			 $counter++;
			 if ($counter >= 4)
				break;
		 }
		 else
			$counter = 0;
		 $last = $floating[$i];
	  }

	   if ($counter >= 4)
		   $decimal = round($decimal, $index);
	   if ($format)
	   {
			if ($index < strlen($floating))
			   $index = strlen($floating);
			else if ($index < 2)
				$index = 2;
			return Number::Format($decimal, $index);
	   }
	   else
		   return $decimal;
	}

	/**
	 * Verilen değerin formatlanmış bir sayı olup olmadığını kontrol eder
	 * ve geriye formatı silerek döndürür
	 * @param string $value
	 */
	public static function UndoFormat($value, $detectedFormat = -1)
	{
		$formats = array();
		$formats[] = array('.', ',', "/^[0-9,]+\.[0-9]{1,}$/");
		$formats[] = array(',', '.', "/^[0-9.]+\,[0-9]{1,}$/");
		if ($detectedFormat == 0 || $detectedFormat == 1)
			$formats = array($formats[$detectedFormat]);
		foreach($formats as $format)
		{
			$decSep = $format[0];
			$thoSep = $format[1];
			$pattern = $format[2];
			if (preg_match($pattern, $value))
				return str_replace(array($thoSep, $decSep),
					array('', '.'), $value);
		}
		return $value;
	}

	/**
	 * Verilen sayıyı number_format tan geçirir (DECIMAL ve THOUSAND separatorlere
	 * bağlı olarak).
	 * @param float $number
	 * @param int $decimals 0 dan küçük verilirse yuvarlatma yapılmaz
	 * @return float
	 */
	public static function Format($number, $decimals = 0, $unit = '')
	{
		// Sadece gerektiğinde (yani sayı tam sayı değilse)
		// ondalık gösterilmek isteniyor:
		if ($decimals < 0)
		{
			$parts = explode('.', $number);
			if (count($parts) == 1)
				$decimals = 0;
			else
				$decimals = strlen($parts[1]);
		}
		return number_format($number, $decimals, self::$DECIMAL_SEPARATOR, self::$THOUSAND_SEPARATOR)
			. ($unit ? " $unit": '');
	}

	/**
	 * Verilen sayıyı number_format tan geçirir (DECIMAL ve THOUSAND separatorlere
	 * bağlı olarak).
	 * @param float $number
	 * @param int $decimals 0 dan küçük verilirse yuvarlatma yapılmaz
	 * @return float
	 */
	public static function FormatDynamic($number, $decimals = 0, $unit = '')
	{
		$parts = explode('.', $number);
		if (count($parts) == 1)
			$decimals = 0;
		else
			$decimals = strlen($parts[1]);

		return number_format($number, $decimals, self::$DECIMAL_SEPARATOR, self::$THOUSAND_SEPARATOR)
			. ($unit ? " $unit": '');
	}

	public static function FormatTL($number, $decimals = 2, $unit = ' TL')
	{
		return self::Format($number, $decimals, $unit);
	}

	public static function FormatXls($number, $decimals = 0)
	{
		return number_format($number, $decimals, '.', '');
	}

	/**
	 * Verilen sayıyı okuması daha kolay hale getirir, gereksiz kusuratları siler
	 */
	public static function ReadableNumber($int)
	{
		if ($int < 1000)
			return $int;
		$int = round($int / 1000);
		if ($int < 1000)
			return $int . ' Bin';
		$int = round($int / 1000);
		if ($int < 1000)
			return $int . ' Milyon';
		return $int;
	}
}
?>
