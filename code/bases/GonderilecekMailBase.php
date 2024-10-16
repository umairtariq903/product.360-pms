<?php
abstract class GonderilecekMailBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var string FieldName = email                                */
	public $Email = "";

	/** @var string FieldName = baslik                               */
	public $Baslik = "";

	/** @var string FieldName = icerik                               */
	public $Icerik = "";

	/** @var datetime FieldName = eklenme_tarihi                     */
	public $EklenmeTarihi = "0000-00-00 00:00:00";
}

/**
 * @method GonderilecekMail GetById(int $id, bool $AutoCreate = false)
 * @method GonderilecekMail GetFirst(array|object $params = array())
 * @method GonderilecekMail[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method GonderilecekMailDb SetOrderByExp(string $customStr)
 */
abstract class GonderilecekMailDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  *
		/*fields*/
		FROM gonderilecek_mailler
		WHERE (1=1)
		ORDER BY (1)
	';
}

class GonderilecekMailModelMap extends ModelMap
{
	public $Name = 'gonderilecek_mailler';
	public $ModelName = 'GonderilecekMail';

	protected $DbFields = array(
		"Id" => array(
			"type"     => VarTypes::INT,
			"name"     => "id",
			"field"    => "gonderilecek_mailler.id",
			"display"  => "Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Email" => array(
			"type"     => VarTypes::STRING,
			"name"     => "email",
			"field"    => "gonderilecek_mailler.email",
			"display"  => "Email",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Baslik" => array(
			"type"     => VarTypes::STRING,
			"name"     => "baslik",
			"field"    => "gonderilecek_mailler.baslik",
			"display"  => "Baslik",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Icerik" => array(
			"type"     => VarTypes::STRING,
			"name"     => "icerik",
			"field"    => "gonderilecek_mailler.icerik",
			"display"  => "Icerik",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"EklenmeTarihi" => array(
			"type"     => VarTypes::DATETIME,
			"name"     => "eklenme_tarihi",
			"field"    => "gonderilecek_mailler.eklenme_tarihi",
			"display"  => "Eklenme Tarihi",
			"model"    => "datetime",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "0000-00-00 00:00:00" )
	);
}