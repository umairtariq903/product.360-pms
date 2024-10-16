<?php
abstract class ProductAttributeBase extends ModelBase
{
	public $Id = 0;
	public $ProductId = 0;
	public $AttributeId = 0;
	public $Value = "";
	public $AttributeName = "";
}

abstract class ProductAttributeDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   T1.*,T2.name_ AS attribute_name /*fields*/ FROM product_attributes T1 INNER JOIN p_attributes T2 ON T1.attribute_id=T2.id WHERE (1=1) ORDER BY (1)';
}

class ProductAttributeModelMap extends ModelMap
{
	public $Name = 'product_attributes';
	public $ModelName = 'ProductAttribute';
	protected $DbFields = array(
		"Id"=>array(1006,"id","T1.id","Id","int",0,1,0,0),
		"ProductId"=>array(1006,"product_id","T1.product_id","Product Id","int",0,1,0,0),
		"AttributeId"=>array(1006,"attribute_id","T1.attribute_id","Attribute Id","int",0,1,0,0),
		"Value"=>array(1002,"value_","T1.value_","Value","string",0,1,0,""),
		"AttributeName"=>array(1002,"attribute_name","T2.name_","Attribute Name","string",0,0,0,"")
	);
}