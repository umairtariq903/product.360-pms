<?php
/**
 * @property string $YetkiKontrol işlem için yetki kontrolü yapılacaksa verilmeli
 */
class BtnIslem
{
	public static $YetkiIslemFunc = array('YetkiKontrol', 'YetkiIslem');
	public static $YetkiGormeFunc = array('YetkiKontrol', 'YetkiGorme');

	public $text = '';
	public $icon = '';
	public $enabled = true;
	public $visible = true;
	public $deleted = false;
	/**
	 * @var string Buttton tıklanması durumunda çalışacak olan JS fonksiyon adı.<br>
	 * eğer bir tr içinde ise tr nin row_id sini ve kendisini parametre olarak
	 * fonksiyona otomatik gönderilir.
	 * @example function ButtonClicked(row_id, tr)
	 */
	public $CallBackFunc = null;

	protected $YetkiKontrol = null;

	public function __sleep()
	{
		return array('icon', 'text', 'CallBackFunc', 'YetkiKontrol');
	}

	public function __construct($CallBackFunc = null, $yetki = '')
	{
		// Mevcut sayfada görme ve işlem yetkilerini kontrol edelim
		$this->SetYetkiKontrol($yetki);
		$this->CallBackFunc = $CallBackFunc;
	}

	public static function GetIns($text, $callback = null, $icon = '', $yetki = '')
	{
		$btn = new BtnIslem($callback, $yetki);
		$btn->text =  $text;
		$btn->icon = $icon;
		return $btn;
	}

	public function __get($name)
	{
		if ($name == 'YetkiKontrol')
			return $this->YetkiKontrol;
	}

	public function SetYetkiKontrol($value)
	{
		if($value == null)
		{
			$this->enabled = true;
			$this->visible = true;
		}
		else
		{
			if(is_callable(self::$YetkiIslemFunc))
				$this->enabled = call_user_func(self::$YetkiIslemFunc, $value);
			if(is_callable(self::$YetkiGormeFunc))
				$this->visible = call_user_func(self::$YetkiGormeFunc, $value);
		}
		$this->YetkiKontrol = $value;
	}

	public function __set($name, $value)
	{
		if ($name == 'YetkiKontrol')
			$this->SetYetkiKontrol($value);
	}
}

class BtnIslemList extends BtnIslem
{
	public $text = 'İşlemler';
}

class BtnIslemSil extends BtnIslem
{
	public $text = 'Sil';
	public $icon = 'ui-icon-trash';
	public $CallBackFunc = 'DataGridDelete';
}

class BtnIslemCopy extends BtnIslem
{
	public $text = 'Kopyala';
	public $icon = 'ui-icon-copy';
}

class BtnIslemSec extends BtnIslem
{
	public $text = 'Seç';
	public $icon = 'ui-icon-circle-plus';
}

class BtnIslemPrint extends BtnIslem
{
	public $text = 'Yazdır';
	public $icon = 'ui-icon-print';
}
