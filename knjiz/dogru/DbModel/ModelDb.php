<?php

abstract class ModelDb
{
	/**
	 * Silme prosedürü ile ilgili sabitler
	 */
	const DELETE_WITHOUT_CONTROL	= 0;
	const DELETE_CONTROL_ONLY		= 1;
	const DELETE_WITH_CONTROL		= 2;

	/**
	 * Kaydetme prosedürü ile ilgili sabitler
	 */
	const SAVE_WITHOUT_CONTROL	= 0;
	const SAVE_CONTROL_ONLY		= 1;
	const SAVE_WITH_CONTROL		= 2;

	/**
	 * Sorgu üretme prosedürü ile ilgili sabitler
	 */
	const QUERY_RETURN_LIST			= 0;
	const QUERY_RETURN_COUNT		= 1;
	const QUERY_RETURN_SUMMARY		= 2;

	/**
	 * Object veya array olarak verilen değerleri string türünden bir alana
	 * yazarken kullanılacak serileştirme türleri
	 */
	const SERIALIZE_PHP		= 0;
	const SERIALIZE_JSON	= 1;

	public static $SumQuery = '';
	/**
	 * Db operasyonlarını gerçekleştiren sınıfımızın hangi modeli
	 * baz aldığını gösteren değer. Boş bırakılırsa sınıf adından tahmin edilir.
	 * @var string
	 */
	private $ModelName = NULL;

	/**
	 * Sorguyu çalıştırırken baz alınacak SELECT Sorgusu
	 *
	 * @var string
	 */
	protected $_SelectQuery = "";

	/**
	 * Çalıştırılan bir sorguya ait özet bilgi (aggregate) hesaplaması için
	 * kullanılacak SQL ifadesi
	 *
	 * @var string
	 */
	private $_SummaryFields = "COUNT(*)";

	public $OrderByFieldsId = 1;
	public $OrderByFields = array(1 => '1');
	public $OrderByFieldCustomStr = '';
	public $GroupByFieldCustomStr = '';
	public $idName = 'Id';
	public $IsDbRecord = true;
	public $PreCalculatedSum = null;
	public $CustomParamOperators = array();
	public $SerializationType	= self::SERIALIZE_PHP;

	/**
	 *
	 * @var bool GetList lerde her seferinde veritabanından ayrı ayrı çekmek yerine
	 * daha önce yüklediği varsa ona yönlendiriyor. Bu sayede bellek taşmaları engellenebilir.
	 */
	public static $USE_DB_REPOSITORY = false;

	public static $CHECK_FIELDS_ON_SAVE = false;

	public static $TABLE_FIELDS = array();

	/**
	 *
	 * @var array Sorguya eklenen parametreler
	 * name => {Type, Default}
	 */
	public $QueryParams = array();

	public function __construct()
	{
		foreach($this->QueryParams as $name => &$param)
		{
			$param = (object)$param;
			if(is_callable(App::$QueryParamValueFunc))
			{
				$val = call_user_func(App::$QueryParamValueFunc, $name);
				if($val !== NULL)
				{
					if($param->Type == 'Date')
						$val = Tarih::ToMysqlDate($val);
					$param->Default = $val;
				}
			}
		}
	}

	/**
	 * @return ModelMap
	 */
	public function GetModelMap()
	{
		static $ModelMapIns = null;

		if ($ModelMapIns)
			return $ModelMapIns;

		$ModelMapName = ''; // Bulunamadı
		$class = preg_replace("/(Db|VT)$/i", "", get_class($this)) . 'ModelMap';
		if (class_exists($class) && in_array('ModelMap', class_parents($class)))
			$ModelMapName = $class;
		if($ModelMapName)
			$ModelMapIns = CallUserFunc("$ModelMapName::Get");
		return $ModelMapIns;
	}

	public function GetModelName()
	{
		if ($this->ModelName == NULL)
		{
			$class = preg_replace("/(Db|VT)$/i", "", get_class($this));
			if (class_exists($class) && in_array('ModelBase', class_parents($class)))
				$this->ModelName = $class;
			else
				$this->ModelName = ''; // Bulunamadı
		}

		return $this->ModelName;
	}

