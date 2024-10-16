<?php
abstract class AppFileBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var int FieldName = sira                                    */
	public $Sira = 0;

	/** @var string FieldName = ad                                   */
	public $Ad = "";

	/** @var string FieldName = yol                                  */
	public $Yol = "";

	/** @var int FieldName = boyut                                   */
	public $Boyut = 0;

	/** @var datetime FieldName = tarih                              */
	public $Tarih = "0000-00-00 00:00:00";

	/** @var string FieldName = kategori                             */
	public $Kategori = "";

	/** @var string FieldName = aciklama                             */
	public $Aciklama = "";

	/** @var string FieldName = origin                               */
	public $Origin = "";

	/** @var int FieldName = aktif                                   */
	public $Aktif = 1;
}

/**
 * @method AppFile GetById(int $id, bool $AutoCreate = false)
 * @method AppFile GetFirst(array|object $params = array())
 * @method AppFile[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method AppFileDb SetOrderByExp(string $customStr)
 */
abstract class AppFileDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT *
		FROM app_file a
		WHERE (1=1)
		ORDER BY sira, (1)
	';
}

class AppFileModelMap extends ModelMap
{
	public $Name = 'app_file';
	public $ModelName = 'AppFile';

	protected $DbFields = array(
		"Id" => array(
			"type"     => VarTypes::INT,
			"name"     => "id",
			"field"    => "a.id",
			"display"  => "Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Sira" => array(
			"type"     => VarTypes::INT,
			"name"     => "sira",
			"field"    => "a.sira",
			"display"  => "Sira",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Ad" => array(
			"type"     => VarTypes::STRING,
			"name"     => "ad",
			"field"    => "a.ad",
			"display"  => "Ad",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Yol" => array(
			"type"     => VarTypes::STRING,
			"name"     => "yol",
			"field"    => "a.yol",
			"display"  => "Yol",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Boyut" => array(
			"type"     => VarTypes::INT,
			"name"     => "boyut",
			"field"    => "a.boyut",
			"display"  => "Boyut",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Tarih" => array(
			"type"     => VarTypes::DATETIME,
			"name"     => "tarih",
			"field"    => "a.tarih",
			"display"  => "Tarih",
			"model"    => "datetime",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "0000-00-00 00:00:00" ),
		"Kategori" => array(
			"type"     => VarTypes::STRING,
			"name"     => "kategori",
			"field"    => "a.kategori",
			"display"  => "Kategori",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Aciklama" => array(
			"type"     => VarTypes::STRING,
			"name"     => "aciklama",
			"field"    => "a.aciklama",
			"display"  => "Aciklama",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Origin" => array(
			"type"     => VarTypes::STRING,
			"name"     => "origin",
			"field"    => "a.origin",
			"display"  => "Origin",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Aktif" => array(
			"type"     => VarTypes::INT,
			"name"     => "aktif",
			"field"    => "a.aktif",
			"display"  => "Aktif",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 1 )
	);
}