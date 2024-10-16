<?php
/**
 * @property-read string $IdEncoded Debug kapalı olduğunda şifrelenmiş Id
 * @package DbEntity
 */
abstract class ModelBase
{
	/**
	 * Her entity/model bir Id'e sahip olmak zorundadır
	 * @var int
	 */
	public $Id = 0;

	public $WillBeDeleted = 0;

	/**
	 * Her entity/model bir SiraNo'ya sahip olmak zorundadır
	 * @var int
	 */
	public $SiraNo = 0;

	public $WillBeCascadeUpdate = true;

	//ilişkilerde otomatik güncelleme için gerekli
	public $CascadeChanged = false;


	/**
	 * Modelin ait olduğu gerçek fiziksel tablo bilgileri
	 * @var ModelMap
	 */
	protected $ModelMap = NULL;

	/**
	 *  Bu Modelin bağlı bulunduğu ModelDb sınıfının adı
	 *  (Eğer isimlendirme kurallarına uyulursa, bu genelde
	 *   Model+Db|VT olacaktır
	 * @var ModelDb
	 */
	private $ModelDbInstance;

	public function __construct($SetAllVarsToNull = false)
	{
		if($SetAllVarsToNull)
			$this->SetVarsToNull();
	}

	/**
	 * Nesneyi içi boş ModelParam nesnesi olarak verir.
	 * @return static
	 */
	public static function AsParams($defaults = array())
	{
		return ModelParam::Get($defaults);
	}

	public function SetVarsToNull()
	{
		$vars = array_keys(get_object_vars($this));
		foreach($vars as $var)
			$this->{$var} = NULL;
		$rels = array_keys($this->GetModelMap()->Relationships);
		foreach($rels as $field)
			$this->{$field} = new stdClass();
		return $this;
	}


	/**
	 *
	 * @param type $row Veritabanı satırına ait dizi
	 * @return $this|this
	 */
	public function Init($row)
	{
		if(! $row)
			return $this;
		$this->ModelMap = $this->GetModelMap();
		// Sınıf property'lerini set et
		$this->InitPropertyValues($row);

		return $this;
	}

	/**
	 * @return static
	 */
	public static function InitNew($row)
	{
		$o = new static();
		return $o->Init($row);
	}

	public function GetClassName()
	{
		return get_class($this);
	}

	/**
	 * @return ModelMap
	 */
	public function GetModelMap()
	{
		if ($this->ModelMap == NULL)
		{
			$model = $this->GetClassName();
			$ModelMap = $model . 'ModelMap';

			if (class_exists($ModelMap) && in_array('ModelMap', class_parents($ModelMap)))
				$this->ModelMap = CallUserFunc("$ModelMap::Get");
		}
		return $this->ModelMap;
	}

	/**
	 * Fields dizisinde tanımlı alanları row içinde arar ve değer atamalarını yapar
	 *
	 * (Alternatif olarak dizide olmayan ama row içinde gelen değerleri de
	 * değerlendirecek bir mantık geliştirilebilir)
	 * @param type $row
	 */
	protected function InitPropertyValues($row)
	{
		foreach($this->GetModelMap()->DbFields as $property => $field)
			if (array_key_exists($field->Name , $row))
			{
				if ($field->LazyInit == false)
					$this->{$property} = $field->GetTypeObj()->InitProp($row[$field->Name], $this, $field->Name);
				else
					$this->SetValue($property, $row[$field->Name]);
			}
	}

	/**
	 *
	 * @return ModelDb
	 */
	public function GetDb($viewName = '')
	{
		if ($this->ModelDbInstance == NULL)
		{
			$model = $this->GetClassName();
			$alt = array($model . 'Db', $model . 'VT');
			$instance = NULL;

			foreach($alt as $a)
				if (class_exists($a) && in_array('ModelDb', class_parents($a))){
					$instance = CallUserFunc("$a::Get$viewName");
					break;
				}

			/* Eğer instance hala NULL ise ikinci bir tarama daha yapabiliriz:
			 * - Tüm sınıfları alıp, Model tablosu eldeki tabloya eşit olan sınıfı
			 *   bulabiliriz
			 *
			 * Fakat şimdilik burada bıraktık.
			 * (İsimlendirme kurallarına daha çok dikkat etmek herşeyi kolaylaştırıp, hızlandıracak gibi)
			 */

			$this->ModelDbInstance = $instance;
		}
		return $this->ModelDbInstance;
	}

	/**
	 * İlişkili alan üzerinden elde edilen alt kayıtların sayısını verir
	 * (Alt kayıtların kendisini almak için __get kullanılıyor)
	 * @param string $name
	 */
	public function GetChildCount($name)
	{
		if ($this->GetModelMap()->IsRelationalField($name))
		{
			$rel = $this->ModelMap->Relationships[ $name ];
			$conditions = $rel->GetConditions($this);
			$mObj =$rel->GetChildModel();
			/* @var $mObj ModelBase */
			if($conditions)
				return $mObj->GetDb($rel->ViewName)->GetCount($conditions);
		}
		return 0;
	}