	public function GetIdFieldName($asOrigin = false)
	{
		$map = $this->GetModelMap();
		$fld = @$map->DbFields[$this->idName];
		/* @var $fld DbField */
		if($fld)
			return $asOrigin ? $fld->FieldName : $fld->Name;
		return '';
	}

	/**
	 * Bu DB operasyon sınıfının ilişkili olduğu modele ait bir örnek döndürür
	 *
	 * @return ModelBase|null
	 */
	public function GetModelInstance($row = NULL)
	{
		if (($name = $this->GetModelName()) != '')
			return new $name;

		return $row = NULL;
	}

	public function GetSelectQuery()
	{
		if (strval($this->_SelectQuery) == "" )
			$this->_SelectQuery =  "SELECT * FROM " . $this->GetModelInstance()->TableName;
		return $this->_SelectQuery;
	}

	/**
	 *
	 * @param string $query
	 */
	protected function SetQueryParams($query)
	{
		$replace = array();
		foreach($this->QueryParams as $name => $param)
		{
			$param = (object)$param;
			if (property_exists($param, 'Value'))
				$val = $param->Value;
			else
				$val = $param->Default;
			if ($param->Type == 'Number')
				$val = floatval($val);
			else if ($param->Type == 'String')
				$val = "'". addcslashes($val, "'") ."'";
			else if ($param->Type == 'Date')
				$val = "'". Tarih::ToMysqlDate($val) . "'";
			if($param->Type != 'Nothing')
				$replace['@'.$name] = $val;
		}
		return str_replace(array_keys($replace), array_values($replace), $query);
	}

	public function SetSelectQuery($query, $params = array())
	{
		foreach($params as $key => $value)
			if(array_key_exists($key, $this->QueryParams))
				$this->QueryParams[$key]->Value = $value;
		$this->_SelectQuery = $this->SetQueryParams($query);
		return $this;
	}

	private function CheckFieldIsSet()
	{
		static $checked = false;
		if($checked)
			return;
		$fields = DB::Get()->GetFields($this->_SelectQuery);
		foreach($this->GetModelMap()->DbFields as $f)
			$f->IsSet = array_key_exists($f->Name, $fields);
		$checked = true;
	}

	public function GetSummaryFields()
	{
		return $this->_SummaryFields;
	}

	public function SetSummaryFields($fields)
	{
		$this->_SummaryFields = $fields;
	}

	/**
	 *
	 * @param type $id
	 * @param type $AutoCreate
	 * @return ModelBase
	 */
	public function GetById($id, $AutoCreate = false)
	{
		$id = intval($id);
		if($id <= 0 && $AutoCreate)
			return $this->GetModelInstance()->Init(NULL);
		if($id > 0)
			$list = $this->GetList(array($this->idName => $id));
		if (isset($list) && count($list) > 0)
			return $list[0];

		return NULL;
	}

	/**
	 * @param array|carray $ids id dizisi ve , ile ayrılmış string
	 * @return ModelBase[]|ModelBaseArray
	 */
	public function GetByIds($ids)
	{
		if(! $ids)
			return new ModelBaseArray();
		if(IsSerialized($ids))
			$ids = mb_unserialize($ids);
		$params = new stdClass();
		$params->Id = Condition::InList($ids);
		return $this->GetList($params);
	}

