<?php
/**
 * @property bool $request zorunlu bir alan mı
 * @property string $value değeri
 */
class FormItem extends HtmlElement
{
	public $id = '';
	protected $value = '';
	private $request = false;
	public $JsRequirements = array();
	protected $Attrs = array('class' => 'data_form_item');
	public $TopLabel = 0;
	/**
	 *
	 * @var DbField
	 */
	public $Column = null;

	protected function attrChanged($key)
	{
		if (in_array($key, array('id', 'value')))
			$this->{$key} = $this->Attrs[$key];
	}

	/**
	 * @param string $val eğer verilirse setler
	 * @return FormItem|string değer istendiği durumlarda string dönderir.
	 */
	public function val()
	{
		$params = func_get_args();
		if(count($params) == 0)
			return $this->value;
		$this->value = "$params[0]";
		$this->attr('value', $this->value);
		return $this;
	}

	public function __get($name)
	{
		if($name == 'request')
			return $this->request;
		if($name == 'value')
			return $this->val();
		return NULL;
	}

	public function __set($name, $value)
	{
		if($name == 'request')
		{
			$this->request = $value;
			if ($this->request)
				$this->addClass('request_form_item');
			else
				$this->removeClass('request_form_item');
		}
		if($name == 'value')
			return $this->val();
	}

	/**
	 *
	 * @param DataColumn|DataField $col Grid oluşturulurken DataColumn, Form oluşturulurken DataField veriliyor
	 * @return $this
	 */
	public function UpdateFromColumn($col)
	{
		$this->Column = $col;
		$this->attr('id', $col->Name);
		$obj = $this->Column->DataObj;
		if($obj)
		{
			$regs = array();
			if (preg_match("/(.*)\.(.*)/", $col->Name, $regs))
				$val = $obj->{$regs[1]}->{$regs[2]};
			else
				$val = $obj->{$col->Name};
			$this->val($val);
		}
		if($col->Width)
			$this->css('width', $col->Width);
		if($col->Height)
			$this->css('height', $col->Height);
		$this->__set('request', $col->Required);
		if($col->Information)
			$this->attr('title', $col->Information);
		if($col->ExtAttributes && is_array($col->ExtAttributes))
			foreach($col->ExtAttributes as $value)
			{
				$parts = explode('=', $value);
				$this->attr($parts[0], $parts[1]);
			}
		if ($col->Dependency)
			$this->attr('dependency', $col->Dependency);
		return $this;
	}

	public static function Get($tag, $short = false)
	{
		return new FormItem($tag, $short);
	}
}

class FormInputStr extends FormItem
{
	public function __construct()
	{
		parent::__construct('input', true);
		$this->attr('type', 'text');
	}
}

class FormInputPass extends FormItem
{
	public function __construct()
	{
		parent::__construct('input', true);
		$this->attr('type', 'password');
	}

	public function __toString()
	{
		if ($this->Column->Readonly)
			return '****';
		return parent::__toString();
	}
}

class FormInputCheck extends FormItem
{
	public function __construct()
	{
		parent::__construct('input', true);
		$this->attr('type', 'checkbox');
	}

	/**
	 * @param string $val eğer verilirse setler
	 * @return FormItem|string değer istendiği durumlarda string dönderir.
	 */
	public function val()
	{
		$params = func_get_args();
		if(count($params) == 0)
			return $this->value;
		$isChecked = $params[0] != 0;
		$this->value = $isChecked;
		if ($isChecked)
			$this->attr('checked', 'checked');
		return $this;
	}
}

class FormInputSelect extends FormItem
{
	public $DisplayText = '';
	public $SelectNearest = false;

	public function __construct()
	{
		parent::__construct('select');
	}

