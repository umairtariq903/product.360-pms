<?php
abstract class ParolaYenilemeBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var string FieldName = email                                */
	public $Email = "";

	/** @var string FieldName = text                                 */
	public $Text = "";

	/** @var datetime FieldName = eklenme_tarihi                     */
	public $EklenmeTarihi = "0000-00-00 00:00:00";
}

/**
 * @method ParolaYenileme GetById(int $id, bool $AutoCreate = false)
 * @method ParolaYenileme GetFirst(array|object $params = array())
 * @method ParolaYenileme[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method ParolaYenilemeDb SetOrderByExp(string $customStr)
 */
abstract class ParolaYenilemeDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  *
		/*fields*/
		FROM parola_yenilemeler
		WHERE (1=1)
		ORDER BY (1)
	';
}

class ParolaYenilemeModelMap extends ModelMap
{
	public $Name = 'parola_yenilemeler';
	public $ModelName = 'ParolaYenileme';

	protected $DbFields = array(
		"Id" => array(
			"type"     => VarTypes::INT,
			"name"     => "id",
			"field"    => "parola_yenilemeler.id",
			"display"  => "Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Email" => array(
			"type"     => VarTypes::STRING,
			"name"     => "email",
			"field"    => "parola_yenilemeler.email",
			"display"  => "Email",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Text" => array(
			"type"     => VarTypes::STRING,
			"name"     => "text",
			"field"    => "parola_yenilemeler.text",
			"display"  => "Text",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"EklenmeTarihi" => array(
			"type"     => VarTypes::DATETIME,
			"name"     => "eklenme_tarihi",
			"field"    => "parola_yenilemeler.eklenme_tarihi",
			"display"  => "Eklenme Tarihi",
			"model"    => "datetime",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "0000-00-00 00:00:00" )
	);
}