<?php
abstract class VendorBase extends ModelBase
{
	public $Id = 0;
	public $Code = "";
	public $Name = "";
}

abstract class VendorDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   * /*fields*/ FROM vendors WHERE (1=1) ORDER BY (1)';
}

class VendorModelMap extends ModelMap
{
	public $Name = 'vendors';
	public $ModelName = 'Vendor';
	protected $DbFields = array(
		"Id"=>array(1006,"id","vendors.id","Id","int",0,1,0,0),
		"Code"=>array(1002,"code_","vendors.code_","Code","string",0,1,0,""),
		"Name"=>array(1002,"name_","vendors.name_","Name","string",0,1,0,"")
	);
}