	public function __get($name)
	{
		if (isset($this->{$name}))
			return $this->{$name};
		if ($name == 'IdEncoded')
			return $this->IdEncoded = DgrCode::Encode($this->Id);
		$regs = array();
		if (preg_match("/(.*)_Str$/", $name, $regs) && isset($this->{$regs[1]}))
		{
			$map = $this->GetModelMap();
			$col = $map->DbFields[$regs[1]];
			$model = VarTypes::GetInst($col->ModelName);
			if (is_a($model, 'VarListItem'))
				return $this->{$name} = call_user_func(array($model, 'ToStr'), $this->{$regs[1]});
		}
		if ($this->GetModelMap()->IsRelationalField($name))
		{
			$rel = $this->ModelMap->Relationships[ $name ];
			$conditions = $rel->GetConditions($this);
			$mObj =$rel->GetChildModel();
			/* @var $mObj ModelBase */
			if($conditions)
			{
				$oldUseRep = ModelDb::$USE_DB_REPOSITORY;
				if ($this->Id <= 0)
					ModelDb::$USE_DB_REPOSITORY = false;
				$dbObj = $mObj->GetDb($rel->ViewName)->GetList($conditions);
				ModelDb::$USE_DB_REPOSITORY = $oldUseRep;
				// Alt nesnelere de kendi ataması için
				if(count($dbObj) > 0)
					foreach($mObj->GetModelMap()->Relationships as $childRelation)
						if ($childRelation->Type == Relation::ONE_TO_ONE
							&& $childRelation->ChildFields[0]->ModelName == $this->GetClassName()
							&& $childRelation->ReverseField == $rel->AccessField)
						{
							$circularName = $childRelation->AccessField;
							foreach($dbObj as $childObj)
								$childObj->{$circularName} = $this;
							break;
						}
			}
			else
				$dbObj = new ModelBaseArray();
			if ($rel->Type == Relation::ONE_TO_MANY)
				$this->SetValue($name, $dbObj);
			else if ($rel->Type == Relation::ONE_TO_ONE && count($dbObj) > 0)
				$this->SetValue($name, $dbObj[0]);
		}
		else if ($name == 'ModelMap')
			return $this->GetModelMap();
		else
		{
			$fields = $this->GetModelMap()->DbFields;
			$val = $this->GetValue($name);
			if (key_exists($name, $fields) && $fields[$name]->LazyInit && (!is_object($val) && !is_array($val)))
				$this->SetValue($name, $fields[$name]->InitProp($val, $this, $name));
		}
		return $this->GetValue($name);
	}

	/**
	 * Bu nesnenin veritabanındaki orijinal halini döndürür
	 * @return static
	 */
	public function GetOriginalCopy()
	{
		if ($this->Id <= 0)
			return null;
		return $this->GetDb()->GetById($this->Id);
	}

	public function __set($name, $value)
	{
		$this->SetValue($name, $value);
	}

	public function SetFromObj($obj, $probeNaming = false, $includeId = false)
	{
		$params = get_object_vars($obj);
		$idName = $this->GetDb()->idName;
		$map = $this->GetModelMap();
		$vars = array_keys(get_object_vars($this));
		$vars = array_merge($vars, array_keys($map->DbFields));
		foreach($params as $param=>$value)
		{
			if(($param == $idName || $param == 'Id' || $param == 'id' || $param == 'ModelMap') && $includeId == false)
				continue;

			// probeNaming = true ise, var_name = VarName ile eşit olarak algılanacak
			if ($probeNaming)
				foreach($vars as $varName)
					if (strtolower($varName) == strtolower(preg_replace("/[^a-z0-9]/i", "", $param)))
					{
						$param = $varName;
						break;
					}
			$paramVal = $this->{$param};
			if($map && $map->IsRelationalField($param))
			{
				$className = $map->Relationships[$param]->ChildFields[0]->ModelName;
				if($paramVal)
					if(method_exists($paramVal, 'SetFromObj'))
						$paramVal->SetFromObj($value, $className);
					else
						$paramVal = $value;
			}
			else if (in_array($param, $vars))
			{
				$field = null;
				if (array_key_exists($param, $map->DbFields))
					$field = $map->DbFields[$param];
				if ($field && $field->IsSerialized && $field->ModelIsArray && is_array($value))
				{
					if ($paramVal == NULL)
						$this->SetValue($param, new ModelBaseArray());

					$modelName = $field->ModelName;
					$class = $modelName == '' ? 'stdClass' : $modelName;
					if($field->ModelIsArray)
						$this->SetValue($param, $value);
					else
					{
						if(! $paramVal)
							$paramVal = new ModelBaseArray();
						$paramVal->SetFromObj($value, $class);
					}
				}
				else if (is_object($paramVal) && !is_a($paramVal, 'stdClass') && is_object($value))
					$this->SetValue($param, ObjectLib::SetFromObj($paramVal, $value));
				else
					$this->SetValue($param, (is_string ($value) ? trim($value) : $value));
			}
		}// foreach
		return $this;
	}// SetFromObj

