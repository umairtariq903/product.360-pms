<?php

class ModelBaseArray extends ArrayObject
{
	public $Changed = false;

	/**
	 * ModelBase dizisinde Id alanına göre arama yapar.
	 * @return ModelBase
	 */
	public function GetById($id)
	{
		return $this->GetByKey('Id', $id);
	}

	/**
	 * ModelBase dizisinde $key proparty si $value eşit olan elemanı verir
	 * @param string $key
	 * @param mixed $value
	 * @return ModelBase
	 */
	public function GetByKey($key, $value)
	{
		foreach ($this->getArrayCopy() as $obj) {
			if($obj->{$key} == $value)
				return $obj;
		}
		return NULL;
	}

	/**
	 * @param mixed $key
	 * @param mixed $value
	 * @return ModelBaseArray
	 */
	public function FilterByKey($key, $value)
	{
		$copy = new ModelBaseArray();
		$parts = explode('->', $key);
		foreach($this->getArrayCopy() as $obj)
		{
			// Gönderilen "key" içiçe birden fazla
			// ok işareti içerebileceği için
			// ->{$key} yerine döngü kullanıyoruz
			$var = $obj;
			foreach($parts as $prop)
				$var = $var->{$prop};
			if ($var == $value)
				$copy->append($obj);
		}
		return $copy;
	}

	/**
	 * Verilen fonksiyona göre dizi elemanlarını filtreler
	 *
	 * @param Closure|callable $callback Callback fonksiyonu (geriye true|false döndürür)
	 * @return ModelBaseArray
	 */
	public function Filter($callback)
	{
		$array = $this->getArrayCopy();

		return new ModelBaseArray(array_filter($array, $callback));
	}

	/**
	 * @param string $key
	 * @return array
	 */
	public function GetDistinctValues($key, $keyId = '', $includeNullAndEmpty = false)
	{
		$values = array();
		foreach($this->getArrayCopy() as $obj)
		{
			$val = (isset($obj->{$key}) ? $obj->{$key} : NULL);
			if (($val == '' || $val == NULL) && !$includeNullAndEmpty)
				continue;
			if (in_array($val, $values))
				continue;

			if ($keyId == '')
				$values[] = $val;
			else
				$values[$obj->{$keyId}] = $val;
		}

		return $values;
	}

	/**
	 * Veritabanından az sorgu yaparak nesne dizisindeki ilişkinin tek seferde
	 * sorgulanarak oluşturulmasını sağlar
	 * @param type $idName
	 * @param type $modelName
	 */
	public function LoadSubModel($idName, $propName, $modelName)
	{
		$ids = implode(',', $this->GetDistinctValues($idName));
		$params = array('Id' => Condition::FindInSet($ids));
		$list = CallUserFunc("$modelName::Get")->GetList($params);
		/*@var $list ModelBaseArray */
		foreach($this->getArrayCopy() as $obj)
			$obj->{$propName} = $list->GetById($obj->{$idName});
	}

	public function SetFromObj($objList, $className) {
		if(!is_array($objList))
			return $this;
		$this->Changed = true;
		foreach($this->getArrayCopy() as $obj)
			$obj->WillBeDeleted = 1;
		$newArray = array();
		if($objList !== NULL)
			foreach ($objList as $o){
				$obj = null;
				if($o->Id > 0)
					$obj = $this->GetById($o->Id);
				if(! $obj)
					$obj = new $className;
				$obj->SetFromObj($o);
				$obj->WillBeDeleted = 0;
				$newArray[] = $obj;
			}
		foreach($this->getArrayCopy() as $obj)
			if($obj->WillBeDeleted)
				$newArray[] = $obj;
		$this->exchangeArray($newArray);
		return $this;
	}

	public function Save($parent = null, $value = null, $control = ModelDb::SAVE_WITHOUT_CONTROL)
	{
		if(! $this->Changed)
			return 1;
		$dizi = array();
		foreach($this->getArrayCopy() as $obj){
			/*@var $obj ModelBase */
			if($obj->WillBeDeleted)
				$sonuc = $obj->Delete($control);
			else{
				if(isset($parent) && $parent)
					$obj->{$parent} = $value;
				$sonuc = $obj->Save($control);
				$dizi[] = $obj;
			}
			if($sonuc != 1)
				return ThrowException($sonuc);
		}
		$this->exchangeArray($dizi);
		$this->Changed = false;
		return 1;
	}

	public function SetValues($name, $value)
	{
		ArrayLib::SetPropValues($this, array($name => $value));
		return $this;
	}

	public function DeleteAll()
	{
		foreach($this->getArrayCopy() as $obj)
			$obj->Delete_WoC();
		$this->exchangeArray(array());
	}

	/**
	 * verilen alanlardan oluşan bir dizi dönderir. Eğer alan tek ise alan dizisi
	 * dönderir. Birden fazla ise stdClass dizisi dönderir.
	 * @return array|stdClass[]
	 */
	public function GetStdObjArray($fields = 'Id')
	{
		$fields = explode(',', $fields);
		$sonuc = array();
		foreach($this->getArrayCopy() as $obj)
		{
			$s = new stdClass();
			foreach($fields as $f)
				$s->{$f} = $obj->{$f};
			if(count($fields) == 1)
				$sonuc[] = $s->{$f};
			else
				$sonuc[] = $s;
		}
		return $sonuc;
	}

	public function ToStdObj()
	{
		$sonuc = array();
		foreach($this->getArrayCopy() as $obj)
			/* @var $obj ModelBase */
			$sonuc[] = $obj->ToStdObj();
		return $sonuc;
	}

	public function Merge(ModelBaseArray $list)
	{
		$i = $this->count() - 1;
		if($i < 0)
			$i = 1;
		else
			$i = $this[$i]->SiraNo + 1;
		foreach($list as $obj)
		{
			$obj->SiraNo = $i++;
			$this->append($obj);
		}
		return $this;
	}
}

// eskiye uyumluluk için
class DbBaseArray extends ModelBaseArray
{

}