	protected function attrChanged($key)
	{
		parent::attrChanged($key);
		if ($key == 'value')
		{
			$this->DisplayText = '';
			$options = $this->find('option');
			if($this->SelectNearest)
			{
				$array = array();
				foreach($options as $opt)
					$array[$opt->value] = $opt->innerHTML();
				$val = StringLib::FindNearest($array, $this->value);
			}
			else
				$val = $this->value;
			foreach($options as $opt)
			{
				/* @var $opt FormItem */
				if($opt->value == $val)
				{
					$opt->Attrs['selected'] = null;
					$this->DisplayText = $opt->innerHTML();
				}
				elseif(key_exists('selected', $this->Attrs))
					unset($opt->Attrs['selected']);
			}
		}
	}

	public static function GetFromArray($array, $useKeys = false, $keysForCat = false)
	{
		$select = new VarListItem();
		$select->BuildFromArray($array, $useKeys, $keysForCat);
		return $select->GetFormItem();
	}

	public function val()
	{
		$params = func_get_args();
		if(count($params) == 0)
			return parent::val();
		$val = $params[0];
		if ($this->attr('multiple') == '1')
		{
			$selected = is_array($val) ? $val : explode(',', $val);
			$options = $this->find('option');
			foreach($options as $opt)
				if (in_array($opt->val(), $selected))
					$opt->attr('selected', 'selected');
		}
		return parent::val($val);
	}
}

class FormTextArea extends FormItem
{
	public function __construct()
	{
		parent::__construct('textarea');
	}

	public function val()
	{
		$params = func_get_args();
		$this->attr('display_name',$this->Column->DisplayName);
		if(count($params) == 0)
			return $this->innerHTML();
		$val = $params[0];
		if (is_array($val))
			$val = implode("\n", $val);
		$this->innerHTML($val);
		return $this;
	}
}

class FormEditableDiv extends FormItem
{
	public function __construct()
	{
		parent::__construct('div');
	}

	public function val()
	{
		$params = func_get_args();
		$this->attr('contentEditable',"true");
		$this->attr('display_name',$this->Column->DisplayName);
		if(count($params) == 0)
			return $this->innerHTML();
		$val = $params[0];
		if (is_array($val))
			$val = implode("\n", $val);
		$this->innerHTML($val);
		return $this;
	}
}

class FormInputHidden extends FormItem
{
	public function __construct()
	{
		parent::__construct('input', true);
		$this->attr('type', 'hidden');
	}
}

class FormInputEmail extends FormItem
{
	public $JsRequirements = array(JS_MASKED_INPUT_EXT);
	public function __construct()
	{
		parent::__construct('input', true);
		$this->attr('var_type', 'email');
		$this->attr('type', 'text');
	}
}

class FormRichEdit extends FormTextArea
{
	public $JsRequirements = array(JS_TINYMCE);
	public function __construct()
	{
		parent::__construct();
		$this->attr('rich_edit', '1');
		$this->css('width', '99%');
	}
}

/**
 * @property string $allowedExt
 */
class FormInputFile extends FormItem
{
	const FILE_TYPE_IMAGE = 'jpg,jpeg,gif,png';
	const FILE_TYPE_VIDEO = 'mp4';
	const FILE_TYPE_DOC	= 'doc,docx,xls,xlsx,pdf';

	public $hasValue = true;
	public $Readonly = false;
	protected $allowedExt = '';

	/**
	 *
	 * @param int $type
	 *	1= Single file
	 *  2= Multi file
	 *  3= image
	 */
	public function __construct($type = 1, $readOnly = false)
	{
		parent::__construct('input', true);
		$this->Readonly = $readOnly;
		$this->attr('type', 'file')
			->attr('upload_type', $type)
			->addClass('input-upload');
		if ($this->Readonly)
			$this->attr('disabled', 'disabled');
	}

	public function __set($name, $value)
	{
		if ($name == 'allowedExt')
			return $this->attr('allowed_ext', $value);
		parent::__set($name, $value);
	}

}

class FormInputFileContainer extends FormItem
{
	/**
	 * @var FormInputFile
	 */
	public $input = null;

