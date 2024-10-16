<?php
abstract class ParolaYenilemeBase extends ModelBase
{
	public $Id = 0;
	public $Email = "";
	public $Text = "";
	public $EklenmeTarihi = "0000-00-00 00:00:00";
}

abstract class ParolaYenilemeDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   * /*fields*/ FROM parola_yenilemeler WHERE (1=1) ORDER BY (1)';
}

class ParolaYenilemeModelMap extends ModelMap
{
	public $Name = 'parola_yenilemeler';
	public $ModelName = 'ParolaYenileme';
	protected $DbFields = array(
		"Id"=>array(1006,"id","parola_yenilemeler.id","Id","int",0,1,0,0),
		"Email"=>array(1002,"email","parola_yenilemeler.email","Email","string",0,1,0,""),
		"Text"=>array(1002,"text","parola_yenilemeler.text","Text","string",0,1,0,""),
		"EklenmeTarihi"=>array(1003,"eklenme_tarihi","parola_yenilemeler.eklenme_tarihi","Eklenme Tarihi","datetime",0,1,0,"0000-00-00 00:00:00")
	);
}