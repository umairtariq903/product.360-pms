<?php
abstract class AppFileBase extends ModelBase
{
	public $Id = 0;
	public $Sira = 0;
	public $Ad = "";
	public $Yol = "";
	public $Boyut = 0;
	public $Tarih = "0000-00-00 00:00:00";
	public $Kategori = "";
	public $Aciklama = "";
	public $Origin = "";
	public $Aktif = 1;
}

abstract class AppFileDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT * FROM app_file a WHERE (1=1) ORDER BY sira, (1)';
}

class AppFileModelMap extends ModelMap
{
	public $Name = 'app_file';
	public $ModelName = 'AppFile';
	protected $DbFields = array(
		"Id"=>array(1006,"id","a.id","Id","int",0,1,0,0),
		"Sira"=>array(1006,"sira","a.sira","Sira","int",0,1,0,0),
		"Ad"=>array(1002,"ad","a.ad","Ad","string",0,1,0,""),
		"Yol"=>array(1002,"yol","a.yol","Yol","string",0,1,0,""),
		"Boyut"=>array(1006,"boyut","a.boyut","Boyut","int",0,1,0,0),
		"Tarih"=>array(1003,"tarih","a.tarih","Tarih","datetime",0,1,0,"0000-00-00 00:00:00"),
		"Kategori"=>array(1002,"kategori","a.kategori","Kategori","string",0,1,0,""),
		"Aciklama"=>array(1002,"aciklama","a.aciklama","Aciklama","string",0,1,0,""),
		"Origin"=>array(1002,"origin","a.origin","Origin","string",0,1,0,""),
		"Aktif"=>array(1006,"aktif","a.aktif","Aktif","int",0,1,0,1)
	);
}