	public function SetFromArray($dizi, $WithId = TRUE, $all = false)
	{
		$idName = $this->GetDb()->idName;
		$vars = get_object_vars($this);
		foreach($dizi as $param=>$value)
		{
			if($all)
				$this->{$param} = $value;
			if($all || (!$WithId && ($param == $idName || $param == 'Id')))
				continue;
			$map = $this->GetModelMap();
			$db = $this->GetDb();
			$field = @$map->DbFields[$param];
			if((array_key_exists($param, $vars) && !$map->IsRelationalField($param)
				&& (!$field || (!$field->IsSerialized && !$field->ModelIsArray)))
			   || $db->IsValidCustomParam($param, $value))
					$this->{$param} = $value;
		}// foreach
		return $this;
	}

	public function Save($control = ModelDb::SAVE_WITH_CONTROL)
	{
		return $this->GetDb()->Save($this, $control);
	}

	/**
	 * Kontrol yaparak Kaydet
	 */
	public function Save_WiC()
	{
		return $this->Save(ModelDb::SAVE_WITH_CONTROL);
	}

	/**
	 * Kontrol YAPMADAN Kaydet
	 */
	public function Save_WoC()
	{
		return $this->Save(ModelDb::SAVE_WITHOUT_CONTROL);
	}

	/**
	 * Kaydetmek için sadece kontrol
	 */
	public function Save_Ctrl()
	{
		return $this->Save(ModelDb::SAVE_CONTROL_ONLY);
	}

	public function Delete($control = ModelDb::DELETE_WITH_CONTROL)
	{
		return $this->GetDb()->Delete($this, $control);
	}

	/**
	 * Kontrol yaparak Sil
	 */
	public function Delete_WoC()
	{
		return $this->Delete(ModelDb::DELETE_WITHOUT_CONTROL);
	}

	/**
	 * Kontrol YAPMADAN Sil
	 */
	public function Delete_WiC()
	{
		return $this->Delete(ModelDb::DELETE_WITH_CONTROL);
	}

	/**
	 * silme yapmadan sadece kontrol
	 */
	public function Delete_Ctrl()
	{
		return $this->Delete(ModelDb::DELETE_CONTROL_ONLY);
	}

	public function GetValue($name)
	{
		return @$this->{$name};
	}

	public function SetValue($name, $value)
	{
		$this->{$name} = $value;
	}

	public function GetParams()
	{
		$params = new ConditionList();
		$vars = array_keys(get_object_vars($this));
		foreach($vars as $var){
			$val = $this->{$var};
			if($val != NULL)
				if(is_string($val))
					$params->Add($var, OPRT::EQ, $val);
				else if(is_numeric($val))
					$params->Add($var, OPRT::EQ_NUM, $val);
				else if(is_array($val))
					foreach($val as $cond => $value)
						$params->Add($var, $cond, $value);
		}

		return $params;
	}

	public function DeleteAllRelationItem($rltnName)
	{
		if ($this->GetModelMap()->IsRelationalField($rltnName))
		{
			$rel = $this->ModelMap->Relationships[ $rltnName ];
			$conditions = $rel->GetConditions($this);
			$mObj = $rel->GetChildModel();
			/* @var $mObj ModelBase */
			if($conditions)
				$mObj->GetDb()->DeleteAllRelationItem($mObj, $conditions);
		}
		else
			ThrowException("Böyle bir ilişki bulunamadı($rltnName)");
	}

	public function UpdateAllRelationItem($rltnName, $fieldValues)
	{
		if ($this->GetModelMap()->IsRelationalField($rltnName))
		{
			$rel = $this->ModelMap->Relationships[ $rltnName ];
			$conditions = $rel->GetConditions($this);
			$mObj = $rel->GetChildModel();
			/* @var $mObj ModelBase */
			if($conditions)
				$mObj->GetDb()->UpdateAllRelationItem($mObj, $conditions, $fieldValues);
		}
		else
			ThrowException("Böyle bir ilişki bulunamadı($rltnName)");
	}

	/**
	 * Nesneyi ilişkili dizi olarak döndürür (DB'den gelen $row nesnesine benzer)
	 * @return array
	 */
	public function ToArray()
	{
		$row = array();
		foreach($this->GetModelMap()->DbFields as $property => $field)
		{
			$fieldName = preg_replace("/^(\w+\.)/i", "", $field->FieldName);
			$row[ $fieldName ] = $this->{$property};
		}
		return $row;
	}

	public function ToStdObj($fields = null, $regExp = false)
	{
		$obj = new stdClass();
		$originalKeys = array_keys($this->GetModelMap()->DbFields);
		if ($fields != null && $regExp)
		{
			if (is_array($fields))
				$fields = implode('|', $fields);
			else
				$fields = str_replace (',', '|', $fields);
			$keys = array();
			foreach($originalKeys as $property)
				if (preg_match("/^($fields)$/i", $property))
					$keys[] = $property;
			$fields = $keys;
		}
		else if ($fields != null && is_string($fields))
			$fields = explode(',', $fields);
		else
			$fields = $originalKeys;
		foreach($fields as $property)
			$obj->{$property} = $this->{$property};
		return $obj;
	}
/*
	public function __sleep()
	{

	}
 */
}
