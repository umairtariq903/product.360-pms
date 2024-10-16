<?php
abstract class CompanyBase extends ModelBase
{
	public $Id = 0;
	public $Name = "";
}

abstract class CompanyDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   * /*fields*/ FROM companies WHERE (1=1) ORDER BY (1)';
}

class CompanyModelMap extends ModelMap
{
	public $Name = 'companies';
	public $ModelName = 'Company';
	protected $DbFields = array(
		"Id"=>array(1006,"id","companies.id","Id","int",0,1,0,0),
		"Name"=>array(1002,"name","companies.name","Name","string",0,1,0,"")
	);
}