	public function Delete(ModelBase $obj, $control = ModelDb::DELETE_WITH_CONTROL)
	{
		if(! $this->IsDbRecord)
			return 'Bu kayıt silinemez';

		if ($obj->Id <= 0)
			return 'Kayıt bulunamadı';
		//------------------------------------------------------------------------
		// Bu nesneye ait alt nesneler varsa, onlar silinmeden bu nesne silinemez
		//------------------------------------------------------------------------
		if ($control > ModelDb::DELETE_WITHOUT_CONTROL)
		{
			if(($IsDeletable = $this->IsDeletable($obj)) != 1)
				return $IsDeletable;

			if ($control == ModelDb::DELETE_CONTROL_ONLY)
				return 1;
		}

		if ($control != ModelDb::DELETE_CONTROL_ONLY)
			$this->BeforeDelete($obj);
		//--------------------------------------------------------
		// Kaydı sil
		//--------------------------------------------------------
		DB::Delete($obj->GetModelMap()->Name, $this->GetIdFieldName() . " = $obj->Id", get_class($obj). ':Delete');

		if (DB::ErrorNo() != 0)
			return DB::Error();

		//--------------------------------------------------------
		// alt kayıtları da varsa sil
		//--------------------------------------------------------
		$this->CascadeDelete($obj);
		$this->AfterDelete($obj);
		return 1;
	}

	public function BeforeDelete(ModelBase $obj)
	{

	}

	public function AfterDelete(ModelBase $obj)
	{

	}

	public function BeforeSave(ModelBase $obj)
	{

	}

	public function AfterSave(ModelBase $obj)
	{

	}

	public function DeleteAllRelationItem(ModelBase $RltnObj, $conditions)
	{
		$fields = $RltnObj->GetModelMap()->DbFields;
		$where = array();
		foreach($conditions as $field => $value)
			$where[] = $value->ToString($fields[$field]->Name);
		if ($where)
			return DB::Delete($RltnObj->GetModelMap()->Name, implode(' AND ', $where), 'DeleteAllRelationItem');
		return 1;
	}

	protected function IsDeletable(ModelBase $obj)
	{
		foreach($obj->GetModelMap()->Relationships as $relation)
		{
			if ($relation->Behaviour != RelationBehaviour::PREVENT_DELETION &&
				$relation->Behaviour != RelationBehaviour::CASCADE_DELETE)
				continue;

			// Alt kayıtları recursive kontrol et
			$childModel = $relation->GetChildModel();
			if (! $childModel)
				continue;
			$altDb = $childModel->GetDb();
			if($relation->Behaviour == RelationBehaviour::CASCADE_DELETE)
			{
				$altObj = $obj->{$relation->AccessField};
				if(! isset($altDb) || ! isset($altObj))
					continue;
				if (in_array('ModelBase', class_parents($altObj))){
					$dizi = array($altObj);
				}else
					$dizi = $altObj;
				foreach ($dizi as $altObj)
					if (($deletable = $altDb->IsDeletable($altObj)) != 1)
						return $deletable;
			}
			else
			{
				// Alt ilişkideki kayıt sayısını bul
				$ModelName = $relation->GetChildModel()->GetModelMap()->ModelName;
				$conditions = $relation->GetConditions($obj);
				if (!$conditions)
					return 1;
				$count = $altDb->GetCount($conditions);
				if ($count > 0)
				{
					$msg = "$ModelName alt kayıtları ($count adet) olduğu için silme yapamazsınız";
					if (App::IsUTF8())
						$msg = Kodlama::UTF8($msg);
					return $msg;
				}
			}
		}

		// Silinebilir
		return 1;
	}

	private function CascadeDelete(ModelBase $obj)
	{
		$map = $obj->GetModelMap();
		foreach($map->Relationships as $relation)
		{
			if ($relation->Behaviour != RelationBehaviour::CASCADE_DELETE)
				continue;

			// Önce alt kayıtları recursive sil, sonra ana kayıt sil
			$childModel = $relation->GetChildModel();
			if (! $childModel)
				continue;
			$altDb = $childModel->GetDb();
			$altObj = $obj->{$relation->AccessField};
			if(!isset($altDb) || !isset($altObj))
				continue;
			if (in_array('ModelBase', class_parents($altObj))){
				$dizi = array($altObj);
			}else
				$dizi = $altObj;
			foreach ($dizi as $altObj)
				if (($deleted = $altDb->Delete($altObj, ModelDb::DELETE_WITHOUT_CONTROL)) != 1)
					return $deleted;
		}
		foreach($map->DbFields as $key => $field)
			if($field->ModelName == 'AppFile')
			{
				$f = $obj->{$key};
				/*@var $f AppFile */
				if(is_a($f, 'AppFile'))
					$f->Delete();
			}
			else if ($field->ModelName == 'AppFileList')
			{
				$f = $obj->{$key};
				/*@var $f ModelBaseArray */
				if(is_a($f, 'ModelBaseArray'))
					$f->DeleteAll();
			}

		// Silindi
		return 1;
	}

