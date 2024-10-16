<?php
abstract class UserBase extends ModelBase
{
	public $Id = 0;
	public $Email = "";
	public $Password = "";
	public $Name = "";
	public $Surname = "";
	public $AreaCode = "";
	public $Phone = "";
	public $City = 0;
	public $LastLoginDate = "0000-00-00 00:00:00";
	public $MembershipDate = "0000-00-00 00:00:00";
	public $Active = 0;
	public $LoginCount = 0;
	public $Logo = "";
	public $UserType = 0;
}

abstract class UserDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   * /*fields*/ FROM users WHERE (1=1) ORDER BY (1)';
}

class UserModelMap extends ModelMap
{
	public $Name = 'users';
	public $ModelName = 'User';
	protected $DbFields = array(
		"Id"=>array(1006,"id","users.id","Id","int",0,1,0,0),
		"Email"=>array(1002,"email","users.email","Email","string",0,1,0,""),
		"Password"=>array(1002,"password","users.password","Password","string",0,1,0,""),
		"Name"=>array(1002,"name","users.name","Name","string",0,1,0,""),
		"Surname"=>array(1002,"surname","users.surname","Surname","string",0,1,0,""),
		"AreaCode"=>array(1002,"area_code","users.area_code","Area Code","string",0,1,0,""),
		"Phone"=>array(1002,"phone","users.phone","Phone","string",0,1,0,""),
		"City"=>array(1006,"city","users.city","City","int",0,1,0,0),
		"LastLoginDate"=>array(1003,"last_login_date","users.last_login_date","Last Login Date","datetime",0,1,0,"0000-00-00 00:00:00"),
		"MembershipDate"=>array(1003,"membership_date","users.membership_date","Membership Date","datetime",0,1,0,"0000-00-00 00:00:00"),
		"Active"=>array(1006,"active","users.active","Active","int",0,1,0,0),
		"LoginCount"=>array(1006,"login_count","users.login_count","Login Count","int",0,1,0,0),
		"Logo"=>array(1002,"logo","users.logo","Logo","string",0,1,0,""),
		"UserType"=>array(1006,"user_type","users.user_type","User Type","int",0,1,0,0)
	);
}