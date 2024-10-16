<?php
class VarTypes
{
	const	NUMERIC		= 1001;
	const	STRING		= 1002;
	const	DATETIME	= 1003;
	const	DATE		= 1004;
	const	TIME		= 1005;
	const	INT			= 1006;
	const	FLOAT		= 1007;
	const	MONEY		= 1008;
	const	ARRAY_STR	= 1009;
	const	CARRAY_STR	= 1010;
	const	BOOL		= 1011;
	const	YEAR		= 1012;
	const	BLOB		= 1013;
	const	BYTE_BLOB	= 1014;

	public static $Classes = array(
			self::STRING		=> 'VarStr',
			self::NUMERIC		=> 'VarNumeric',
			self::DATETIME		=> 'VarDateTime',
			self::DATE			=> 'VarDate',
			self::TIME			=> 'VarTime',
			self::INT			=> 'VarInt',
			self::FLOAT			=> 'VarFloat',
			self::MONEY			=> 'VarMoney',
			self::ARRAY_STR		=> 'VarArray',
			self::CARRAY_STR	=> 'VarCArray',
			self::BOOL			=> 'VarBool',
			self::YEAR			=> 'VarYear',
			self::BLOB			=> 'VarBlob',
			self::BYTE_BLOB		=> 'VarByteBlob'
	);

	public static $ModelDbTypes = array(
			'bool'		=> self::BOOL,
			'int'		=> self::INT,
			'float'		=> self::FLOAT,
			'money'		=> self::MONEY,
			'date'		=> self::DATE,
			'time'		=> self::TIME,
			'datetime'	=> self::DATETIME,
			'string'	=> self::STRING,
			'array'		=> self::ARRAY_STR,
			'carray'	=> self::CARRAY_STR,
			'year'		=> self::YEAR,
			'blob'		=> self::BLOB,
			'byte_blob'	=> self::BYTE_BLOB
	);

	public static $CustomVarTypes = array(
			'TextArea'	=> 'VarTextArea',
			'RichEdit'	=> 'VarRichEdit',
			'Password'	=> 'VarPassword',
			'Telefon'	=> 'VarTelefon',
			'Aktif/Pasif'	=> 'VarBoolAP',
			'Yıl Listesi'	=> 'VarYilList',
			'AppFile'		=> 'VarAppFile',
			'AppFileList'	=> 'VarAppFileList',
			'AppFileImage'	=> 'VarAppFileImage',
			'Sehir'	=> 'VarSehir'
	);

	/**
	 * type a karşılık gelen sınıfı üretir
	 * Varsayılan VarStr<br>
	 * @param int $type
	 * @return VarBase
	 */
	public static function GetInst($type)
	{
		$class = '';
		if(is_object($type))
			return $type;
		// Kullanıcı tarafından tanımlı bir tür mü
		elseif (isset(self::$CustomVarTypes[$type]))
			$class = self::$CustomVarTypes[$type];
		// ModelDb türlerinden bir tür mü
		elseif (isset(self::$ModelDbTypes[$type]))
			$type = self::$ModelDbTypes[$type];
		else
			// MySQL den gelen bir tür mü?
			$type = self::GetMySqlTypeClass($type);
		// Sınıf verilmiş se direk onu alalım
		if (! $class && class_exists($type))
			$class = $type;

		if(! $class)
			$class = IfNull(self::$Classes, $type, self::$Classes[self::STRING]);
		return new $class;
	}

	public static function GetTypeFromValue($value)
	{
		$type = self::STRING;
		if (is_float($value))
			$type = self::FLOAT;
		if (is_int($value))
			$type = self::INT;
		if (Tarih::IsDate($value))
			$type = self::DATETIME;
		return $type;
	}

