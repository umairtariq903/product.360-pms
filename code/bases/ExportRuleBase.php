<?php
/**
 * @property ExportFilter[] $FiltersInfo
 * @property ExportTransaction[] $TransactionsInfo
 * @property VendorExport $VendorExportInfo
 */
abstract class ExportRuleBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var int FieldName = export_id                               */
	public $ExportId = 0;

	/** @var string FieldName = name_                                */
	public $Name = "";

	/** @var int FieldName = aktif                                   */
	public $Aktif = 1;

	/** @var int FieldName = rule_sort                               */
	public $RuleSort = 0;

	/** @var int FieldName = transaction                             */
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

/**
 * @method ExportRule GetById(int $id, bool $AutoCreate = false)
 * @method ExportRule GetFirst(array|object $params = array())
 * @method ExportRule[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method ExportRuleDb SetOrderByExp(string $customStr)
 */
abstract class ExportRuleDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  *
		/*fields*/
		FROM export_rules
		WHERE (1=1)
		ORDER BY rule_sort,(1)
	';
}

class ExportRuleModelMap extends ModelMap
{
	public $Name = 'export_rules';
	public $ModelName = 'ExportRule';

	protected $DbFields = array(
		"Id" => array(
			"type"     => VarTypes::INT,
			"name"     => "id",
			"field"    => "export_rules.id",
			"display"  => "Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"ExportId" => array(
			"type"     => VarTypes::INT,
			"name"     => "export_id",
			"field"    => "export_rules.export_id",
			"display"  => "Export Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Name" => array(
			"type"     => VarTypes::STRING,
			"name"     => "name_",
			"field"    => "export_rules.name_",
			"display"  => "Name",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Aktif" => array(
			"type"     => VarTypes::INT,
			"name"     => "aktif",
			"field"    => "export_rules.aktif",
			"display"  => "Aktif",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 1 ),
		"RuleSort" => array(
			"type"     => VarTypes::INT,
			"name"     => "rule_sort",
			"field"    => "export_rules.rule_sort",
			"display"  => "Rule Sort",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Transaction" => array(
			"type"     => VarTypes::INT,
			"name"     => "transaction",
			"field"    => "export_rules.transaction",
			"display"  => "Transaction",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 )
	);

	protected $Relationships = array(
		array(
			"access_field" => "FiltersInfo",
			"view_name"    => "",
			"condition"    => "ExportFilter.ExportRuleId = Id",
			"condition2"   => "",
			"condition3"   => "",
			"type"         => Relation::ONE_TO_MANY,
			"reverse_field"=> "",
			"behaviour"    => RelationBehaviour::CASCADE_DELETE),
		array(
			"access_field" => "TransactionsInfo",
			"view_name"    => "",
			"condition"    => "ExportTransaction.ExportRuleId = Id",
			"condition2"   => "",
			"condition3"   => "",
			"type"         => Relation::ONE_TO_MANY,
			"reverse_field"=> "",
			"behaviour"    => RelationBehaviour::CASCADE_DELETE),
		array(
			"access_field" => "VendorExportInfo",
			"view_name"    => "",
			"condition"    => "VendorExport.Id = ExportId",
			"condition2"   => "",
			"condition3"   => "",
			"type"         => Relation::ONE_TO_ONE,
			"reverse_field"=> "RulesInfo",
			"behaviour"    => RelationBehaviour::DO_NOTHING)
	);
}