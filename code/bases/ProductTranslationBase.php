<?php
/**
 * @property Product $ProductInfo
 */
abstract class ProductTranslationBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var int FieldName = product_id                              */
	public $ProductId = 0;

	/** @var string FieldName = title_en                             */
	public $TitleEn = "";

	/** @var string FieldName = title_de                             */
	public $TitleDe = "";

	/** @var string FieldName = title_nl                             */
	public $TitleNl = "";

	/** @var string FieldName = title_fr                             */
	public $TitleFr = "";

	/** @var string FieldName = title_es                             */
	public $TitleEs = "";

	/** @var string FieldName = description_en                       */
	public $DescriptionEn = "";

	/** @var string FieldName = description_de                       */
	public $DescriptionDe = "";

	/** @var string FieldName = description_nl                       */
	public $DescriptionNl = "";

	/** @var string FieldName = description_fr                       */
	public $DescriptionFr = "";

	/** @var string FieldName = description_es                       */
	public $DescriptionEs = "";

	/** @var string FieldName = categories_en                        */
	public $CategoriesEn = "";

	/** @var string FieldName = categories_de                        */
	public $CategoriesDe = "";

	/** @var string FieldName = categories_nl                        */
	public $CategoriesNl = "";

	/** @var string FieldName = categories_fr                        */
	public $CategoriesFr = "";

	/** @var string FieldName = categories_es                        */
	public $CategoriesEs = "";

	/** @var string FieldName = gender_en                            */
	public $GenderEn = "";

	/** @var string FieldName = gender_de                            */
	public $GenderDe = "";

	/** @var string FieldName = gender_nl                            */
	public $GenderNl = "";

	/** @var string FieldName = gender_fr                            */
	public $GenderFr = "";

	/** @var string FieldName = gender_es                            */
	public $GenderEs = "";

	/** @var int FieldName = completed                               */
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

/**
 * @method ProductTranslation GetById(int $id, bool $AutoCreate = false)
 * @method ProductTranslation GetFirst(array|object $params = array())
 * @method ProductTranslation[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method ProductTranslationDb SetOrderByExp(string $customStr)
 */
abstract class ProductTranslationDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  *
		/*fields*/
		FROM product_translations
		WHERE (1=1)
		ORDER BY (1)
	';
}

class ProductTranslationModelMap extends ModelMap
{
	public $Name = 'product_translations';
	public $ModelName = 'ProductTranslation';

	protected $DbFields = array(
		"Id" => array(
			"type"     => VarTypes::INT,
			"name"     => "id",
			"field"    => "product_translations.id",
			"display"  => "Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"ProductId" => array(
			"type"     => VarTypes::INT,
			"name"     => "product_id",
			"field"    => "product_translations.product_id",
			"display"  => "Product Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"TitleEn" => array(
			"type"     => VarTypes::STRING,
			"name"     => "title_en",
			"field"    => "product_translations.title_en",
			"display"  => "Title En",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"TitleDe" => array(
			"type"     => VarTypes::STRING,
			"name"     => "title_de",
			"field"    => "product_translations.title_de",
			"display"  => "Title De",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"TitleNl" => array(
			"type"     => VarTypes::STRING,
			"name"     => "title_nl",
			"field"    => "product_translations.title_nl",
			"display"  => "Title Nl",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"TitleFr" => array(
			"type"     => VarTypes::STRING,
			"name"     => "title_fr",
			"field"    => "product_translations.title_fr",
			"display"  => "Title Fr",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"TitleEs" => array(
			"type"     => VarTypes::STRING,
			"name"     => "title_es",
			"field"    => "product_translations.title_es",
			"display"  => "Title Es",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"DescriptionEn" => array(
			"type"     => VarTypes::STRING,
			"name"     => "description_en",
			"field"    => "product_translations.description_en",
			"display"  => "Description En",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"DescriptionDe" => array(
			"type"     => VarTypes::STRING,
			"name"     => "description_de",
			"field"    => "product_translations.description_de",
			"display"  => "Description De",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"DescriptionNl" => array(
			"type"     => VarTypes::STRING,
			"name"     => "description_nl",
			"field"    => "product_translations.description_nl",
			"display"  => "Description Nl",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"DescriptionFr" => array(
			"type"     => VarTypes::STRING,
			"name"     => "description_fr",
			"field"    => "product_translations.description_fr",
			"display"  => "Description Fr",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"DescriptionEs" => array(
			"type"     => VarTypes::STRING,
			"name"     => "description_es",
			"field"    => "product_translations.description_es",
			"display"  => "Description Es",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"CategoriesEn" => array(
			"type"     => VarTypes::STRING,
			"name"     => "categories_en",
			"field"    => "product_translations.categories_en",
			"display"  => "Categories En",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"CategoriesDe" => array(
			"type"     => VarTypes::STRING,
			"name"     => "categories_de",
			"field"    => "product_translations.categories_de",
			"display"  => "Categories De",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"CategoriesNl" => array(
			"type"     => VarTypes::STRING,
			"name"     => "categories_nl",
			"field"    => "product_translations.categories_nl",
			"display"  => "Categories Nl",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"CategoriesFr" => array(
			"type"     => VarTypes::STRING,
			"name"     => "categories_fr",
			"field"    => "product_translations.categories_fr",
			"display"  => "Categories Fr",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"CategoriesEs" => array(
			"type"     => VarTypes::STRING,
			"name"     => "categories_es",
			"field"    => "product_translations.categories_es",
			"display"  => "Categories Es",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"GenderEn" => array(
			"type"     => VarTypes::STRING,
			"name"     => "gender_en",
			"field"    => "product_translations.gender_en",
			"display"  => "Gender En",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"GenderDe" => array(
			"type"     => VarTypes::STRING,
			"name"     => "gender_de",
			"field"    => "product_translations.gender_de",
			"display"  => "Gender De",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"GenderNl" => array(
			"type"     => VarTypes::STRING,
			"name"     => "gender_nl",
			"field"    => "product_translations.gender_nl",
			"display"  => "Gender Nl",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"GenderFr" => array(
			"type"     => VarTypes::STRING,
			"name"     => "gender_fr",
			"field"    => "product_translations.gender_fr",
			"display"  => "Gender Fr",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"GenderEs" => array(
			"type"     => VarTypes::STRING,
			"name"     => "gender_es",
			"field"    => "product_translations.gender_es",
			"display"  => "Gender Es",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Completed" => array(
			"type"     => VarTypes::INT,
			"name"     => "completed",
			"field"    => "product_translations.completed",
			"display"  => "Completed",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 )
	);

	protected $Relationships = array(
		array(
			"access_field" => "ProductInfo",
			"view_name"    => "",
			"condition"    => "Product.Id = ProductId",
			"condition2"   => "",
			"condition3"   => "",
			"type"         => Relation::ONE_TO_ONE,
			"reverse_field"=> "ProductTranslationInfo",
			"behaviour"    => RelationBehaviour::DO_NOTHING)
	);
}