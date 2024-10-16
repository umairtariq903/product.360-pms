<?php
require_once KNJIZ_DIR . 'dogru/DataGrid/DataTableBase.php';
class DbModelForm extends DataTableBase
{
	/**
	 * @var PageController
	 */
	public $Page = null;
	public $ModelDb = null;
	public $Id = '';
	public $ColumnCount = 1;
	public $Border = false;
	public $CustomSaveFunc = '';
	public $CustomLoadFunc = '';
	public function __construct($page, $id = null)
	{
		if($id === null)
			$id = intval(@$_GET['id']);

		$this->Page = $page;
		$this->Page->DbModelForm = $this;
		$this->Id = $id;
	}

	/**
	 * @param type $type
	 * @return \DataField
	 */
	public function GetNewColumn($type)
	{
		return new DataField($type);
	}

	/**
	 * @return DbModelForm
	 */
	public static function Get($page, $model, $visibleColumns = array(), $id = null)
	{
		$form = new DbModelForm($page, $id);
		$form->Build($model, $visibleColumns);
		foreach($form->Columns as $col)
			$form->CheckJsRequirements($col->GetFormItem());
		return $form;
	}

	/**
	 * @return ModelBase
	 */
	public function GetObj()
	{
		static $obj = null;
		if ($obj != null)
			return $obj;
		$tempObjId = @$_GET['TempObj'];
		if ($this->Page->Data)
			return $obj = $this->Page->Data;
		if ($tempObjId && isset($_SESSION['TempObj'][$tempObjId]))
			return $_SESSION['TempObj'][$tempObjId];
		/* @var $model ModelDb */
		if (is_a($this->ModelDb, 'ModelDb'))
			$obj = $this->ModelDb->GetById($this->Id, TRUE);
		else
			$obj = (object)$this->ModelDb;
		if(!$obj)
		{
			$page = PageController::$_CurrentInstance;
			$isAjax = @$_GET['ajax'] == '1' || @$_GET['grid'] == '1';
			if ($isAjax)
//				ThrowException("Aranan kayıt bulunamadı(Id = $this->Id)");
				ThrowException("Aranan kayıt bulunamadı");
//			return $page->ShowRecordNotFound("Aranan kayıt bulunamadı veya yayından kaldırılmıştır(Id = $this->Id)");
			return $page->ShowRecordNotFound("Aranan kayıt bulunamadı veya yayından kaldırılmıştır");
		}
		if ($tempObjId)
		{
			$obj->SetFromArray($_GET);
			$_SESSION['TempObj'][$tempObjId] = $obj;
		}
		return $this->Page->Data = $obj;
	}

	/**
	 * @param FormItem $formItem
	 */
	public function CheckJsRequirements($formItem)
	{
		$page = PageController::$_CurrentInstance;
		if (!$page)
			return;
		foreach($formItem->JsRequirements as $jsFile)
			$page->AddResource($jsFile);
	}

