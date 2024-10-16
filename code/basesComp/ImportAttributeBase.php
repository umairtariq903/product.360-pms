<?php
abstract class ImportAttributeBase extends ModelBase
{
	public $Id = 0;
	public $ImportId = 0;
	public $AttributeId = 0;
	public $Value = "";
	public $Condition = "";
	public $AttributeName = "";
	public $AttributeProductFieldName = "";
}

abstract class ImportAttributeDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   T1.*,T2.name_ AS attribute_name,T2.product_field_name AS attribute_product_field_name /*fields*/ FROM import_attributes T1 INNER JOIN p_attributes T2 ON T1.attribute_id=T2.id WHERE (1=1) ORDER BY (1)';
}

class ImportAttributeModelMap extends ModelMap
{
	public $Name = 'import_attributes';
	public $ModelName = 'ImportAttribute';
	protected $DbFields = array(
		"Id"=>array(1006,"id","T1.id","Id","int",0,1,0,0),
		"ImportId"=>array(1006,"import_id","T1.import_id","Import Id","int",0,1,0,0),
		"AttributeId"=>array(1006,"attribute_id","T1.attribute_id","Attribute Id","int",0,1,0,0),
		"Value"=>array(1002,"value_","T1.value_","Value","string",0,1,0,""),
		"Condition"=>array(1002,"condition_","T1.condition_","Condition","string",0,1,0,""),
		"AttributeName"=>array(1002,"attribute_name","T2.name_","Attribute Name","string",0,0,0,""),
		"AttributeProductFieldName"=>array(1002,"attribute_product_field_name","T2.product_field_name","Attribute Product Field Name","string",0,0,0,"")
	);
}