	public function UpdateAllRelationItem(ModelBase $RltnObj, $conditions, $alanlar)
	{
		$map = $RltnObj->GetModelMap();
		$dbAlanlar = array();
		foreach($alanlar as $FieldName => $val){
			$dbField = $map->DbFields[$FieldName];
			if(! $dbField)
				ThrowException ("$FieldName alanı bulunamadı");
			$fldVal = is_null($val) ? 'NULL' : $dbField->ToSet($val, $this);
			$dbAlanlar[] = "$dbField->Name = " . $fldVal;
		}
		$sets = implode(', ', $dbAlanlar);
		$where = array();
		foreach($conditions as $field => $value)
			$where[] = $value->ToString($map->DbFields[$field]->Name);
		$where = implode(' AND ', $where);
		if ($where)
			return DB::Update($map->Name, $sets, $where, 'UpdateAllRelationItem');
		return 0;
	}

	/**
	 * @return static
	 */
	public static function Get($params = array())
	{
		// instance her parametre grubu için farklı oluşturulacak
		static $instance = array();
		if (count($params) == 0)
			$key = '0';
		else
			$key = substr(md5(serialize($params)), 0, 10);
		if (! isset($instance[$key]))
			$instance[$key] = new static();
		$instance[$key]->SetSelectQuery($instance[$key]->_SelectQuery, $params);
		return $instance[$key];
	}

	/**
	 * @return static
	 */
	public static function GetFromQuery($query, $params = array())
	{
		$instance = new static();
		$instance->SetSelectQuery($query, $params);
		return $instance;
	}

	/**
	 * @param string $customStr
	 * @return this|$this
	 */
	public function SetOrderByExp($customStr)
	{
		$this->OrderByFieldCustomStr = $customStr;
		return $this;
	}

	/**
	 * @param string $customStr
	 * @return this|$this
	 */
	public function SetGroupByExp($customStr)
	{
		$this->GroupByFieldCustomStr = $customStr;
		return $this;
	}

	protected static function GetSumQuery($query, $sumFields = "COUNT(*)")
	{
		if(self::$SumQuery)
			return self::$SumQuery;
		$sep = "/\*fields\*/";
		$pat = "#$sep(.*)$sep#s";
		if (preg_match($pat, $query))
			return preg_replace($pat, $sumFields, $query);
		else
			return "SELECT $sumFields FROM ($query) tbl ";
	}

	/**
	 * Sorgu da where için (1=1) ifadesi varmı diye arar. Bulunmazsa sorguyu
	 * SELECT * FROM ($sorgu) a ifadesi içine alır
	 * @return bool
	 */
	public function IsWrappedQuery()
	{
		$query = $this->GetSelectQuery();
		return  strpos($query, '(1=1)') ? 0 : 1;
	}

	/**
	 * Parametreleri sorguya eklemeden önce işler
	 * Varsayılan parametreler nesneye eklenebilir
	 * @param stdClass $params
	 */
	public function ProcessParams($params)
	{
		return '';
	}

	public function GetTableAlias()
	{
		$fields = $this->GetModelMap()->DbFields;
		$keys = array_keys($fields);
		$fldName = $fields[$keys[0]]->FieldName;
		$parts = explode('.', $fldName);
		if (!preg_match('/\(/', $fldName) && count($parts) > 1)
			return $parts[0];
		return 'tbl';
	}