	public function GetTable($groupName = null, $ColumnCount = 0, $border = null)
	{
		$ColumnCount = $ColumnCount > 0 ? $ColumnCount : $this->ColumnCount;
		$border = $border !== NULL ? $border : $this->Border;

		list($fields, $hiddens) = $this->GetTableFields($groupName, $ColumnCount);

		$table = HtmlElement::Get('table')
			->addClass($border ? 'tb_input' : 'tb_input_base');
		$cols = HtmlElement::Get('colgroup')->appendTo($table);
		for($i=0; $i<$ColumnCount; $i++)
		{
			HtmlElement::Get('col', true)->attr('width', '10%')->appendTo($cols);
			HtmlElement::Get('col', true)->attr('width', '*')->appendTo($cols);
		}
		foreach($fields as $row)
		{
			$tr = HtmlElement::Get('tr')->appendTo($table);
			foreach($row as $field)
			{
				$col = $field->Column;
				$cp = $col->Colspan * 2 - 1;
				if ($col->Name == parent::DBFORM_SUB_TITLE)
				{
					$td = HtmlElement::Get('td')->attr('sub_title_name', $col->Name)->appendTo($tr);
					$td->addClass('dbform_sub_title');
					$td->addClass('td_input_top_caption');
					$td->innerHTML($col->DisplayName);
					$td->attr('colspan', $cp + 1);
					if($col->ExtAttributes && is_array($col->ExtAttributes))
						foreach($col->ExtAttributes as $value)
						{
							$parts = explode('=', $value);
							$td->attr($parts[0], $parts[1]);
						}
					if ($col->GroupName)
						$td->attr('group', $col->GroupName);
					continue;
				}
				$td = HtmlElement::Get('td')->attr('label_name', $col->Name)->appendTo($tr);
				$textArea = $field->TopLabel == 1;
				if($textArea)
					$td->addClass('td_input_top_caption');
				else
					$td->addClass('td_input_caption');
				if ($col->GroupName)
					$td->attr('group', $col->GroupName);
				$label = $col->DisplayName;
				if($field->request)
					$label .= HtmlElement::GetSpan(' * ', 'required_field');
				HtmlElement::Get('label')->attr('for', $field->id)->innerHTML($label . ' : ')->appendTo($td);
				if($textArea)
				{
					$cp++;
					$td->attr('colspan', $cp);
					$tr = HtmlElement::Get('tr')->appendTo($table);
				}
				$td = HtmlElement::Get('td')->attr('field_name', $col->Name);
				if ($col->GroupName)
					$td->attr('group', $col->GroupName);
				if($col->Readonly)
					$td->addClass('td_input_read');
				else
					$td->addClass('td_input_data');
				$td->innerHTML($field)->attr('colspan', $cp)->appendTo($tr);
			}
		}
		if($hiddens)
		{
			$div = HtmlElement::GetDIV()->appendTo($table)->css('display', 'none');
			foreach($hiddens as $fld)
				$fld->appendTo($div);
		}
		return $table;
	}

	protected function GetTableFields($groupName, $ColumnCount)
	{
		$fields = array();
		$row = 0;
		$colCount = 0;
		$obj = $this->GetObj();
		$fList = array();
		$hiddens = array();
		foreach($this->Columns as $col)
		{
			if($groupName !== NULL && $groupName != $col->GroupName)
				continue;
			/* @var $col DataField */
			// VarType içindeki public ve global değişkenleri
			// FormItem nesnesine doğrudan aktarıyoruz
			$field = $col->GetFormItem();
			if (method_exists($col, 'GetTypeObj'))
			{
				$props = ObjectLib::GetGlobalPropNamesInClass($col->GetTypeObj());
				foreach($props as $prop)
					$field->{$prop} = $col->{$prop};
			}
			if ($col->Addon)
				$field->attr('addon', json_encode($col->Addon));
			$col->DataObj = $obj;
			$field->UpdateFromColumn($col);
			$cb = array($this->Page, $field->Column->FieldRenderer);
			if (is_callable($cb))
				call_user_func($cb, $field);
			$fList[$col->Name] = $field;
			if(property_exists($col, 'Visible') && ! $col->Visible)
			{
				$hiddens[$col->Name] = $field;
				continue;
			}
			if($colCount >= $ColumnCount)
			{
				$row++;
				$colCount = 0;
			}
			if ($col->Name == parent::DBFORM_SUB_TITLE)
				$col->Colspan = $ColumnCount;
			$colCount += $col->Colspan;
			$fields[$row][] = $field;
		}

		return array($fields, $hiddens);
	}

