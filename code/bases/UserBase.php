<?php
abstract class UserBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var string FieldName = email                                */
	public $Email = "";

	/** @var string FieldName = password                             */
	public $Password = "";

	/** @var string FieldName = name                                 */
	public $Name = "";

	/** @var string FieldName = surname                              */
	public $Surname = "";

	/** @var string FieldName = area_code                            */
	public $AreaCode = "";

	/** @var string FieldName = phone                                */
	public $Phone = "";

	/** @var int FieldName = city                                    */
	public $City = 0;

	/** @var datetime FieldName = last_login_date                    */
	public $LastLoginDate = "0000-00-00 00:00:00";

	/** @var datetime FieldName = membership_date                    */
	public $MembershipDate = "0000-00-00 00:00:00";

	/** @var int FieldName = active                                  */
	public $Active = 0;

	/** @var int FieldName = login_count                             */
	public $LoginCount = 0;

	/** @var string FieldName = logo                                 */
	public $Logo = "";

	/** @var int FieldName = user_type                               */
	public $UserType = 0;
}

/**
 * @method User GetById(int $id, bool $AutoCreate = false)
 * @method User GetFirst(array|object $params = array())
 * @method User[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method UserDb SetOrderByExp(string $customStr)
 */
abstract class UserDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  *
		/*fields*/
		FROM users
		WHERE (1=1)
		ORDER BY (1)
	';
}

class UserModelMap extends ModelMap
{
	public $Name = 'users';
	public $ModelName = 'User';

	protected $DbFields = array(
		"Id" => array(
			"type"     => VarTypes::INT,
			"name"     => "id",
			"field"    => "users.id",
			"display"  => "Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Email" => array(
			"type"     => VarTypes::STRING,
			"name"     => "email",
			"field"    => "users.email",
			"display"  => "Email",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Password" => array(
			"type"     => VarTypes::STRING,
			"name"     => "password",
			"field"    => "users.password",
			"display"  => "Password",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Name" => array(
			"type"     => VarTypes::STRING,
			"name"     => "name",
			"field"    => "users.name",
			"display"  => "Name",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Surname" => array(
			"type"     => VarTypes::STRING,
			"name"     => "surname",
			"field"    => "users.surname",
			"display"  => "Surname",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"AreaCode" => array(
			"type"     => VarTypes::STRING,
			"name"     => "area_code",
			"field"    => "users.area_code",
			"display"  => "Area Code",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Phone" => array(
			"type"     => VarTypes::STRING,
			"name"     => "phone",
			"field"    => "users.phone",
			"display"  => "Phone",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"City" => array(
			"type"     => VarTypes::INT,
			"name"     => "city",
			"field"    => "users.city",
			"display"  => "City",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"LastLoginDate" => array(
			"type"     => VarTypes::DATETIME,
			"name"     => "last_login_date",
			"field"    => "users.last_login_date",
			"display"  => "Last Login Date",
			"model"    => "datetime",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "0000-00-00 00:00:00" ),
		"MembershipDate" => array(
			"type"     => VarTypes::DATETIME,
			"name"     => "membership_date",
			"field"    => "users.membership_date",
			"display"  => "Membership Date",
			"model"    => "datetime",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "0000-00-00 00:00:00" ),
		"Active" => array(
			"type"     => VarTypes::INT,
			"name"     => "active",
			"field"    => "users.active",
			"display"  => "Active",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"LoginCount" => array(
			"type"     => VarTypes::INT,
			"name"     => "login_count",
			"field"    => "users.login_count",
			"display"  => "Login Count",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Logo" => array(
			"type"     => VarTypes::STRING,
			"name"     => "logo",
			"field"    => "users.logo",
			"display"  => "Logo",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"UserType" => array(
			"type"     => VarTypes::INT,
			"name"     => "user_type",
			"field"    => "users.user_type",
			"display"  => "User Type",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 )
	);
}