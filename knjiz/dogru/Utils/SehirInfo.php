<?php
class SehirInfo
{
	public static function Sehirler()
	{
		static $Sehirler = null;
		if ($Sehirler != NULL)
			return $Sehirler;
		return $Sehirler = DgrPack::GetData('sehir_list');
	}

	public static function Ilceler($sehir = '')
	{
		$sehirler = self::Sehirler();
		$ilceler = array();
		foreach($sehirler as $s)
			if($s->Adi == $sehir || !$sehir)
				$ilceler = array_merge($ilceler, explode(',', $s->Ilceler));
		return $ilceler;
	}
}