	/**
	 * @var HtmlElement
	 */
	public $a = null;

	/**
	 * @var FormItem
	 */
	public $button = null;
	public $Initialized = false;
	public $ReadOnly = false;
	public $JsRequirements = array(JS_FILEUPLOAD);
	public $MaxFileSize = -1;

	public function __construct()
	{
		parent::__construct('div');
		$this->attr('upload_type', 1);
	}

	public function html()
	{
		if ($this->Initialized)
			return parent::html();
		$this->Initialized = true;
		$this->input = new FormInputFile();
		$this->input->addClass('single');
		$this->button= HtmlElement::GetButton('Delete', '', 'btn-del-upload');
		$this->a = HtmlElement::GetA('', '', '');

		$div = HtmlElement::GetDIV('', 'tbl-file-upload')->appendTo($this);
		$elements = array($this->input, $this->a, $this->button);
		$classes = array('upload', 'info', 'delete');
		foreach($elements as $i => $el)
		{
			$span = HtmlElement::GetSpan('', $classes[$i])->appendTo($div);
			$el->appendTo($span);
		}

		return parent::html();
	}

	public function val()
	{
		$params = func_get_args();
		if(count($params) == 0)
			return '';
		if (!$this->Initialized)
			$this->html();
		/* @var $value AppFile */
		$value = $params[0];
		if (! ($value instanceof AppFile))
			$value = new AppFile();
		$url = $value->Link;

		$this->input
			->attr('file_name', $value->Ad)
			->attr('file_url', $value->Yol)
			->attr('file_id', DgrCode::Encode($value->Id))
			->attr('max_file_size', $this->MaxFileSize);
		$this->a
			->attr('href', 'javascript:void(0)')
			->attr('onclick', 'Page.Download("' . $url . '")')
			->innerHTML($value->Ad);

		if ($this->ReadOnly)
		{
			$this->input->css('display', 'none');
			$this->button->css('display', 'none');
		}
		return $this;
	}

	public function SetAllowedExt($ext)
	{
		$this->html();
		$ext = preg_replace("/[^a-z0-9,]/i", "", trim($ext));
		$this->input->allowedExt = $ext;
		return $this;
	}

	public function SetReadOnly($readOnly)
	{
		$this->ReadOnly = $readOnly;
		return $this;
	}
}

class FormInputImageContainer extends FormItem
{
	/**
	 * @var FormInputFile
	 */
	public $input = null;
	/**
	 *@var HtmlElement
	 */
	public $img = null;
	/**
	 *
	 * @var HtmlElement
	 */
	public $DefaultImgUrl = 'images/sample_logo.png';
	public $DefaultImgWidth = 180;
	public $AllowedExt = 'jpg,jpeg,gif,png';
	public $MaxResolution = '1024x768';
	public $MaxFileSize = '2MB';
	public $Aspect = 0;
	public $Deletable = true;
	public $Initialized = false;
	public $JsRequirements = array(JS_FILEUPLOAD);

	public $DisplayText = '';

	public function __construct()
	{
		parent::__construct('div');
		$this->attr('upload_type', 3);
		$this->addClass('image-upload');
	}

	public function html()
	{
		if (! $this->Initialized)
		{
			$this->input = new FormInputFile(3);
			$this->input->addClass('image');
			$this->input
				->attr('allowed_ext', $this->AllowedExt)
				->attr('max_resolution', $this->MaxResolution)
				->attr('max_file_size', $this->MaxFileSize)
				->attr('aspect', $this->Aspect);
			$this->img = HtmlElement::GetImg($this->DefaultImgUrl, 'img-avatar')
				->attr('file_name', '')
				->attr('file_url', '');
			$this->attr('deletable', $this->Deletable ? 1 : 0);
			$this->css('font-size', '0.9em');
			$this->addChild($this->img);
			$this->addChild($this->input);
		}
		$this->Initialized = true;
		$this->img
				->attr('src', $this->DefaultImgUrl)
				->attr('width', $this->DefaultImgWidth)
				->attr('default_src', $this->DefaultImgUrl);
		$img = ObjectLib::CloneObj($this->img);
		$url = $this->input->attr('file_url');
		if($url)
			$img->attr('src', $url);
		$this->DisplayText = $img->html();
		return parent::html();
	}

