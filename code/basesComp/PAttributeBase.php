<?php
abstract class PAttributeBase extends ModelBase
{
	public $Id = 0;
	public $Name = "";
	public $ProductFieldName = "";
	public $IsVendor = 0;
	public $Important = 0;
	public $Type = 0;
}

abstract class PAttributeDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   * /*fields*/ FROM p_attributes WHERE (1=1) ORDER BY (1)';
}

class PAttributeModelMap extends ModelMap
{
	public $Name = 'p_attributes';
	public $ModelName = 'PAttribute';
	protected $DbFields = array(
		"Id"=>array(1006,"id","p_attributes.id","Id","int",0,1,0,0),
		"Name"=>array(1002,"name_","p_attributes.name_","Name","string",0,1,0,""),
		"ProductFieldName"=>array(1002,"product_field_name","p_attributes.product_field_name","Product Field Name","string",0,1,0,""),
		"IsVendor"=>array(1006,"is_vendor","p_attributes.is_vendor","Is Vendor","int",0,1,0,0),
		"Important"=>array(1006,"important","p_attributes.important","Important","int",0,1,0,0),
		"Type"=>array(1006,"type_","p_attributes.type_","Type","int",0,1,0,0)
	);
}