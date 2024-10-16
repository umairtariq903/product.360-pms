<?php
class DgrCode
{
	public static $Codes = array(
		1 => 'abcdefghijklmnopqrstuvwxyz+/0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
		2 => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz+/'
	);

	public static function MaxKey()
	{
		return max(array_keys(self::$Codes));
	}

	public static function GetCheckSum($s)
	{
		$sum = 0;
		for($i = 0; $i < strlen($s); $i++)
		   $sum += ord($s[$i]);
		return $sum % 100;
	}

	public static function CheckEncoded(&$s, &$eIdx)
	{
		if (gettype($s) != 'string')
			return false;
		$p = explode('_', $s);
		$eIdx = intval($p[0]);
		if(count($p) < 3 || $eIdx < 1 || $eIdx > self::MaxKey())
			return false;
		$sum = intval($p[count($p) - 1]);
		array_shift($p);
		array_pop($p);
		$tmp = implode('_', $p);
		$sum2 = self::GetCheckSum($tmp);
		if($sum == $sum2)
		  $s = $tmp;
		return $sum == $sum2;
	}

	public static function Decode($s, $eIdx = -1)
	{
		if($eIdx <= 0 && !self::CheckEncoded($s, $eIdx))
			return $s;

		$Codes64 = self::$Codes[$eIdx];
		$sonuc = '';
		$a = 0;
		$b = 0;
		for($i = 0; $i < strlen($s); $i++)
		{
			$x = strpos($Codes64, $s[$i]);
			if($x >= 0)
			{
				$b = $b * 64 + $x;
				$a += 6;
				if($a >= 8)
				{
					$a -= 8;
					$x = $b >> $a;
					$b = $b % (1 << $a);
					$x = $x % 256;
					$sonuc .= chr($x + 1);
				}
			}
			else
				return $sonuc;
		}
		return $sonuc;
	}

	public static function Encode($s, $eIdx = -1)
	{
		$i = -1;
		$s = (string)$s;
		if(self::CheckEncoded($s, $i))
			$s = self::Decode($s, $i);
		if($eIdx <= 0)
			$eIdx = self::MaxKey();
		$Codes64 = self::$Codes[$eIdx];
		$sonuc = '';
		$a = 0;
		$b = 0;
		for($i = 0; $i < strlen($s); $i++)
		{
			$x = ord($s[$i]) - 1;
			$b = $b * 256 + $x;
			$a = $a + 8;
			while($a >= 6)
			{
				$a -= 6;
				$x = (int) ($b / (1 << $a));
				$b = $b % (1 << $a);
				$sonuc .= $Codes64[$x];
			}
		}
		if($a > 0)
		{
			$x = $b << (6 - $a);
			$sonuc .= $Codes64[$x];
		}
		return $eIdx . "_" . $sonuc . "_" . self::GetCheckSum($sonuc);
	}
}
