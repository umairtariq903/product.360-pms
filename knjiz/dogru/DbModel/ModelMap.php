<?php

/**
 * @property DbField[] $DbFields
 * @property Relation[] $Relationships Modelin başka nesnelerle kurduğu alt üst ilişkisini tanımlayan dizi
 */
abstract class ModelMap
{
	public $Name;
	public $ModelName;
	public $TableAlias;
	/**
	 * Bu modelde
	 * - Tanımlı alanlarla (property),
	 * - Gerçek tablo alanlarının (fields),
	 * - Eşleştirilmesi (mapping)
	 * - İle ilgili tanımları (DbField) içeren dizimiz
	 */
	protected $DbFields;

	protected $Relationships = array();
	private $__DbFields = NULL;
	private $__Relationships = NULL;

	public function __get($name) {
		if($name == 'DbFields'){
			if($this->__DbFields === NULL){
				// alanları oluştur
				$a = array();
				foreach($this->DbFields as $property => $array)
					$a[$property] = DbField::Init($array);
				$model = new $this->ModelName;
				/* @var $model ModelBase */
				$pk = $a[$model->GetDb()->idName];
				$regs = array();
				if ($pk && preg_match("/([a-z0-9_]+)\.$pk->Name/", $pk->FieldName, $regs))
					$this->TableAlias = $regs[1];
				$this->__DbFields = $a;
			}
			return $this->__DbFields;
		}

		// İlişkiler
		if($name == 'Relationships'){
			if($this->__Relationships === NULL){
				$a = array();
				foreach($this->Relationships as $array)
				{
					$field = $array['access_field'];
					$rvField = @$array['reverse_field'];
					$conditions = array(
						$array['condition'],
						@$array['condition2'],
						@$array['condition3']);
					$type = $array['type'];
					$behaviour = $array['behaviour'];
					$parents = $children = $operators = array();
					$otherModel = '';
					foreach($conditions as $cond)
					{
						$regs = array();
						if (! preg_match("/([^=!<>]*)([=!<>]+)(.*)/i", $cond, $regs))
							continue;
						$parents[] = DbField::Init(
							array("type" => VarTypes::INT,
								  "model" => $this->ModelName,
								  "field" => trim($regs[3])));
						$operators[] = trim($regs[2]);
						$otherField = $regs[1];
						if (preg_match("/(.*)\.(.*)/i", $otherField, $regs))
						{
							$otherModel = $regs[1];
							$otherField = $regs[2];
						}
						$children[] = DbField::Init(
							array("type" => VarTypes::INT,
								  "model" => trim($otherModel),
								  "field" => trim($otherField)));
					}
					$a[$field] = new Relation($parents, $children, $operators, $field, $rvField, $type, $behaviour);
					$a[$field]->ViewName = IfNull($array, 'view_name');
				}
				$this->__Relationships = $a;
			}
			return $this->__Relationships;
		}
	}

	/**
	 *
	 * @param string $name
	 * @return bool
	 */
	public function IsRelationalField($name)
	{
		$this->__Relationships = $this->__get('Relationships');
		return key_exists($name, $this->__Relationships);
	}

	/**
	 * Sorguda geçen alan adına göre DbField nesnesini döndürür
	 * @param string $dbFieldName Sorgu içinde geçen alan adı
	 * @return DbField
	 */
	public function GetFieldByName($dbFieldName)
	{
		static $reverseMap = array();
		$fields = $this->__get('DbFields');
		if (! $reverseMap)
			foreach($fields as $name => $obj)
				$reverseMap[$obj->Name] = $name;
		return $fields[ $reverseMap[$dbFieldName] ];
	}

	public function __set($name, $value)
	{
		if ($name == '__DbFields' || $name == '__Relationships')
			$this->{$name} = $value;
	}

	/**
	 * @return static
	 */
	public static function Get()
	{
		static $instance = NULL;
		if ($instance === NULL)
			$instance = new static();
		return $instance;
	}
}