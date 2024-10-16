<?php
abstract class ProjectBase extends ModelBase
{
	public $Id = 0;
	public $Name = "";
}

abstract class ProjectDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   * /*fields*/ FROM projects WHERE (1=1) ORDER BY (1)';
}

class ProjectModelMap extends ModelMap
{
	public $Name = 'projects';
	public $ModelName = 'Project';
	protected $DbFields = array(
		"Id"=>array(1006,"id","projects.id","Id","int",0,1,0,0),
		"Name"=>array(1002,"name","projects.name","Name","string",0,1,0,"")
	);
}