	public function GetQuery($params = array(), $returnCount = ModelDb::QUERY_RETURN_LIST, $page = 0, $pageSize = 20)
	{
		$query = $this->GetSelectQuery();
		$sumFields = $this->GetSummaryFields();
		$isWrap = $this->IsWrappedQuery();
		$order = preg_match('/ORDER\s+BY\s+(.*)\(1\)/i', $query) ? 1 : 0;
		$alias = $this->GetTableAlias();
		if ($returnCount == ModelDb::QUERY_RETURN_COUNT)
			$query = self::GetSumQuery($query);
		else if ($returnCount == ModelDb::QUERY_RETURN_SUMMARY)
			$query = self::GetSumQuery($query, $sumFields);
		else if($isWrap)
			$query = "SELECT * FROM ($query) $alias ";
		$where = $this->GetWhere($params);
		if ($where != '')
			if($isWrap)
				$query .= "\nWHERE $where";
			else
			{
//				if ($returnCount == ModelDb::QUERY_RETURN_LIST)
//					$query = StringLib::RowTrim($query);
				$query = str_replace ('(1=1)', $where, $query);
			}
		if ($returnCount != ModelDb::QUERY_RETURN_LIST)
		{
			$subPattern = "[a-z0-9_,\.\s'`ÖİışŞçÇğĞüÜ ]+";
			return $isWrap ? $query : preg_replace("/ORDER\s+BY\s+(\($subPattern\)|$subPattern)(.*)$/i", "$2", $query);
		}
		// Özel bir gruplama varsa
		if ($this->GroupByFieldCustomStr)
		{
			$regexp = "/(ORDER\s+BY\s+[a-z0-9_,\.\s()'`]+)$/i";
			$regs = array();
			if (preg_match($regexp, $query, $regs))
				$query = str_replace($regs[1], '', $query);
			$query = "SELECT * FROM ($query) $alias GROUP BY $this->GroupByFieldCustomStr ";
			if ($regs)
				$query .= $regs[1];
		}
		// Özel bir sıralama varsa ekle
		if($order){
			if ($this->OrderByFieldCustomStr != '')
				$order = $this->OrderByFieldCustomStr;
			else
				$order = $this->OrderByFields[$this->OrderByFieldsId];
			$query = preg_replace('/ORDER\s+BY\s+(.*)\(1\)/i', "ORDER BY $1 $order", $query);
		}
		else if ($this->OrderByFieldCustomStr != '')
		{
			$regexp = "/ORDER\s+BY\s+[a-z0-9_,\.\s()'`]+$/i";
			$replace = "ORDER BY $this->OrderByFieldCustomStr";
			if (!$isWrap && preg_match($regexp, $query))
				$query = preg_replace($regexp, $replace, $query);
			else
				$query .= "\n$replace";
		}

		if ($returnCount == ModelDb::QUERY_RETURN_LIST && $page > 0 && $pageSize > 0)
			$query .= " LIMIT " . (($page - 1) * $pageSize) . ", $pageSize";
		return $query;
	}

	public function GetSummary($params = array())
	{
		return DB::FetchSingle($this->GetQuery($params, ModelDb::QUERY_RETURN_SUMMARY, 0, 0), '', get_class($this). ':GetSum');
	}

	public function GetCount($params = array())
	{
		$query = $this->GetQuery($params, ModelDb::QUERY_RETURN_COUNT);
		return DB::FetchScalar($query, '', get_class($this) . ':GetCount');
	}

	/**
	 * @return PagedData
	 */
	public function GetPage($params = array(), $page = 0, $pageSize = 20)
	{
		$sonuc = new PagedData();
		if ($this->PreCalculatedSum)
			$sum = $this->PreCalculatedSum;
		else
			$sum = $this->GetSummary($params);
		$recordCount = $sum[0];
		$pageCount = 1;
		if($pageSize > 0)
			$pageCount = ceil($recordCount / floatval($pageSize));
		if ($page <= 0 || $page > $pageCount)
			$page = 1;
		$rows = $this->GetList($params, $recordCount, $page, $pageSize);

		$sonuc->RecordCount	= $recordCount;
		$sonuc->PageCount	= $pageCount;
		$sonuc->PageSize	= $pageSize;
		$sonuc->Records		= $rows;
		$sonuc->Summary		= $sum;
		$sonuc->PageNo		= $page;
		return $sonuc;
	}

