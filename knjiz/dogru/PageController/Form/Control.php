<?php
/**
 * @property string $fontWeight
 * @property string $color
 * @property string $fontSize
 * @property string $border
 */
class CssCollection
{
}

abstract class Control
{
	const TYPE_TEXT		= 1;
	const TYPE_HIDDEN	= 2;
	const TYPE_CHECKBOX	= 3;
	const TYPE_RADIO	= 4;
	const TYPE_SELECT	= 5;
	const TYPE_OPTION	= 6;
	const TYPE_HTML		= 7;

	public $Id	= '';
	public $Name= '';
	public $ClassName = '';
	/**
	 *
	 * @var CssCollection
	 */
	public $Style;
	public $Attributes = array();
	public $Type = 1;

	public function __construct($Id)
	{
		$this->Id = $Id;
		$this->Name = $Id;
		$this->Style = new CssCollection();
	}
}

class InputControl extends Control
{
	const TYPE_TEXT		= 1;
	const TYPE_HIDDEN	= 2;
	const TYPE_CHECKBOX	= 3;
	const TYPE_RADIO	= 4;
	const TYPE_OPTION	= 5;

	public $Text  = "";
	public $Value = "";

	public function __construct($Id, $Value = '')
	{
		parent::__construct($Id);
		$this->Value = $Value;
	}
}

class ListControl extends Control
{
	public $Value = "";
	public $SelectedIndex = -1;
	/**
	 *
	 * @var InputControl[]
	 */
	public $Options = array();

	public function __construct($Id, $Value = '', $Options = array())
	{
		parent::__construct($Id);
		$this->Value = $Value;
		$this->Options = $Options;
	}

}

class HtmlControl extends Control
{
	public $Html = "";

	public function __construct($Id, $Html = '')
	{
		parent::__construct($Id);
		$this->Html = $Html;
	}
}