	/**
	 *
	 * @param string $groupName
	 * @param int $ColumnCount
	 * @param int $border
	 * @return string
	 */
	public function GetTableBS($groupName = null, $ColumnCount = 0, $border = null, $labelSizeSm = 3, $labelSizeLg = 2)
	{
		$ColumnCount = $ColumnCount > 0 ? $ColumnCount : $this->ColumnCount;
		$border = $border !== NULL ? $border : $this->Border;

		list($fields, $hiddens) = $this->GetTableFields($groupName, $ColumnCount);

		$table = HtmlElement::Get('DIV')->addClass('form-horizontal');
		if ($border)
			$table->addClass('form-bordered');
		$body = HtmlElement::Get('DIV')->addClass('form-body')->appendTo($table);
		foreach($fields as $row)
		{
			$tr = HtmlElement::Get('DIV')->addClass("form-group form-group-sm")
					->appendTo($body);
			foreach($row as $field)
			{
				$col = $field->Column;
				if ($col->Name == parent::DBFORM_SUB_TITLE)
				{
					$div = HtmlElement::Get('DIV')
						->addClass('col-sm-12')
						->appendTo($tr);
					$td = HtmlElement::Get('DIV')
						->attr('sub_title_name', $col->Name)
						->appendTo($div);
					$td->addClass('dbform_sub_title');
					$td->addClass('td_input_top_caption');
					$td->innerHTML($col->DisplayName);
					if($col->ExtAttributes && is_array($col->ExtAttributes))
						foreach($col->ExtAttributes as $value)
						{
							$parts = explode('=', $value);
							$td->attr($parts[0], $parts[1]);
						}
					if ($col->GroupName)
						$td->attr('group', $col->GroupName);
					continue;
				}
				$colClass = $field->TopLabel == 1 ? '' : "col-sm-$labelSizeSm col-lg-$labelSizeLg";
				$td = HtmlElement::Get('LABEL')
					->addClass("control-label $colClass")
					->attr('for', $field->id)
					->attr('label_name', $col->Name)
					->innerHTML($col->DisplayName)
					->appendTo($tr);
				if ($col->GroupName)
					$td->attr('group', $col->GroupName);
				if($field->request)
					HtmlElement::GetSpan(' * ', 'required_field')->appendTo($td);
				$count = count($row);
				$colSm = ceil((12 - $labelSizeSm)  / ($count * 2 - 1));
				$colLg = ceil((12 - $labelSizeLg) / ($count * 2 - 1));
				$colClass = $field->TopLabel == 1 ? '' : "col-sm-$colSm col-lg-$colLg";
				$td = HtmlElement::Get('DIV')
					->addClass($colClass)
					->attr('field_name', $col->Name);
				if ($col->GroupName)
					$td->attr('group', $col->GroupName);
				$field->addClass('form-control input-sm');
				$td->innerHTML($field)->appendTo($tr);
			}
		}
		if($hiddens)
		{
			$div = HtmlElement::GetDIV()->appendTo($body)->css('display', 'none');
			foreach($hiddens as $fld)
				$fld->appendTo($div);
		}
		return $table;
	}

	public function GetFieldItem($fName, $readOnly = false)
	{
		$col = $this->Columns[$fName];
		/* @var $col DataField */
		$field = $col->GetFormItem();
		$field->Readonly = $readOnly;
		$col->DataObj = $this->GetObj();
		$field->UpdateFromColumn($col);
		$cb = array($this->Page, $field->Column->FieldRenderer);
		if (is_callable($cb))
			call_user_func($cb, $field);
		return $field;
	}

	public function GetFieldItemBS($fName, $readOnly = false)
	{
		$field = $this->GetFieldItem($fName,$readOnly);
		$field->addClass("form-control");
		return $field;
	}

	public function Save()
	{
		$obj = $this->GetObj();
		if (@$_GET['TempObj'])
			return 1;
		return $obj->Save();
	}

	public function GetSaveButton($jsSaveFunc = '')
	{
		return "<button class=\"btn_ui_save\" onclick=\"DbModelForm_Save('$jsSaveFunc')\">Kaydet</button>";
	}

	/**
	 * @return FormItem
	 */
	public function FormItemFromName($name)
	{
		$col = $this->ColumnFromName($name, -10);
		$f = $col->GetFormItem();
		$f->Column->DataObj = $this->GetObj();
		return $f->UpdateFromColumn($col);
	}

	/**
	 * @return 1|0
	 */
	public function HasFile()
	{
		foreach($this->Columns as $col)
		{
			if(method_exists($col, 'GetTypeClassName'))
				$class = $col->GetTypeClassName();
			else
				continue;
			if ($class == 'VarAppFile' || in_array('VarAppFile', class_parents($class)))
				return 1;
			if ($class == 'VarAppFileList' || in_array('VarAppFileList', class_parents($class)))
				return 1;
		}
		return 0;
	}
}