	public static function GetMySqlTypeClass($type)
	{
		if(! is_int($type))
			return $type;
		switch($type)
		{
			case MYSQLI_TYPE_BIT:
				return self::BOOL;
			case MYSQLI_TYPE_DATE:
				return self::DATE;
			case MYSQLI_TYPE_TIME:
				return self::TIME;
			case MYSQLI_TYPE_DATETIME:
				return self::DATETIME;
			case MYSQLI_TYPE_DECIMAL:
			case MYSQLI_TYPE_DOUBLE:
			case MYSQLI_TYPE_FLOAT:
				return self::FLOAT;
			case MYSQLI_TYPE_TINY:
			case MYSQLI_TYPE_SHORT:
			case MYSQLI_TYPE_INT24:
			case MYSQLI_TYPE_LONG:
				return self::INT;
			case MYSQLI_TYPE_STRING:
			case MYSQLI_TYPE_VAR_STRING:
				return self::STRING;
			case MYSQLI_TYPE_BLOB:
			case MYSQLI_TYPE_LONG_BLOB:
				return self::BLOB;
		}
		return $type;
	}

	/**
	 * Verilen ModelDb type karşılık nesneyi türetir.
	 * Varsayılan new VarStr()<br>
	 * @param string $type
	 * @return int
	 */
	public static function GetInstFromModelDbType($type)
	{
		if(isset(self::$CustomVarTypes[$type]))
			$class = self::$CustomVarTypes[$type];
		else
			$class = IfNull(self::$ModelDbTypes, $type, self::STRING);
		return new $class;
	}

	/**
	 * Verilen ModelDb type sabiti integer olarak türetir.
	 * Varsayılan VarTypes::STRING<br>
	 * @param string $type
	 * @return int
	 */
	public static function GetFromModelDbType($type)
	{
		return IfNull(self::$ModelDbTypes, $type, self::STRING);
	}

	/**
	 * Verilen db type karşılık gelen sabiti string olarak dönderir.
	 * Varsayılan 'VarTypes::STRING'<br>
	 * Örnek: 'int' -> 'VarTypes::INT'
	 * @param string $type
	 * @return string
	 */
	public static function GetFromModelDbStr($type)
	{
		// VarBase'den türetilmiş custom türler gelmişse
		// onların Type özelliğine bakarak türü anlıyoruz
		$type = self::GetFromCustomVarType($type);
		$type = IfNull(VarTypes::$ModelDbTypes, $type, self::STRING);
		$refl = new ReflectionClass('VarTypes');
		$consts = $refl->getConstants();
		return 'VarTypes::' . array_search($type, $consts);
	}

	public static function GetFromCustomVarType($varType)
	{
		if (array_key_exists($varType, VarTypes::$CustomVarTypes))
		{
			/* @var $obj VarBase */
			$obj = new VarTypes::$CustomVarTypes[$varType];
			$type = $obj->Type;
			return array_search($type, self::$ModelDbTypes);
		}
		return $varType;
	}
}

class VarBase
{
	public $Type = '';
	public $Default = "";
	public $Align = 'left';
	public $Colspan = 1;
	public $LazyInit = false;
	/**
	 * @global
	 */
	public $TopLabel = 0;

	/**
	 * @global
	 */
	public $Addon = '';

	public function InitProp($value)
	{
		return $value;
	}

	public function ToStr($value, $realData = null)
	{
		return $value;
	}

	public function ToExcelValue($value)
	{
		return $this->ToStr($value);
	}

	public function ToCondition($value)
	{
		return array($value => " [name]='[value]' ");
	}

	public function ToSet($value)
	{
		return "'$value'";
	}

	public function IsValidForWhere($value)
	{
		return $value != '';
	}

	public function SearchType()
	{
		$sonuc = array_search($this->Type, VarTypes::$ModelDbTypes);
		return $sonuc ? $sonuc : 'text';
	}

	public function ToMySql($value)
	{
		return $value;
	}

	public function SplitParams($value)
	{
		$parts = explode('|', trim($value));
		for($i=0; $i < count($parts); $i++)
			if ($parts[$i] != '')
				$parts[$i] = $this->ToMySql($parts[$i]);
		if (count($parts) == 1 || $parts[0] == $parts[1])
			return array("$parts[0]" => " [name]='[value]' ");
		else
		{
			if ($parts[0] == '')
				return array("$parts[1]" => "[name]<='[value]'");
			if ($parts[1] == '')
				return array("$parts[0]" => "[name]>='[value]'");
			return array(
				"$parts[0]" => "[name]>='[value]'",
				"$parts[1]" => "[name]<='[value]'");
		}
	}

