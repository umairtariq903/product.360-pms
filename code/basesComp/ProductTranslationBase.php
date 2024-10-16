<?php
abstract class ProductTranslationBase extends ModelBase
{
	public $Id = 0;
	public $ProductId = 0;
	public $TitleEn = "";
	public $TitleDe = "";
	public $TitleNl = "";
	public $TitleFr = "";
	public $TitleEs = "";
	public $DescriptionEn = "";
	public $DescriptionDe = "";
	public $DescriptionNl = "";
	public $DescriptionFr = "";
	public $DescriptionEs = "";
	public $CategoriesEn = "";
	public $CategoriesDe = "";
	public $CategoriesNl = "";
	public $CategoriesFr = "";
	public $CategoriesEs = "";
	public $GenderEn = "";
	public $GenderDe = "";
	public $GenderNl = "";
	public $GenderFr = "";
	public $GenderEs = "";
	public $Completed = 0;
	public function GetValue($name)
	{
		return @$this->{$name};
	}
	public function SetValue($name, $value)
	{
		$this->{$name} = $value;
	}
}

abstract class ProductTranslationDbBase extends ModelDb
{
	protected $_SelectQuery = 'SELECT /*fields*/   * /*fields*/ FROM product_translations WHERE (1=1) ORDER BY (1)';
}

class ProductTranslationModelMap extends ModelMap
{
	public $Name = 'product_translations';
	public $ModelName = 'ProductTranslation';
	protected $DbFields = array(
		"Id"=>array(1006,"id","product_translations.id","Id","int",0,1,0,0),
		"ProductId"=>array(1006,"product_id","product_translations.product_id","Product Id","int",0,1,0,0),
		"TitleEn"=>array(1002,"title_en","product_translations.title_en","Title En","string",0,1,0,""),
		"TitleDe"=>array(1002,"title_de","product_translations.title_de","Title De","string",0,1,0,""),
		"TitleNl"=>array(1002,"title_nl","product_translations.title_nl","Title Nl","string",0,1,0,""),
		"TitleFr"=>array(1002,"title_fr","product_translations.title_fr","Title Fr","string",0,1,0,""),
		"TitleEs"=>array(1002,"title_es","product_translations.title_es","Title Es","string",0,1,0,""),
		"DescriptionEn"=>array(1002,"description_en","product_translations.description_en","Description En","string",0,1,0,""),
		"DescriptionDe"=>array(1002,"description_de","product_translations.description_de","Description De","string",0,1,0,""),
		"DescriptionNl"=>array(1002,"description_nl","product_translations.description_nl","Description Nl","string",0,1,0,""),
		"DescriptionFr"=>array(1002,"description_fr","product_translations.description_fr","Description Fr","string",0,1,0,""),
		"DescriptionEs"=>array(1002,"description_es","product_translations.description_es","Description Es","string",0,1,0,""),
		"CategoriesEn"=>array(1002,"categories_en","product_translations.categories_en","Categories En","string",0,1,0,""),
		"CategoriesDe"=>array(1002,"categories_de","product_translations.categories_de","Categories De","string",0,1,0,""),
		"CategoriesNl"=>array(1002,"categories_nl","product_translations.categories_nl","Categories Nl","string",0,1,0,""),
		"CategoriesFr"=>array(1002,"categories_fr","product_translations.categories_fr","Categories Fr","string",0,1,0,""),
		"CategoriesEs"=>array(1002,"categories_es","product_translations.categories_es","Categories Es","string",0,1,0,""),
		"GenderEn"=>array(1002,"gender_en","product_translations.gender_en","Gender En","string",0,1,0,""),
		"GenderDe"=>array(1002,"gender_de","product_translations.gender_de","Gender De","string",0,1,0,""),
		"GenderNl"=>array(1002,"gender_nl","product_translations.gender_nl","Gender Nl","string",0,1,0,""),
		"GenderFr"=>array(1002,"gender_fr","product_translations.gender_fr","Gender Fr","string",0,1,0,""),
		"GenderEs"=>array(1002,"gender_es","product_translations.gender_es","Gender Es","string",0,1,0,""),
		"Completed"=>array(1006,"completed","product_translations.completed","Completed","int",0,1,0,0)
	);
	protected $Relationships = array(
		array("access_field"=>"ProductInfo","view_name"=>"","condition"=>"Product.Id = ProductId","condition2"=>"","condition3"=>"","type"=>Relation::ONE_TO_ONE,"reverse_field"=>"ProductTranslationInfo","behaviour"=>RelationBehaviour::DO_NOTHING)
	);
}