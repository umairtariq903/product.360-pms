<?php
abstract class ExportRuleBase extends ModelBase
{
	public $Id = 0;
	public $ExportId = 0;
	public $Name = "";
	public $Aktif = 1;
	public $RuleSort = 0;
	public $Transaction = 0;
	public function GetValue($name)
	{
		return @$this->{$name};
	}
	public function SetValue($name, $value)
	{
		$this->{$name} = $value;
	}
}

abstract class ExportRuleDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   * /*fields*/ FROM export_rules WHERE (1=1) ORDER BY rule_sort,(1)';
}

class ExportRuleModelMap extends ModelMap
{
	public $Name = 'export_rules';
	public $ModelName = 'ExportRule';
	protected $DbFields = array(
		"Id"=>array(1006,"id","export_rules.id","Id","int",0,1,0,0),
		"ExportId"=>array(1006,"export_id","export_rules.export_id","Export Id","int",0,1,0,0),
		"Name"=>array(1002,"name_","export_rules.name_","Name","string",0,1,0,""),
		"Aktif"=>array(1006,"aktif","export_rules.aktif","Aktif","int",0,1,0,1),
		"RuleSort"=>array(1006,"rule_sort","export_rules.rule_sort","Rule Sort","int",0,1,0,0),
		"Transaction"=>array(1006,"transaction","export_rules.transaction","Transaction","int",0,1,0,0)
	);
	protected $Relationships = array(
		array("access_field"=>"FiltersInfo","view_name"=>"","condition"=>"ExportFilter.ExportRuleId = Id","condition2"=>"","condition3"=>"","type"=>Relation::ONE_TO_MANY,"reverse_field"=>"","behaviour"=>RelationBehaviour::CASCADE_DELETE),
		array("access_field"=>"TransactionsInfo","view_name"=>"","condition"=>"ExportTransaction.ExportRuleId = Id","condition2"=>"","condition3"=>"","type"=>Relation::ONE_TO_MANY,"reverse_field"=>"","behaviour"=>RelationBehaviour::CASCADE_DELETE),
		array("access_field"=>"VendorExportInfo","view_name"=>"","condition"=>"VendorExport.Id = ExportId","condition2"=>"","condition3"=>"","type"=>Relation::ONE_TO_ONE,"reverse_field"=>"RulesInfo","behaviour"=>RelationBehaviour::DO_NOTHING)
	);
}