	public function CustomParamToWhere($param, $value, $operator = null)
	{
		if (strtolower($param) == 'sorgu')
		{
			$this->CheckFieldIsSet();
			$wrapped = $this->IsWrappedQuery();
			$cond = new ConditionList(" OR ");
			foreach($this->GetModelMap()->DbFields as $field)
				if ($field->IsStringType() && $field->IsSet && ($wrapped || !$field->IsCalculated()))
					$cond->Add($wrapped ? $field->Name : $field->FieldName, OPRT::LIKE, $value);
			return "$cond";
		}
		return NULL;
	}

	public function IsValidCustomParam($param, $value = 'dummy')
	{
		$op = IfNull($this->CustomParamOperators, $param, null);
		$cond = $this->CustomParamToWhere($param, $value, $op) ;
		return $cond != '' || $cond != NULL;
	}

	protected function GetWhere($params, $useOR = false)
	{
		// Parametreleri temizle ve WHERE ifadesini oluştur
		$conditions = array();
		$modelMap = $this->GetModelMap();
		$DbFields = $modelMap->DbFields;
		if (is_array($params))
			$params = (object)$params;
		$params = ObjectLib::CloneObj($params, 3);
		$this->ProcessParams($params);
		$isWrap = $this->IsWrappedQuery();
		$slowConditions = array();
		foreach($params as $param=>$value)
		{
			if($value === NULL)
				continue;
			if ($modelMap->IsRelationalField($param))
			{
				if (count((array)$value) > 0)
				{
					$rel = $modelMap->Relationships[$param];
					$relConditions = $rel->GetSubQueryConditions();
					$mObj =$rel->GetChildModel()->SetVarsToNull();
					if (! is_bool($value))
						$mObj->SetFromObj((object)$value, false, true);
					$mObj->SetFromArray($relConditions);
					$childModelMap = $mObj->GetModelMap();
					$conditions[] = "EXISTS (".
						"SELECT * FROM $childModelMap->Name $childModelMap->TableAlias  ".
						"WHERE " . $mObj->GetDb()->GetWhere($mObj) . ")";
					$slowConditions[] = count($conditions) - 1;
				}
				continue;
			}
			$field = @$DbFields[Kodlama::KodlamaDuzelt($param)];
			$op = IfNull($this->CustomParamOperators, $param, null);
			$custom = $this->CustomParamToWhere($param, $value, $op);
			if($custom instanceof Condition)
				$custom = $custom->ToString();
			else if ($value instanceof Condition)
			{
				// Parametrelerin tekrar kullanımını engellediği için
				// parametreyi değiştirmeden önce clone'luyoruz
				$val = ObjectLib::CloneObj($value);
				if($val->field == '' && $field)
					$val->field = $isWrap ? $field->Name : $field->FieldName;
				$custom = $val->ToString();
			}
			else if ($value instanceof ConditionList)
			{
				foreach($value->Conds as $cond)
					$cond->field = $isWrap ? $field->Name : $field->FieldName;
				$custom = $value->ToString();
			}
			if ($custom != '' && $custom != NULL)
			{
				$conditions[] = $custom;
				if ($param == 'sorgu')
					$slowConditions[] = count($conditions) - 1;
			}
			else if ($field)
			{
				$condition = array();
				if (!is_array($value) && preg_match("/^([<>!=]{1,2})/", $value))
					$condition["$value"] = "[name][value]";
				elseif ($field->IsValidForWhere($value))
					$condition = $field->ToCondition($value);
				foreach($condition as $val => $cond)
					$conditions[] = str_replace(
						array("[name]", "[value]"),
						array($isWrap ? $field->Name : $field->FieldName, $val),	$cond);
			}
		}

		// Yavaş çalışacak şartları WHERE ifadesinin en sonuna ekliyoruz
		if ($slowConditions)
		{
			$fast = array();
			foreach($conditions as $i => $cond)
				if (! in_array($i, $slowConditions))
					$fast[] = $cond;
			foreach($slowConditions as $i)
				$fast[] = $conditions[$i];

			$conditions = $fast;
		}

		$opr = $useOR ? 'OR' : 'AND';
		return implode("\n $opr ", $conditions);
	}

