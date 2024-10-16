<?php
require_once 'ModelBase.php';
require_once 'ModelMap.php';
require_once 'ModelDb.php';
class GenericModel extends ModelBase
{
	private $ModelDbInstance = null;

	public function SetDb(ModelDb $db )
	{
		$this->ModelDbInstance = $db;
	}
	public function GetDb($viewName = '')
	{
		if ($this->ModelDbInstance != null)
			return $this->ModelDbInstance;
		return parent::GetDb();
	}
}

class GenericModelMap extends ModelMap
{
	public function __construct($modelIns, $fields)
	{
		$this->Name = get_class($modelIns);
		$this->DbFields = $fields;
		$this->__DbFields = $fields;
		$this->__Relationships = array();
	}
}

class GenericModelDb extends ModelDb
{
	private $ModelName = 'GenericModel';
	private $ModelMapIns= null;
	private $ModelIns = null;
	public function __construct($modelIns, $modelMap, $query)
	{
		$this->ModelName = get_class($modelIns);
		$this->ModelMapIns = $modelMap;
		$this->ModelIns = $modelIns;
		$this->SetSelectQuery($query);
	}

	public function GetModelName()
	{
		return $this->ModelName;
	}

	public function GetModelMap()
	{
		if ($this->ModelMapIns != null)
			return $this->ModelMapIns;
		return parent::GetModelMap();
	}

	/**
	 *
	 * @param string $query
	 * @return ModelBase
	 */
	public static function GetFromQuery($query, $params = array())
	{
		$query = trim($query, ";\r\n\t ");
		$query = "SELECT * FROM ($query) as d WHERE (1=1)";
		$fields = DB::Get()->GetFields($query, true);
		$map = array();
		foreach($fields as $value)
		{
			$map[$value->PhpName] = DbField::InitFromPhpField($value);
			$map[$value->PhpName]->IsReal = 1;
		}
		$modelBase = new GenericModel();
		$modelMap = new GenericModelMap($modelBase, $map);
		$modelDbInstance = new GenericModelDb($modelBase, $modelMap, $query);
		$modelBase->SetDb($modelDbInstance);
		$modelBase->ModelMap = $modelMap;
		return $modelDbInstance;
	}

	public function GetModelInstance($row = NULL)
	{
		return clone $this->ModelIns;
	}
}