	/**
	 * @return FormItem
	 */
	public function GetFormItem()
	{
		return new FormInputStr();
	}

	/**
	 * @return static
	 */
	public static function Get()
	{
		return new static;
	}
}

class VarStr extends VarBase
{
	public $Type = VarTypes::STRING;
	public function InitProp($value)
	{
		$model = func_get_arg(1);
		$fieldName = func_get_arg(2);
		if (IsSerialized($value))
		{
			$sonuc = @mb_unserialize($value);
			if($sonuc === false && $value != 'b:0;')
				$sonuc = $value;
			return $sonuc;
		}
		else if ($obj = IsJsonString($value))
		{
			$sonuc = $obj;
			if ($model && $fieldName)
			{
				/* @var $model ModelBase */
				$field = $model->GetModelMap()->GetFieldByName($fieldName);
				$class = '';
				if ($field && $field->ModelName && class_exists($field->ModelName))
				{
					$class = $field->ModelName;
					if (is_array($sonuc))
						for($i=0; $i<count($sonuc); $i++)
							$sonuc[$i] = ObjectLib::CastDeep($class, $sonuc[$i]);
					else if (is_object($sonuc))
						$sonuc = ObjectLib::CastDeep($class, $sonuc);
				}
			}
			return $sonuc;
		}
		return parent::InitProp($value);
	}

	public function ToCondition($value)
	{
		$value = addslashes($value);
		return array($value => " [name] LIKE '%[value]%' ");
	}

	public function ToSet($value)
	{
		if (is_object($value) || is_array($value))
		{
			$modelDb = func_get_arg(1);
			/* @var $modelDb ModelDb */
			if ($modelDb && $modelDb->SerializationType == ModelDb::SERIALIZE_JSON)
				$value = json_encode ($value);
			else
				$value = serialize($value);
		}
		return parent::ToSet(DB::EscapeString(trim($value)));
	}
}

class VarBlob extends VarBase
{
	public function IsValidForWhere($value)
	{
		return false;
	}

	public function ToCondition($value)
	{
		return null;
	}

	public function ToSet($value)
	{
		return parent::ToSet(DB::EscapeString(trim($value)));
	}
}

class VarByteBlob extends VarBlob
{
	public function InitProp($value)
	{
		return ByteArray::FromBlob($value, true);
	}

	public function ToSet($value)
	{
		$value = ByteArray::ToBlob($value);
		return parent::ToSet($value);
	}
}

class VarStrAutoComplate extends VarStr
{
	public $List = array();

	public function GetFormItem()
	{
		$input = parent::GetFormItem();
		$list = implode("#|#", $this->List);
		$input->attr('auto_list', addslashes($list));
		return $input;
	}
}

class VarPassword extends VarStr
{
	public function GetFormItem()
	{
		return new FormInputPass();
	}
}

class VarTelefon extends VarStr
{
	public function GetFormItem()
	{
		$obj = new FormInputStr();
		$obj->addClass('Telefon');
		$obj->css('width', '11em');
		return $obj;
	}
}

class VarEmail extends VarStr
{
	public function GetFormItem()
	{
		return new FormInputEmail();
	}
}

class VarEmailExt extends VarStr
{
	public $SaveWithExtension = 1;

	public function GetExt()
	{
		$ext = '';
		if (function_exists('GetEmailExt'))
			$ext = GetEmailExt();
		else
			$ext = ImapAyar::Get()->EmailExt;
		if (! $ext)
			ThrowException('GetEmailExt fonksiyonu bulunamadi veya ImapAyar set edilmedi.');
		return $ext;
	}