	/**
	 * @param type $params
	 * @param type $recordCount
	 * @param type $page
	 * @param type $pageSize
	 * @return
	 */
	public function GetList($params = array(), $recordCount = 1, $page = 0, $pageSize = 0)
	{
		static $repository = array();
		$rows = new ModelBaseArray();
		if ($recordCount > 0)
		{
			if($page == 0 && self::$USE_DB_REPOSITORY)
			{
				$key = $this->GetModelMap()->Name . $this->GetWhere($params);
				if(self::$USE_DB_REPOSITORY && isset($repository[$key]))
					return $repository[$key];
			}
			$query = $this->GetQuery($params, ModelDb::QUERY_RETURN_LIST, $page, $pageSize);
			$i = ($page - 1) * $pageSize + 1;
			$ModelDb = $this;
			DB::FetchAssoc($query, function($row) use(&$i, &$rows, &$ModelDb){
				$obj = $ModelDb->GetModelInstance($row);
				$obj->Init($row);
				$obj->SiraNo = $i++;
				$rows[] = $obj;
			}, get_class($this) . ':GetList');
			if($page == 0 && self::$USE_DB_REPOSITORY)
				$repository[$key] = $rows;
		}
		return $rows;
	}

	/**
	 * Verilen şarlara bağlı olarak listeyi gezer ve obje olarak $callFunc fonksiyonuna
	 * gönderir.
	 */
	public function WalkList($params, $callFunc)
	{
		$rs = DB::Query($this->GetQuery($params), get_class($this) . ':WalkList');
		if(DB::ErrorNo() > 0)
			return DB::Error();
		$i = 1;
		while($row = DB::RsFetchArray($rs))
		{
			$obj = $this->GetModelInstance($row);
			$obj->Init($row);
			$obj->SiraNo = $i++;
			CallUserFunc($callFunc, $obj);
		}
		DB::RsFree($rs);
		return $i - 1;
	}

	/**
	 * @return ModelBase
	 */
	public function GetFirst($params = array())
	{
		$list = $this->GetList($params, 1, 1, 1);
		return count($list) ? $list[0] : NULL;
	}

	public function DeleteById($id, $control = ModelDb::DELETE_WITH_CONTROL)
	{
		$obj = $this->GetById($id);
		if($obj != NULL)
			return $this->Delete($obj, $control);
		else
			return 'Kayıt bulunamadı';
	}

	public function Validate(ModelBase $obj)
	{
		if($obj)
			return 1;
	}

	public function CascadeUpdate(ModelBase $obj, $control)
	{
		$map = $this->GetModelMap();
		foreach($obj as $name => $value)
		{
			if(!(is_a($value, 'ModelBaseArray') && $value->Changed)
				&& !(is_a($value, 'ModelBase') && $value->CascadeChanged))
				continue;
			/*@var $value ModelBaseArray */
			if($map->IsRelationalField($name))
			{
				$conds = $map->Relationships[$name]->GetConditions($obj);
				$regs = array();
				foreach($conds as $key => $cond){
					$parVal = $this->GetParentCondition($cond);
					if($parVal !== null)
						if(is_a($value, 'ModelBaseArray'))
							$value->SetValues($key, $parVal);
						else
						{
							//TODO-DGR
//							if(substr($parVal,0,1) == "'" && substr($parVal, strlen($parVal) - 1, strlen($parVal)) == "'")
//								$parVal = str_replace ("'", "", $parVal);

							$value->{$key} = $parVal;
						}
				}
			}
			if(is_a($value, 'ModelBaseArray'))
				$value->Save(null, null, $control);
			else
				$value->Save($control);
		}
	}

