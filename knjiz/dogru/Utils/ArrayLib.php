<?php
/**
 * diziler üzerinde yapılan çeşitli fonkisyonları barındırır.
 */
define('SORT_DATE_ASC',		10);
define('SORT_DATE_DESC',	11);
class ArrayLib
{
	/**
	 * Verilen object dizisinde verilen propName değerlerinin dizisini dönderir
	 * @param Object[] $objArray
	 * @param string $propName
	 * @param mixed $defaultVal belirtilen özellik bulunmaması durumunda
	 * kullanılacak değer
	 * @return array
	 */
	public static function ObjPropertyList($objArray, $propName, $defaultVal = '', $preserveKeys = false)
	{
		$a = array();
		$i = 0;
		foreach($objArray as $key => $obj)
		{
			if (!$preserveKeys)
				$key = $i++;
			if (@$obj->{$propName})
				$a[$key] = $obj->{$propName};
			else
				$a[$key] = $defaultVal;
		}
		return $a;
	}

	public static function ObjPropertyKeyName($objArray, $keyName, $propName = NULL)
	{
		$a = array();
		foreach($objArray as $obj)
		{
			$key = $obj->{$keyName};
			if (! isset($a[$key]))
				$a[$key] = $propName ? $obj->{$propName} : $obj;
		}
		return $a;
	}

	/**
	 * Verilen object dizinde $itemKeys de belirtilen key lere ait objelerin
	 * $prop alanlarını $value olarak atar
	 * @param string $itemKeys , ile ayrılmış keyler
	 * @param string $prop atama yapılacak proparty adı
	 * @param mixed $value atanacak olan değer
	 * @return ObjectArray
	 */
	public static function SetValues($array, $prop, $itemKeys, $value)
	{
		$items = explode(',', $itemKeys);
		foreach($items as $key)
			if(key_exists($key, $array))
				$array[$key]->{$prop} = $value;
		return $array;
	}

	/**
	 *
	 * @param object[] $array Object dizisi
	 * @param type $values Değiştirilecek elemanlar, örn: array('Tur' => 2)
	 * @return object[]
	 */
	public static function SetPropValues($array, $values)
	{
		foreach($array as $obj)
			foreach($values as $name => $val)
				$obj->{$name} = $val;
		return $array;
	}

	/**
	 *  Verilen dizi içindeki ifadeleri virgül ve "ve/veya" ifadesi ile
	 * birleştirir
	 * @param array $array
	 */
	public static function ImplodeStr($array, $sep = ',', $lastSep = 've')
	{
		$last = array_pop($array);
		if (count($array) > 0)
			return implode($sep, $array) . " $lastSep " . $last;
		else
			return strval($last);
	}

	/**
	 * Bir dizi objeyi barındıran değişken için verilen
	 * kriterlere uyan ilk objeyi geri döndürür
	 * Ör: SearchObj($array, array('FirmaId' => 23))
	 *	   FirmaId özelliği 23 olan ilk objeyi döndürür
	 * @param array $array
	 * @param array $criteria
	 * @param bool $returnAll TRUE verilirse, şarta uyan tüm nesneleri dizi olarak döndürür
	 */
	public static function SearchObj($array, $criteria, $returnAll = false)
	{
		// Kriter = "a=b,c=2,d=..." formatında verilmişse
		if (is_string($criteria))
		{
			$parts = explode(",", $criteria);
			$criteria = array();
			foreach($parts as $part)
			{
				$keys = explode('=', $part);
				$criteria[$keys[0]] = $keys[1];
			}
		}
		$all = array();
		foreach($array as $obj)
		{
			$flag = true;
			foreach($criteria as $propName => $propValue)
			{
				$val = is_object($obj) ? $obj->{$propName} : $obj[$propName];
				if ($val != $propValue)
					$flag = false;
			}
			if ($flag && !$returnAll)
				return $obj;
			else if ($flag)
				$all[] = $obj;
		}
		if ($returnAll)
			return $all;
		return null;
	}

	public static function SearchObjList($array, $criteria)
	{
		return self::SearchObj($array, $criteria, TRUE);
	}

	public static function SearchObjCount($array, $criteria)
	{
		return count(self::SearchObjList($array, $criteria));
	}

	/**
	 * Verilen obje listesi üzerinde verilen kriterlere göre sıralama yapar.<br>
	 * <b>Örnek:</b><br>
	 * CustomSort($list, 'Ad', SORT_DESC) <br>
	 * CustomSort($list, array('Ad' => SORT_ASC, 'Soyad' => SORT_DESC))
	 * @param array $list
	 * @param array $comparison Karşılaştırma için kullanılacak alan ve sıralama türü
	 */
	public static function CustomSort(&$list, $comparison, $sortType = SORT_ASC)
	{
		if(!is_array($comparison))
			$comparison = array($comparison => $sortType);
		usort($list, function($obj1, $obj2) use ($comparison){
			foreach ($comparison as $key => $value)
			{
				if(is_array($obj1))
				{
					$v1 = $obj1[$key];
					$v2 = $obj2[$key];
				}
				else
				{
					$v1 = $obj1->{$key};
					$v2 = $obj2->{$key};
				}
				if ($value == SORT_DATE_ASC || $value == SORT_DATE_DESC)
				{
					$v1 = Tarih::StrToTime($v1);
					$v2 = Tarih::StrToTime($v2);
				}
				if ($value == SORT_ASC || $value == SORT_DATE_ASC)
				{
					if ($v1 < $v2)
						return -1;
					if ($v1 > $v2)
						return 1;
				}
				if ($value == SORT_DESC || $value == SORT_DATE_DESC)
				{
					if ($v1 > $v2)
						return -1;
					if ($v1 < $v2)
						return 1;
				}
			}
			return 0;
		});
		return $list;
	}

	public static function ArraySpliceItem(&$array,$key,$length = 1){
		//unset($array[$key]);
		array_splice($array,$key,$length);
	}
}