	public function GetFormItem()
	{
		$inp = new FormInputEmail();
		$inp->attr('var_type', 'email_ext');
		$inp->attr('email_ext', $this->GetExt());
		$inp->attr('save_ext', $this->SaveWithExtension);
		return $inp;
	}

	public function ToSet($value)
	{
		$ext = $this->GetExt();
		if (!$this->SaveWithExtension || $this->SaveWithExtension != '1')
			$value = preg_replace("/$ext/", '', $value);
		return parent::ToSet($value);
	}
}

class VarTextArea extends VarStr
{
	public $TopLabel = 1;

	public function GetFormItem()
	{
		return new FormTextArea();
	}
}

class VarEditableDiv extends VarStr
{
	public function GetFormItem()
	{
		return new FormEditableDiv();
	}
}

class VarHidden extends VarStr
{
	public function GetFormItem()
	{
		return new FormInputHidden();
	}
}

class VarRichEdit extends VarStr
{
	public $TopLabel = 1;

	/**
	 * @global
	 */
	public $BasicToolbar = 0;

	public function ToSet($value)
	{
		$value = str_replace('&quot;', '"', $value);
		return parent::ToSet($value);
	}

	public function GetFormItem()
	{
		$inp =  new FormRichEdit();
		if ($this->BasicToolbar == 1)
			$inp->attr('basic_toolbar', 1);
		return $inp;
	}
}

class VarNumeric extends VarBase
{
	public $Type = VarTypes::NUMERIC;
	public $Default = 0.0;
	public $Align = 'right';
	public $Decimal = 2;

	public function ToStr($value, $realData = null)
	{
		return Number::Format(floatval($value), $this->Decimal);
	}

	public function ToMySql($value)
	{
		return floatval($value);
	}

	public function ToCondition($value)
	{
		return $this->SplitParams($value);
	}

	public function ToSet($value)
	{
		return round(floatval($value), 7);
	}

	public function IsValidForWhere($value)
	{
		return ($value > 0) || ($value === 0) || ($value === '0');
	}

	public function ToExcelValue($value)
	{
		return number_format(doubleval($value), 2, ',', '.');
	}
}

class VarInt extends VarNumeric
{
	public $Type = VarTypes::INT;
	public $Default = 0;
	public $Decimal = 0;

	public function GetFormItem()
	{
		$obj = parent::GetFormItem();
		$obj->attr('var_type', 'int');
		return $obj;
	}
}

class VarYear extends VarInt
{
	public function ToStr($value, $realData = null)
	{
		return $value;
	}

	public function GetFormItem()
	{
		$obj = parent::GetFormItem();
		return $obj->attr('var_type', 'year');
	}
}

class VarMonth extends VarListItem
{
	public function LoadListItems()
	{
		$this->BuildFromArray(Tarih::$TumAylar,true);
	}

	public function GetFormItem()
	{
		$obj = parent::GetFormItem();
		$obj->css('width', '10em');
		return $obj;
	}
}

class VarSon10Year extends VarListItem
{
	public function LoadListItems()
	{
		$yillar = [];
		$buYil = Tarih::GetYear(Tarih::Bugun());

		for($i=0; $i <= 10; $i++)
			$yillar[$buYil-$i] = $buYil - $i;
		$this->BuildFromArray($yillar,true);
	}

	public function GetFormItem()
	{
		$obj = parent::GetFormItem();
		$obj->css('width', '10em');
		return $obj;
	}
}

class VarFloat extends VarNumeric
{
	public $Type = VarTypes::FLOAT;

	public function GetFormItem()
	{
		$obj = parent::GetFormItem();
		$obj->attr('var_type', 'float');
		return $obj;
	}
}

class VarMoney extends VarFloat
{
	public $Type = VarTypes::MONEY;
    public $Unit = "TL";

	public function ToStr($value, $realData = null)
	{
		return parent::ToStr($value, $realData) . " " . $this->Unit;
	}

	public function GetFormItem()
	{
		$obj = parent::GetFormItem();
		$obj->attr('var_type', 'money');
		return $obj;
	}
}