	protected function GetParentCondition($cond)
	{
		if ($cond instanceof ConditionList)
		{
			foreach($cond->Conds as $c)
				if ($c->cond == OPRT::EQ_FIELD)
					return trim($c->value);
		}
		else if ($cond instanceof Condition && $cond->cond == OPRT::EQ_FIELD)
			return trim($cond->value);
		else if (preg_match('/(=)\s*(.*)/', $cond, $regs))
			return trim($regs[2]);
		return null;
	}

	public function Save(ModelBase $obj, $control = ModelDb::SAVE_WITH_CONTROL)
	{
		if(! $this->IsDbRecord)
			return 'Bu kayıt kaydedilemez';
		//------------------------------------------------------------------------
		// Bu nesneye ait alt nesneler varsa, onlar silinmeden bu nesne silinemez
		//------------------------------------------------------------------------
		if ($control > ModelDb::SAVE_WITHOUT_CONTROL)
		{
			if(($Validate = $this->Validate($obj)) != 1)
				return $Validate;

			if ($control == ModelDb::SAVE_CONTROL_ONLY)
				return 1;
		}

		if ($control != ModelDb::SAVE_CONTROL_ONLY)
			$this->BeforeSave($obj);
		//--------------------------------------------------------
		// Kaydı güncelle
		//--------------------------------------------------------
		$fields = $this->GetModelMap()->DbFields;
		$tableName = $this->GetModelMap()->Name;

		$setExpressions = array();
		$expFields = array();
		$fldVals = array();
		$idName = $this->GetIdFieldName();
		$existingFields = array();
		if (self::$CHECK_FIELDS_ON_SAVE)
			$existingFields = $this->GetTableFields();
		foreach($fields as $property => $field)
		{
			// Gerçek bir alan değilse, atla
			if (! $field->IsReal)
				continue;
			$parts = explode('.', $field->FieldName);
			$fname = end($parts);
			if($fname == $idName || in_array($fname, $expFields))
				continue;
			if ($existingFields && ! in_array($fname, $existingFields))
				continue;
			$expFields[] = $fname;
			$fldVal = is_null($obj->{$property}) ? 'NULL' : $field->ToSet($obj->{$property}, $this);
			$fldVals[$property] = $fldVal;
			$setExpressions[] = " $fname = " . $fldVal;
		}

		$sets = implode(",\n", $setExpressions);

		if($obj->Id > 0)
			DB::Update($tableName, $sets, "$idName = $obj->Id", get_class($obj). ':Update', get_class($obj));
		else
			DB::Insert($tableName, $sets, $idName, get_class($obj). ':Insert', get_class($obj));

		if($obj->Id <= 0)
		{
			$obj->Id = DB::InsertedId();
			$obj->{$this->idName} = $obj->Id;
		}

		foreach($fields as $property => $field)
		{
			$parts = explode('.', $field->FieldName);
			$fname = end($parts);
			if ($existingFields && ! in_array($fname, $existingFields))
				continue;
			if(($field->ModelName == 'AppFile' || $field->ModelName == 'AppFileImage') && $obj->{$property}->Changed)
				$obj->{$property}->UpdateOrigin($this->GetModelName(), $property, $obj->Id);
			else if($field->ModelName == 'AppFileList')
				AppFile::UpdateFiles($fldVals[$property], $this->GetModelName(), $property, $obj->Id);
		}

		//--------------------------------------------------------
		// alt kayıtları da güncelle
		//--------------------------------------------------------
		if($obj->WillBeCascadeUpdate)
			$this->CascadeUpdate($obj, $control);
		$this->AfterSave($obj);
		return 1;
	}

	public function GetMaxValue($field, $default = 0) {
		$r = DB::FetchScalar("SELECT MAX($field) FROM " . $this->GetModelMap()->Name);
		if($r)
			$default = $r;
		return $default;
	}

	protected function GetTableFields()
	{
		$tableName = $this->GetModelMap()->Name;
		if (array_key_exists($tableName, self::$TABLE_FIELDS))
			return self::$TABLE_FIELDS[$tableName];

		$fields = DB::FetchList("SHOW COLUMNS FROM $tableName");
		return self::$TABLE_FIELDS[$tableName] = array_keys($fields);
	}
}
