<?php
/**
 * Nesneler üzerinde ortak kullanılan işlemleri içerir.
 */
class ObjectLib
{
	/**
	 * Verilen nesnenin Property isimler listesini verir.
	 * @param type $object
	 * @return array
	 */
	public static function GetPropNames($object)
	{
		return array_keys(get_object_vars($object));
	}

	/**
	 * Verilen sınıfa ait, <b>sadece sınıf içinde tanımlı</b> değişken
	 * adlarını döndürür. parent/üst sınıftan miras yoluyla alınan
	 * değişken adları gözardı edilir
	 * @param object $object
	 * @param int $accessType
	 * @return array
	 */
	public static function GetPropNamesInClass($object, $accessType = ReflectionProperty::IS_PUBLIC)
	{
		$reflect = new ReflectionClass($object);
		$props   = $reflect->getProperties($accessType);
		$names = array();
		foreach($props as $prop)
			if ($prop->class == $reflect->getName())
				$names[] = $prop->name;
		return $names;
	}

	/**
	 * Sınıfın içindeki public değişkenleri ve parent nesnedeki @global olarak
	 * işaretlenmiş değişkenlerin adlarını döndürür
	 *
	 * @param object $object
	 * @return array
	 */
	public static function GetGlobalPropNamesInClass($object)
	{
		$reflect = new ReflectionClass($object);
		$props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);
		$names = array();
		foreach($props as $prop)
		{
			/* @var $prop ReflectionProperty */
			$isGlobal = preg_match("/@global/i", $prop->getDocComment());
			if ($prop->class == $reflect->getName() || $isGlobal)
				$names[] = $prop->name;
		}