class VarDateTime extends VarBase
{
	public $Type = VarTypes::DATETIME;

	public function InitProp($value)
	{
		return $this->ToStr($value);
	}

	public function ToStr($value, $realData = null)
	{
		if(!Tarih::IsDate($value))
			return NULL;
		return Tarih::ToNormalDate($value);
	}

	public function ToMySql($value)
	{
		$value = Tarih::ToMysqlDate($value);
		if(! Tarih::IsDate($value))
			return NULL;
		return $value;
	}

	public function ToCondition($value)
	{
		return $this->SplitParams($value);
	}

	public function ToSet($value)
	{
		$value = $this->ToMySql($value);
		if(! $value)
			return 'NULL';
		return parent::ToSet($value);
	}

	public function IsValidForWhere($value)
	{
		$parts = explode('|', $value);
		foreach($parts as $part)
			if (Tarih::IsDate($part))
				return true;
	}

	public function GetFormItem()
	{
		$obj = parent::GetFormItem();
		$obj->attr('var_type', 'datetime');
		return $obj;
	}
}

class VarDate extends VarDateTime
{
	public $Type = VarTypes::DATE;

	public function ToStr($value, $realData = null)
	{
		$value = Tarih::ToNormalDate($value, '-', FALSE);
		if(!Tarih::IsDate($value))
			return NULL;
		return $value;
	}

	public function GetFormItem()
	{
		$obj = parent::GetFormItem();
		$obj->attr('var_type', 'date');
		$obj->attr('date_selector', '1');
		return $obj;
	}
}

class VarTime extends VarStr
{
	public $Type = VarTypes::TIME;

	public function GetFormItem()
	{
		$obj = parent::GetFormItem();
		$obj->attr('var_type', 'time');
		return $obj;
	}
}

class VarArray extends VarStr
{
	public $Type = VarTypes::ARRAY_STR;

	public function IsValidForWhere($value)
	{
		return $value = false;
	}
}

class VarCArray extends VarStr
{
	public $Type = VarTypes::CARRAY_STR;

	public function InitProp($value)
	{
		return explode(',', $value);
	}

	public function IsValidForWhere($value)
	{
		return is_string($value) && $value;
	}

	public function ToCondition($value)
	{
		return array(addslashes($value) => " FIND_IN_SET('[value]',[name]) ");
	}

	public function ToSet($value)
	{
		if (is_array($value))
			return implode(',', $value);
		return parent::ToSet($value);
	}
}

class VarListItem extends VarStr
{
	protected $ListItems = array();
	protected $Categories= array();
	private $LoadedList = false;
	/** @global */
	public $emptyOptStr = 'Choose...';
	/** @global */
	public $showEmptyOpt = 1;
	public $SelectNearest = false;
	public $Multiple = 0;
	/**
	 * Object dizilerinde eklenmesi istenen ek attribute dizisi
	 * Örnek: array('attr_name' => 'obj_prop_name')
	 */
	public $ProbLists = array();
	/**
	 * BuiltFromObj kullanıldığında kullanılan liste
	 * @var object[]
	 */
	public $ObjList = null;

	protected function GetSessionKey()
	{
		return '';
	}

	protected function LoadListItems()
	{
		if ($this->ListItems && is_string($this->ListItems))
			$this->ListItems = explode(',', $this->ListItems);
	}

	public function BuildFromArray($array, $useKeys = false, $keysForCat = false)
	{
		$this->ListItems = array();
		$this->Categories = array();
		if ($keysForCat)
			$this->Categories = $array;
		else
			foreach($array as $key => $value)
				$this->ListItems[$useKeys ? $key : $value] = $value;
		$this->LoadedList = true;
	}

	public function BuildFromObjList($list, $key, $value, $useKeys = false)
	{
		$this->ObjList = array();
		$this->ListItems = array();
		foreach($list as $obj)
		{
			$k = $useKeys ? $obj->{$key} : $obj->{$value};
			$this->ListItems[$k] = $obj->{$value};
			$this->ObjList[$k] = $obj;
		}
		$this->LoadedList = true;
	}

