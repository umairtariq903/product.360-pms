<?php
/**
 * @property string $Align hizalama
 * @property boolean $LazyInit
 * @method mixed InitProp($value) verilen değeri türüne göre nesneye atamaya
 * hazır hale getirir.
 * @method string ToStr($value) verilen değeri türüne göre son kullanıcıya görünecek
 * şekle çevirir.
 * @method mixed ToExcelValue($value) verilen değeri excele aktarırken uyugun
 * formattan geçiriri
 * @method mixed ToCondition($value) verilen değerinin sql WHERE e uygun hale getirir
 * @method mixed ToSet($value) verilen değerinin sql SET e uygun hale getirir
 * @method bool IsValidForWhere($value) verilen değerinin where yazılmak için
 * uygunluğunu kontrol eder.
 * @method FormItem GetFormItem() varsayılan html nesne karşılığını döndürür (Her çağrıda nesneyi sıfırdan oluşturur)
 */
class IVarBase
{
	public $Name		= '';
	public $DisplayName = '';
	public $DataBound	= true;

	/**
	 * Init fonksiyonunda setlenmiş mi?, sorgu da var mı?
	 * @var bool
	 */
	public $IsSet = false;
	/**
	 * Kullanıcı tarafından görünebilirlik
	 * @var bool
	 */
	public $Visible = true;

	public $ExtAttributes = null;

	private $TypeObj	= null;

	public function __construct($type)
	{
		$this->TypeObj = VarTypes::GetInst($type);
	}

	public function __call($name, $arguments)
	{
		return call_user_func_array(array($this->TypeObj, $name), $arguments);
	}

	public function GetTypeObj()
	{
		return $this->TypeObj;
	}

	public function __get($name)
	{
		return $this->TypeObj->{$name};
	}

	public function __set($name, $value)
	{
		$this->TypeObj->{$name} = $value;
	}

	public function GetTypeClassName()
	{
		return get_class($this->TypeObj);
	}

	public function IsStringType($checkParent = false)
	{
		if ($checkParent)
			return is_a($this->TypeObj, 'VarStr');
		else
			return $this->__get('Type') == VarTypes::STRING;
	}
}
