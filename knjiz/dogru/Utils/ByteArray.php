<?php

class ByteArray
{
	/**
	 * $bytes = [11, -126, 2, 127... ] formatındaki byte dizisini
	 * string olarak saklamaya yarayacak şekilde dönüştürür
	 * @param int[] $bytes
	 * @return string
	 */
	public static function Stringify($bytes)
	{
		for($i=0; $i<count($bytes); $i++)
		{
			$num = $bytes[$i];
			if ($num < 0 )
				$num = -$num + 128;
			$hex = base_convert($num, 10, 16);
			$bytes[$i] = str_pad($hex, 2, 0, STR_PAD_LEFT);
		}
		return implode('', $bytes);
	}

	/**
	 * Stringify ile String türüne dönüştürülmüş byte dizisini
	 * eski haline geri döndürür
	 *
	 * @param String $str
	 * @return int[]
	 */
	public static function Parse($str)
	{
		$len = strlen($str);
		if (($len % 2) != 0)
			return 'String düzgün bir şekilde formatlanmamıştır';
		$bytes = array();
		for($i=0; $i<$len; $i+=2)
		{
			$num = substr($str, $i, 2);
			$num = base_convert($num, 16, 10);
			if ($num > 127)
				$num = -1 * ($num - 128);
			$bytes[] = $num;
		}
		return $bytes;
	}

	/**
	 * Verilen byte dizisini veritabanında blob olarak saklamak üzere
	 * string'e çevirir
	 * @param int[] $bytes
	 * @return string
	 */
	public static function ToBlob($bytes)
	{
		return implode(array_map("chr", $bytes));
	}

	/**
	 * Blob olarak kaydedilen string veriyi byte[] olarak döndürür
	 * @param string $str
	 * @param boolean $javaStyleByte Java stili byte kullanımı (-128 ile 127 arası)
	 * @return int[]
	 */
	public static function FromBlob($str, $javaStyleByte = false)
	{
		$len = strlen($str);
		$bytes = array();
		for($i=0; $i<$len; $i++)
		{
			$val = ord($str[$i]);
			if ($javaStyleByte)
				$val = (($val+128) % 256) - 128;
			$bytes[] = $val;
		}
		return $bytes;
	}
}