	public function GetListItems()
	{
		if(! $this->LoadedList)
		{
			$key = $this->GetSessionKey();
			if ($key && isset($_SESSION[$key]))
				$this->ListItems = $_SESSION[$key];
			else
			{
				$this->LoadListItems();
				if ($key)
					$_SESSION[$key] = $this->ListItems;
			}
		}
		$this->LoadedList = true;
		return $this->ListItems;
	}

	public function GetOptions($forSearch = true)
	{
		if ($forSearch)
			$this->emptyOptStr = 'Tümü';
		if ($forSearch === null)
			$this->emptyOptStr = null;
		return $this->GetFormItem()->innerHTML();
	}

	public function GetSelect($id, $forSearch = true, $emptyOptText = null)
	{
		if ($forSearch)
			$this->emptyOptStr = 'Tümü';
		if ($emptyOptText != null)
			$this->emptyOptStr = $emptyOptText;
		return $this->GetFormItem()->attr('id', $id)->html();
	}

	public function ToStr($value, $realData = null)
	{
		$list = $this->GetListItems();
		if (count($this->Categories) > 0)
			foreach($this->Categories as $list2)
				$list = array_merge ($list, $list2);
        if($this->Multiple == 1)
        {
            $strArr = [];
            $ids = is_array($value) ? $value : explode(',', $value);
            foreach ($ids as $id)
                $strArr[] = IfNull($list, $id, $id);
            return implode("<br/>",$strArr);
        }
		return IfNull($list, $value, $value);
	}

	public function SearchType()
	{
		return 'select';
	}

	public function GetFormItem()
	{
		if (PageController::IsUTF8())
			$this->emptyOptStr = Kodlama::UTF8($this->emptyOptStr);
		$obj = new FormInputSelect();
		if ($this->Multiple)
		{
			$this->emptyOptStr = '';
			$obj->attr('multiple', 1);
		}
		$obj->SelectNearest = $this->SelectNearest;
		if(!$this->showEmptyOpt)
			$this->emptyOptStr="";
		if($this->emptyOptStr)
			$obj->innerHTML(FormItem::Get('option')->val('')->innerHTML($this->emptyOptStr));
		$items = $this->GetListItems();
		$cats = $this->Categories;
		if (count($cats) == 0)
			$cats[''] = $items;
		foreach($cats as $name => $list)
		{
			$cat = null;
			if ($name != '')
				$cat = FormItem::Get('optgroup')
					->attr('label', $name)
					->appendTo($obj);
			foreach($list as $key => $value)
			{
				if (PageController::IsUTF8())
					$value = Kodlama::UTF8($value);
				$opt = FormItem::Get('option')
					->val($key)
					->innerHTML($value)
					->appendTo($cat ? $cat : $obj);
				if(isset($this->ObjList[$key]))
					foreach($this->ProbLists as $aName => $pName)
						$opt->attr($aName, @$this->ObjList[$key]->{$pName});
			}
		}
		return $obj;
	}

	public function __get($name)
	{
		if ($name == 'ListItems')
			return $this->GetListItems();
		if ($name == 'SearchOptions')
		{
			if($this->SearchOptions)
				return $this->SearchOptions;
			return $this->SearchOptions = $this->GetOptions();
		}
		if ($name == 'SearchOptionsFull')
		{
			if($this->SearchOptionsFull)
				return $this->SearchOptionsFull;
			return $this->SearchOptionsFull = $this->GetOptions(null);
		}
		if ($name == 'EditOptions')
		{
			if($this->EditOptions)
				return $this->EditOptions;
			return $this->EditOptions = $this->GetOptions(false);
		}
	}
}

class VarOrderBy extends VarListItem
{
	protected $ListItems = array(
		'ASC' => 'ASC (Artan)',
		'DESC'=> 'DESC (Azalan)'
	);
}

class VarListInt extends VarListItem
{
	public $Type = VarTypes::INT;