		return $names;
	}

	/**
	 * Verilen object e $propName property sini büyük-küçük harf duyarsız ayar.
	 */
	public static function GetVal($obj, $propName, $returnIfNotExist = '')
	{
		foreach($obj as $name => $value)
			if (strtolower($name) == strtolower ($propName))
				return $value;
		return $returnIfNotExist;
	}

	/**
	 * $stdObj nesnesi içindeki değişkenleri, $obj nesnesi içine aktarır. $obj
	 * nesnesi içinde olmayan değişkenler es geçilir. <br><br>
	 * <b>Örnek</b>:  <br>
	 *  $obj = (object)[ 'a' => '', 'b' => '']; <br>
	 *  $stdObj = (object) [ 'a' => 'ABC', 'b' => 'XYZ', 'c' => 'TEST']; <br>
	 *  $obj = ObjectLib::SetFromObj($obj, $stdObj); <br> <br>
	 *  işlemleri sonucu $obj nesnesi:  { 'a' => 'ABC', 'b' => 'XYZ'} şekline dönüşür
	 * @param object $obj Değiştirilecek olan nesne
	 * @param object $stdObj Değerlerin alınacağı nesne
	 * @param string[] $names Opsiyonel olarak sadece değiştirilmesi istenen değişken isimleri
	 * @return object verilen $obj nesnesi
	 */
	public static function SetFromObj($obj, $stdObj, $names = null)
	{
		$objKeys = array_keys(get_object_vars($stdObj));
		foreach(array_keys(get_object_vars($obj)) as $name)
		{
			if (! in_array($name, $objKeys))
				continue;
			if ($names && !in_array($name, $names))
				continue;
			$objVar = &$obj->{$name};
			$stdVar = &$stdObj->{$name};
			if (is_object($objVar))
				$objVar = self::SetFromObj($objVar, $stdVar);
			else if (is_array($stdVar))
			{
				$newVar = array();
				foreach(array_keys($stdVar) as $index)
					if (key_exists($index, $objVar) && is_object($objVar[$index]))
						$newVar[$index] = self::SetFromObj($objVar[$index], $stdVar[$index]);
					else if(is_string($stdVar[$index]))
						$newVar[$index] = trim($stdVar[$index]);
					else
						$newVar[$index] = $stdVar[$index];
				$objVar = $newVar;
			}
			else if(is_string($stdVar))
				$objVar = trim($stdVar);
			else
				$objVar = $stdVar;
		}
		return $obj;
	}

	public static function SetFromArray($obj, $array)
	{
		foreach(array_keys(get_object_vars($obj)) as $name)
			if (array_key_exists($name, $array))
				$obj->{$name} = $array[$name];
	}

	/**
	 * verilen $obj nesnesinin istenilen özelliklerini standart nesne olarak verir.
	 * @param object|object[] $obj
	 * @param string[] $props
	 * @return stdClass|stdClass[]
	 */
	public static function GetStdObj($obj, $props, $encodedProps = '')
	{
		$list = array();
		if (is_a($obj, 'ArrayObject'))
			$list = (array)$obj;
		else if (!is_array($obj))
			$list[] = $obj;
		else
			$list = $obj;
		if (is_string($props))
			$props = explode (',', $props);
		if ($props === null)
			$props = array_keys(get_object_vars($list[0]));
		for($i=0; $i<count($list); $i++)
		{
			$s = new stdClass();
			foreach($props as $p)
			{
				$pLeft = $pRight = trim($p);
				$parts = explode('=>', $pLeft);
				if (count($parts) == 2)
				{
					$pRight = $parts[0];
					$pLeft= $parts[1];
				}
				$s->{$pLeft} = $list[$i]->{$pRight};
				if ($encodedProps == $pLeft)
					$s->{$pLeft} = DgrCode::Encode($s->{$pLeft});
			}
			$list[$i] = $s;
		}
		if (is_array($obj) || is_a($obj, 'ArrayObject'))
			return $list;
		return $list[0];
	}

	private static $Singleton = array();
	/**
	 * Verilen sınıftan yeni bir örnek üretip döndürür
	 * @param string $class
	 * @param bool $singleton
	 */
	public static function InitNew($class, $singleton = false)
	{
		if (!$singleton)
			return new $class;
		else
		{
			if (! isset(self::$Singleton[$class]))
				self::$Singleton[$class] = new $class;
			return self::$Singleton[$class];
		}
	}

	/**
	 * Verilen nesnesinin belirtilen özelliklerini diğer nesneye aktarır.
	 * @param array|carray $props
	 */
	public static function AssignProps($to, $from, $props)
	{
		if(is_string($props))
			$props = explode (',', $props);
		if (!is_object($from))
			return $to;
		foreach($props as $p)
			$to->{$p} = $from->{$p};
	}

	/**
	 * Verilen nesnenin istenilen seviyesine kadar kopyasını alır
	 * Bu seviye altında bulunan nesneler referansla aktarılır.
	 * @param object $obj
	 * @param type $level
	 */
	public static function CloneObj($obj, $level = 1, $currLevel = 1)
	{
		static $parents = array();
		if ($currLevel == 1)
			$parents = array();
		$parents[] = $obj;
		$copy = clone $obj;
		foreach($obj as $name => $value)
			if (is_object($value) && !in_array($obj, $parents))
				if($currLevel < $level)
					$copy->{$name} = self::CloneObj($value, $level, $currLevel+1);
				else
					unset($copy->{$name});

		return $copy;
	}

	public static function Cast($destination, $sourceObject)
	{
		if (is_string($destination)) {
			$destination = new $destination();
		}
		$sourceReflection = new ReflectionObject($sourceObject);
		$destinationReflection = new ReflectionObject($destination);
		$sourceProperties = $sourceReflection->getProperties();
		foreach ($sourceProperties as $sourceProperty) {
			$sourceProperty->setAccessible(true);
			$name = $sourceProperty->getName();
			$value = $sourceProperty->getValue($sourceObject);
			if ($destinationReflection->hasProperty($name)) {
				$propDest = $destinationReflection->getProperty($name);
				$propDest->setAccessible(true);
				$propDest->setValue($destination,$value);
			} else {
				$destination->$name = $value;
			}
		}
		return $destination;
	}

	/**
	 * Verilen object'i sınıf tanımındaki PhpDoc metinlerine bakarak
	 * olması gereken nesneye *recursive olarak* cast eder
	 * @param string $destination
	 * @param object $sourceObject
	 * @return object
	 */
	public static function CastDeep($destination, $sourceObject)
	{
		// Önce yüzeysel cast
		$sourceObject = self::Cast($destination, $sourceObject);
		// Sonra her özellk için PhpDoc'a bakarak tekrar cast edilecek
		$reflection = new ReflectionObject($sourceObject);
		$sourceProperties = $reflection->getProperties();
		$regs = array();
		foreach ($sourceProperties as $sourceProperty) {
			/* @var $sourceProperty ReflectionProperty */
			if (preg_match("/@var\s+([^\s]+)/i", $sourceProperty->getDocComment(), $regs))
				$sourceObject->{$sourceProperty->name} =
					self::CastDeep ($regs[1], $sourceObject->{$sourceProperty->name});
		}
		return $sourceObject;
	}

	public static function RemoveProp($obj, $propNames)
	{
		if (is_string($propNames))
			$propNames = explode (',', $propNames);
		foreach($propNames as $p)
			unset($obj->{$p});
	}
}

