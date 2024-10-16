<?php
/**
 * ModelBase aramalarında kullanılması için.
 * Bu sınıf direk olarak kullanılmayacak. ModelBase'den AsParams fonksiyonuyla
 * oluşturulur.
 */
class ModelParam
{
	/**
	 * @return ModelParam
	 */
	public static function Get($defaults = array())
	{
		$inst = new ModelParam();
		foreach($defaults as $key => $value)
			if($value !== NULL)
				$inst->{$key} = $value;
		return $inst;
	}

	public function __get($name)
	{
		// Olmayan bir parametre okunmak istendiğinde bunu da ModelParam nesnesi
		// olarak oluşturuyoruz.
		return $this->{$name} = new ModelParam();
	}
}