	public function ToCondition($value)
	{
		$value = intval($value);
		return array($value => " [name] = [value] ");
	}

	public function ToSet($value)
	{
		return intval($value);
	}
}

class VarBool extends VarListInt
{
	public $Type = VarTypes::BOOL;
	protected $ListItems = array(
		0 => 'Hayır',
		1 => 'Evet'
	);

	public function GetFormItem()
	{
		$obj = parent::GetFormItem();
		$obj->css('width', '10em');
		return $obj;
	}
}

class VarBoolAP extends VarBool
{
	protected $ListItems = array(
		0 => 'Pasif',
		1 => 'Aktif'
	);
}

class VarBoolBS extends VarBool
{
	public function GetFormItem()
	{
		$obj = parent::GetFormItem();
		$obj->addClass('buttonset');
		return $obj;
	}
}

class VarYilList extends VarListInt
{
	public function LoadListItems() {
		$array = array();
		$start = intval(date('Y'));
		$finish= $start - 10;
		for($i=$start; $i>=$finish; $i--)
			$array[] = $i;
		$this->BuildFromArray($array);
	}
}

class VarAppFile extends VarInt
{
	public $LazyInit = true;
	public $MaxFileSize = -1;
	/**
	 * @global
	 */
	public $AllowedExt = '';
	public $Accept = '';

	/**
	 * @param int $value
	 * @return AppFile
	 */
	public function InitProp($value)
	{
		if (! $value)
			return new AppFile();
		$obj = AppFileDb::Get()->GetById($value, true);
		if (!$obj)
			return new AppFile();
		return $obj;
	}

	/**
	 * @param string $value
	 * @param ModelBase $realData
	 */
	public function ToStr($value, $realData = null)
	{
		$args = func_get_args();
		if (isset($args[2]))
		{
			$value = $realData->{$args[2]};
			/* @var $value AppFile */
			return $value->ToLinkHtml();
		}

		// Dosya adı string olarak gelmiştir (AppFile::__toString() sonucu olarak)
		$file = new AppFile();
		$file->SetYol($value);
		return $file->ToLinkHtml();
	}

	/**
	 * @param AppFile $value
	 */
	public function ToSet($value)
	{
		$value->DecodeId();
		$value->Tasi()->OnChanged();
		if (!$value->Yol)
		{
			$value->Delete();
			return parent::ToSet('');
		}
		$value->Save();
		return parent::ToSet($value->Id);
	}

	public function GetFormItem()
	{
		$input = new FormInputFileContainer();
		$input->MaxFileSize = $this->MaxFileSize;
		$input->SetAllowedExt($this->AllowedExt);
        if($this->Accept != "")
		    $input->input->attr("accept",$this->Accept);
		return $input;
	}
}

class VarAppFileImage extends VarAppFile
{
	public $DefaultImgUrl = 'images/sample_logo.png';
	public $DefaultImgWidth = 180;
	public $AllowedExt = 'jpg,jpeg,gif,png';
	public $MaxResolution = '1024x768';
	public $MaxFileSize = '2MB';
	public $Aspect = 0;

	public function GetFormItem()
	{
		$inp = new FormInputImageContainer();
		$inp->AllowedExt = $this->AllowedExt;
		$inp->DefaultImgUrl = $this->DefaultImgUrl;
		$inp->DefaultImgWidth = $this->DefaultImgWidth;
		$inp->MaxFileSize = $this->MaxFileSize;
		$inp->MaxResolution = $this->MaxResolution;
		$inp->Aspect = $this->Aspect;
		return $inp;
	}

	/**
	 * @param string $value
	 * @param ModelBase $realData
	 */
	public function ToStr($value, $realData = null)
	{
		$args = func_get_args();
		if (isset($args[2]))
		{
			$value = $realData->{$args[2]};
			/* @var $value AppFile */
			$yol = $value->Yol;
		}
		else
			// Dosya adı string olarak gelmiştir (AppFile::__toString() sonucu olarak)
			$yol = RelativePath ($value);

		$class = 'grid-image';
		if (! $yol)
		{
			$yol = $this->DefaultImgUrl;
			$class = '';
		}

		$img = HtmlElement::GetImg($yol, $class)
			->attr('width', $this->DefaultImgWidth);
		return "$img";
	}
}

