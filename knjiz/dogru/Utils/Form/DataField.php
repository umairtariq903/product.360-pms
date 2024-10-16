<?php
class DataField extends IVarBase
{
	public $Information = '';
	public $Colspan = 1;
	public $Readonly = 0;
	public $Width = '';
	public $Height = '';
	public $Required = false;
	public $GroupName = '';
	public $Disabled = false;
	public $Dependency = '';
	/**
	 * PageController içinde bu sütunu render eden
	 * metodun adı
	 * @var string
	 */
	public $FieldRenderer = '';
	/**
	 * @var ModelBase|stdClass
	 */
	public $DataObj = null;
}