	public function val()
	{
		$params = func_get_args();
		if(count($params) == 0)
			return '';
		if (!$this->Initialized)
			$this->html();
		/* @var $value AppFile */
		$value = $params[0];
		$url = RelativePath($value->GetTamYol());
		$this->input
			->attr('file_name', $value->Ad)
			->attr('file_url', $url)
			->attr('file_id', DgrCode::Encode($value->Id));
		return $this;
	}
}

class FormInputFileListContainer extends FormItem
{
	/**
	 * @var FormInputFile
	 */
	public $input = null;
	/**
	 *@var HtmlElement
	 */
	public $table = null;
	/**
	 * @var FormInputHidden
	 */
	public $hidden = null;
	public $Categories = null;
    public $allowedExt = "";
    public $accept = "";

	public $ShowFileDesc = true;
	public $ShowFileActive = false;
	public $ShowFileCat = false;
	public $Initialized = false;
	public $Readonly = false;
	public $JsRequirements = array(JS_FILEUPLOAD);

	public function __construct($allowedExt = "")
	{
		parent::__construct('div');
		$this->attr('upload_type', 2);
        if ($allowedExt != "")
        {
            $this->allowedExt = $allowedExt;
            $arr = explode(",",$allowedExt);
            foreach($arr as $k => $v)
                $arr[$k] = "." . $v;
            $this->accept = implode(",",$arr);
        }
	}

	public function SetCategories($categories)
	{
		$this->Categories = $categories;

		return $this;
	}

	public function EnableDesc($enable)
	{
		$this->ShowFileDesc = $enable;

		return $this;
	}

	public function EnableCat($enable)
	{
		$this->ShowFileCat = $enable;

		return $this;
	}

	public function SetReadonly($readonly)
	{
		$this->Readonly = $readonly;

		return $this;
	}

	public function SetValue($value)
	{
		if (! $value || ! (is_array($value) || is_a($value, 'ModelBaseArray')))
			$value = array();

		$this->val($value);

		return $this;
	}