class VarAppFileList extends VarStr
{
	public $LazyInit = true;
	public $Categories = null;

	/**
	 * @param int $value
	 * @return AppFile
	 */
	public function InitProp($value)
	{
		if (! $value)
			return new ModelBaseArray();
		$cond = Condition::Field(OPRT::IN, $value);
		return AppFileDb::Get()->GetList(array('Id' => $cond));
	}

	/**
	 * @param array $categories
	 * @return $this
	 */
	public function SetCategories($categories)
	{
		$this->Categories = $categories;
		return $this;
	}

	/**
	 * @param AppFile $value
	 */
	public function ToSet($value)
	{
		$ids = array();
		$sira = 1;
		foreach($value as $file)
		{
			$file = AppFile::InitFromObj($file);
			$file->DecodeId();
			if ($file->IsTemp())
				$file->Tasi();
			else if ($file->Id <= 0)
			{
				$yol = str_replace(AppFile::$FILE_DIR, '', $file->Yol);
				$old = AppFileDb::Get()->GetFirst(array('Yol' => "='$yol'"));
				if ($old)
					$file->Id = $old->Id;
			}
			else
				// Daha önceden kaydedilmiş dosyanın
				// tarihini ve boyutunu yenilemek için
				$file->OnChanged();
			$file->Sira = $sira++;
			$file->Save();
			$ids[] = $file->Id;
		}
		return parent::ToSet(implode(',', $ids));
	}

	public function GetFormItem()
	{
		$container = new FormInputFileListContainer();
		$container->Categories = $this->Categories;
		return $container;
	}

	public function GetFormItemWithExt($allowedExt = "")
	{
		$container = new FormInputFileListContainer($allowedExt);
		$container->Categories = $this->Categories;
		return $container;
	}
}

class VarAppFileImageList extends VarAppFileList
{
	public $MaxResolution = '1024x768';
	public $MaxFileSize = '2MB';
	public $Aspect = 0;

	public function GetFormItem()
	{
		$container = new FormInputImageListContainer();
		$container->MaxFileSize = $this->MaxFileSize;
		$container->MaxResolution = $this->MaxResolution;
		$container->Aspect = $this->Aspect;

		return $container;
	}
}

class VarSehir extends VarListItem
{
	public $SelectNearest = true;

	public function LoadListItems()
	{
		$this->BuildFromObjList(SehirInfo::Sehirler(),"Adi","Adi");
	}

	public function GetFormItem() {
		$select = parent::GetFormItem();
		$options = $select->find('option');
		foreach($options as $opt)
		{
			/* @var $opt FormItem */
			$sehir = $opt->val();
			$ilceler = SehirInfo::Ilceler($sehir);
			$opt->attr('sub_items', Kodlama::JSON($ilceler));
		}
		$select->attr('ListType', 'VarSehir');
		return $select;
	}
}

class VarIlce extends VarListItem
{
	public function LoadListItems()
	{
		$this->BuildFromArray(SehirInfo::Ilceler());
	}

	public function GetFormItem()
	{
		$select = parent::GetFormItem();
		$select->attr('ListType', 'VarIlce');
		return $select;
	}
}

class VarRichEditDiv extends VarRichEdit
{
	public $TopLabel = 0;

	function GetFormItem()
	{
		$inp = new FormTextArea();
		$inp->JsRequirements[] = JS_TINYMCE;
		$inp->addClass('rich_edit_div');
		if ($this->BasicToolbar == 1)
			$inp->attr('basic_toolbar', 1);
		return $inp;
	}
}

class VarTextAreaList extends VarRichEdit
{
	public $TopLabel = 0;

	function GetFormItem()
	{
		$inp = new FormTextArea();
		$inp->addClass('textarea_list');
		return $inp;
	}
}
