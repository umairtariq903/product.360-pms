<?php
abstract class ExportTransactionBase extends ModelBase
{
	public $Id = 0;
	public $ExportRuleId = 0;
	public $PAttributeId = 0;
	public $ConditionRule = 0;
	public $Value = "";
}

abstract class ExportTransactionDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   * /*fields*/ FROM export_transactions WHERE (1=1) ORDER BY (1)';
}

class ExportTransactionModelMap extends ModelMap
{
	public $Name = 'export_transactions';
	public $ModelName = 'ExportTransaction';
	protected $DbFields = array(
		"Id"=>array(1006,"id","export_transactions.id","Id","int",0,1,0,0),
		"ExportRuleId"=>array(1006,"export_rule_id","export_transactions.export_rule_id","Export Rule Id","int",0,1,0,0),
		"PAttributeId"=>array(1006,"p_attribute_id","export_transactions.p_attribute_id","P Attribute Id","int",0,1,0,0),
		"ConditionRule"=>array(1006,"condition_rule","export_transactions.condition_rule","Condition Rule","int",0,1,0,0),
		"Value"=>array(1002,"value_","export_transactions.value_","Value","string",0,1,0,"")
	);
}