	public function html()
	{
		$this->Initialized = true;
		if (!$this->input)
		{
			$this->input = new FormInputFile(1, $this->Readonly);
			$this->input->appendTo($this)
				->addClass('multiple')
				->attr('upload_type', 2);
            if ($this->allowedExt != "")
                $this->input->attr("allowed_ext",$this->allowedExt);
            if ($this->accept != "")
                $this->input->attr("accept",$this->accept);
			$this->table = HtmlElement::Get('TABLE')
				->addClass('tbl-file-upload multiple-upload')
				->attr('border', '1')
				->appendTo($this);
			if ($this->Readonly)
				$this->input->css('display', 'none');
		}
		else
			$this->table->Children = array();

		$fields = array('No');
		if ($this->ShowFileActive)
			$fields[] = 'Aktif';
		$fields[] = 'Dosya';
		if ($this->ShowFileCat)
			$fields[] = 'Kategori';
		if ($this->ShowFileDesc)
			$fields[] = array('Aciklama', 'Açıklama');
		$fields[] = 'Sil';

		$th = HtmlElement::Get('THEAD')->appendTo($this->table)
			->addClass('ui-widget-header');
		foreach($fields as $f)
		{
			if (is_array($f))
				$f = $f[1];
			$td = HtmlElement::Get('TD')->innerHTML($f)->appendTo($th);
		}
		$tbody = HtmlElement::Get('TBODY')
			->appendTo($this->table)
			->attr('var_type', 'sortable');
		$temp = HtmlElement::Get('TR')->appendTo($tbody)
				->addClass('template-row');
		foreach($fields as $f)
		{
			if (is_array($f))
				$f = $f[0];
			$td = HtmlElement::Get('TD')
				->appendTo($temp);
			if ($f == 'Dosya')
				HtmlElement::Get('A')
					->attr('href', '')
					->addClass('Ad')
					->appendTo($td);
			else if ($f == 'Aciklama')
				$input = $this->GetInputField($f, $td);
			else if ($f == 'Kategori')
			{
				$cats = $this->Categories;
				$input = $this->GetInputField($f, $td, $cats);
				if ($cats)
					foreach($cats as $index => $value)
						FormItem::Get('option')
							->val($index)
							->innerHTML($value)
							->appendTo($input);
			}
			else if ($f == 'Sil' && !$this->Readonly)
			{
				HtmlElement::GetButton('<i class="fa fa-remove"></i>', '', 'btn btn-danger btn-xs btn-del-upload')
					->appendTo($td);
				$td->css('width', '30px')->attr('align', 'center');
			}
			else if ($f == 'No')
				$td->css('width', '25px')->addClass('SiraNo');
			else if ($f == 'Aktif')
			{
				$input = new FormInputCheck();
				$input->appendTo($td)
					->addClass($f)
					->attr('field_name', $f);
				$td->attr('align', 'center')->css('width', '30px');
			}
		}
		return self::parentHtml();
	}

	protected function parentHtml()
	{
		return parent::html();
	}

	protected function GetInputField($name, $parent, $select = false)
	{
		if ($select)
			$input = new FormInputSelect();
		else
			$input = new FormInputStr();
		$input->css('width', '98%')
			->appendTo($parent)
			->addClass($name)
			->attr('field_name', $name);
		if ($this->Readonly)
			$input->attr('disabled', 'disabled');
		return $input;
	}

	public function val()
	{
		$params = func_get_args();
		if(count($params) == 0)
			return '';
		if (!$this->Initialized)
			$this->html();
		/* @var $files AppFile[] */
		$files = $params[0];
		if (! is_a($files, 'ModelBaseArray') && !is_array($files) )
			return false;
		$files = ObjectLib::GetStdObj($files, array('Id', 'Aktif', 'Ad', 'Link', 'Yol', 'Kategori', 'Aciklama','WillBeDeleted','WillBeCascadeUpdate','CascadeChanged'), 'Id');
		if ($this->hidden == null)
		{
			$this->hidden = new FormInputHidden();
			$this->hidden->appendTo($this)
				->addClass('app-file-list');
		}
		$this->hidden->val(str_replace(array("&quot;", "&#34;"), "", Kodlama::JSON($files)));
		return $this;
	}
}

class FormInputImageListContainer extends FormInputFileListContainer
{
	public $DefaultImgWidth = 180;
	public $AllowedExt = 'jpg,jpeg,gif,png';
	public $MaxResolution = '1024x768';
	public $MaxFileSize = '2MB';
	public $Aspect = 0;
	public $JsRequirements = array(JS_FANCYBOX);

	public function __construct()
	{
		parent::__construct('div');
		$this->attr('upload_type', 4);
	}

	public function html()
	{
		$this->Initialized = true;
		if (!$this->input)
		{
			$this->input = new FormInputFile(1, $this->Readonly);
			$this->input->appendTo($this)
				->addClass('multiple')
				->attr('multiple',1)
				->attr('upload_type', 4)
				->attr('allowed_ext', $this->AllowedExt)
				->attr('max_resolution', $this->MaxResolution)
				->attr('max_file_size', $this->MaxFileSize)
				->attr('aspect', $this->Aspect);
			if ($this->Readonly)
				$this->input->css('display', 'none');
		}

		return parent::parentHtml();
	}
}
