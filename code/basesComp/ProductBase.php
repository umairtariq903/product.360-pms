<?php
abstract class ProductBase extends ModelBase
{
	public $Id = 0;
	public $VendorId = 0;
	public $Main = 0;
	public $Sku = "";
	public $Ean = "";
	public $Images2 = "";
	public $ImageProcessed = 0;
	public $AddedImportId = 0;
	public $ProcessingStock = 0;
	public $VName = "";
	public $Title = "";
	public $Description = "";
	public $CreateDate = "0000-00-00 00:00:00";
	public $TitleDe = "";
	public $TitleEn = "";
	public $TitleEs = "";
	public $TitleFr = "";
	public $TitleNl = "";
	public $CategoriesDe = "";
	public $CategoriesEn = "";
	public $CategoriesEs = "";
	public $CategoriesFr = "";
	public $CategoriesNl = "";
	public $GenderDe = "";
	public $GenderEn = "";
	public $GenderEs = "";
	public $GenderFr = "";
	public $GenderNl = "";
	public $DescriptionDe = "";
	public $DescriptionEn = "";
	public $DescriptionEs = "";
	public $DescriptionFr = "";
	public $DescriptionNl = "";
	public $TranslateCompleted = 0;
	private $ImageIds = "";
	public function GetValue($name)
	{
		return @$this->{$name};
	}
	public function SetValue($name, $value)
	{
		$this->{$name} = $value;
	}
}

abstract class ProductDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   T1.* /*fields*/ FROM products T1 WHERE (1=1)';
	public static function GetWithDetail($params = array())
	{
		return self::GetFromQuery('SELECT /*fields*/   T1.*,   T2.title_de,T2.`title_en`,T2.`title_es`,T2.`title_fr`,T2.`title_nl`,   T2.categories_de,T2.`categories_en`,T2.`categories_es`,T2.`categories_fr`,T2.`categories_nl`,   T2.gender_de,T2.`gender_en`,T2.`gender_es`,T2.`gender_fr`,T2.`gender_nl`,   T2.description_de,T2.`description_en`,T2.`description_es`,T2.`description_fr`,T2.`description_nl`,   IF(T2.id,T2.completed,0) AS translate_completed /*fields*/ FROM products T1 LEFT JOIN product_translations T2 ON T1.id=T2.product_id WHERE (1=1)', $params);
	}
}

class ProductModelMap extends ModelMap
{
	public $Name = 'products';
	public $ModelName = 'Product';
	protected $DbFields = array(
		"Id"=>array(1006,"id","T1.id","Id","int",0,1,0,0),
		"VendorId"=>array(1006,"vendor_id","T1.vendor_id","Vendor Id","int",0,1,0,0),
		"Main"=>array(1006,"main","T1.main","Main","int",0,1,0,0),
		"Sku"=>array(1002,"sku","T1.sku","Sku","string",0,1,0,""),
		"Ean"=>array(1002,"ean","T1.ean","Ean","string",0,1,0,""),
		"Images2"=>array(1002,"images2","T1.images2","Images2","string",0,1,0,""),
		"ImageProcessed"=>array(1006,"image_processed","T1.image_processed","Image Processed","int",0,1,0,0),
		"AddedImportId"=>array(1006,"added_import_id","T1.added_import_id","Added Import Id","int",0,1,0,0),
		"ProcessingStock"=>array(1006,"processing_stock","T1.processing_stock","Processing Stock","int",0,1,0,0),
		"VName"=>array(1002,"v_name","T1.v_name","V Name","string",0,1,0,""),
		"Title"=>array(1002,"title","T1.title","Title","string",0,1,0,""),
		"Description"=>array(1002,"description","T1.description","Description","string",0,1,0,""),
		"CreateDate"=>array(1003,"create_date","T1.create_date","Create Date","datetime",0,1,0,"0000-00-00 00:00:00"),
		"TitleDe"=>array(1002,"title_de","T2.title_de","Title De","string",0,0,0,""),
		"TitleEn"=>array(1002,"title_en","T2.title_en","Title En","string",0,0,0,""),
		"TitleEs"=>array(1002,"title_es","T2.title_es","Title Es","string",0,0,0,""),
		"TitleFr"=>array(1002,"title_fr","T2.title_fr","Title Fr","string",0,0,0,""),
		"TitleNl"=>array(1002,"title_nl","T2.title_nl","Title Nl","string",0,0,0,""),
		"CategoriesDe"=>array(1002,"categories_de","T2.categories_de","Categories De","string",0,0,0,""),
		"CategoriesEn"=>array(1002,"categories_en","T2.categories_en","Categories En","string",0,0,0,""),
		"CategoriesEs"=>array(1002,"categories_es","T2.categories_es","Categories Es","string",0,0,0,""),
		"CategoriesFr"=>array(1002,"categories_fr","T2.categories_fr","Categories Fr","string",0,0,0,""),
		"CategoriesNl"=>array(1002,"categories_nl","T2.categories_nl","Categories Nl","string",0,0,0,""),
		"GenderDe"=>array(1002,"gender_de","T2.gender_de","Gender De","string",0,0,0,""),
		"GenderEn"=>array(1002,"gender_en","T2.gender_en","Gender En","string",0,0,0,""),
		"GenderEs"=>array(1002,"gender_es","T2.gender_es","Gender Es","string",0,0,0,""),
		"GenderFr"=>array(1002,"gender_fr","T2.gender_fr","Gender Fr","string",0,0,0,""),
		"GenderNl"=>array(1002,"gender_nl","T2.gender_nl","Gender Nl","string",0,0,0,""),
		"DescriptionDe"=>array(1002,"description_de","T2.description_de","Description De","string",0,0,0,""),
		"DescriptionEn"=>array(1002,"description_en","T2.description_en","Description En","string",0,0,0,""),
		"DescriptionEs"=>array(1002,"description_es","T2.description_es","Description Es","string",0,0,0,""),
		"DescriptionFr"=>array(1002,"description_fr","T2.description_fr","Description Fr","string",0,0,0,""),
		"DescriptionNl"=>array(1002,"description_nl","T2.description_nl","Description Nl","string",0,0,0,""),
		"TranslateCompleted"=>array(1006,"translate_completed","IF(T2.id,T2.completed,0)","Translate Completed","int",0,0,0,0),
		"ImageIds"=>array(1002,"image_ids","T1.image_ids","Image Ids","AppFileList",0,1,0,"")
	);
	protected $Relationships = array(
		array("access_field"=>"AttributesInfo","view_name"=>"","condition"=>"ProductAttribute.ProductId = Id","condition2"=>"","condition3"=>"","type"=>Relation::ONE_TO_MANY,"reverse_field"=>"","behaviour"=>RelationBehaviour::CASCADE_DELETE),
		array("access_field"=>"ProductTranslationInfo","view_name"=>"","condition"=>"ProductTranslation.ProductId = Id","condition2"=>"","condition3"=>"","type"=>Relation::ONE_TO_ONE,"reverse_field"=>"ProductInfo","behaviour"=>RelationBehaviour::DO_NOTHING)
	);
}