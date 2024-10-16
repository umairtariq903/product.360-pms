<?php
abstract class ExportFilterBase extends ModelBase
{
	public $Id = 0;
	public $ExportRuleId = 0;
	public $Operator = "";
	public $PAttributeId = 0;
	public $ConditionRule = 0;
	public $Value = "";
	public $FilterSort = 0;
}

abstract class ExportFilterDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   * /*fields*/ FROM export_filters WHERE (1=1) ORDER BY filter_sort,(1)';
}

class ExportFilterModelMap extends ModelMap
{
	public $Name = 'export_filters';
	public $ModelName = 'ExportFilter';
	protected $DbFields = array(
		"Id"=>array(1006,"id","export_filters.id","Id","int",0,1,0,0),
		"ExportRuleId"=>array(1006,"export_rule_id","export_filters.export_rule_id","Export Rule Id","int",0,1,0,0),
		"Operator"=>array(1002,"operator","export_filters.operator","Operator","string",0,1,0,""),
		"PAttributeId"=>array(1006,"p_attribute_id","export_filters.p_attribute_id","P Attribute Id","int",0,1,0,0),
		"ConditionRule"=>array(1006,"condition_rule","export_filters.condition_rule","Condition Rule","int",0,1,0,0),
		"Value"=>array(1002,"value_","export_filters.value_","Value","string",0,1,0,""),
		"FilterSort"=>array(1006,"filter_sort","export_filters.filter_sort","Filter Sort","int",0,1,0,0)
	);
}