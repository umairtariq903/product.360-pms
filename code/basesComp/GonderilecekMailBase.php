<?php
abstract class GonderilecekMailBase extends ModelBase
{
	public $Id = 0;
	public $Email = "";
	public $Baslik = "";
	public $Icerik = "";
	public $EklenmeTarihi = "0000-00-00 00:00:00";
}

abstract class GonderilecekMailDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   * /*fields*/ FROM gonderilecek_mailler WHERE (1=1) ORDER BY (1)';
}

class GonderilecekMailModelMap extends ModelMap
{
	public $Name = 'gonderilecek_mailler';
	public $ModelName = 'GonderilecekMail';
	protected $DbFields = array(
		"Id"=>array(1006,"id","gonderilecek_mailler.id","Id","int",0,1,0,0),
		"Email"=>array(1002,"email","gonderilecek_mailler.email","Email","string",0,1,0,""),
		"Baslik"=>array(1002,"baslik","gonderilecek_mailler.baslik","Baslik","string",0,1,0,""),
		"Icerik"=>array(1002,"icerik","gonderilecek_mailler.icerik","Icerik","string",0,1,0,""),
		"EklenmeTarihi"=>array(1003,"eklenme_tarihi","gonderilecek_mailler.eklenme_tarihi","Eklenme Tarihi","datetime",0,1,0,"0000-00-00 00:00:00")
	);
}