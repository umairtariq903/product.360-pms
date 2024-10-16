<?php
/**
 * @property AppFileList $ImageIds
 * @property ProductAttribute[] $AttributesInfo
 * @property ProductTranslation $ProductTranslationInfo
 */
abstract class ProductBase extends ModelBase
{

	/** @var int FieldName = id                                      */
	public $Id = 0;

	/** @var int FieldName = vendor_id                               */
	public $VendorId = 0;

	/** @var int FieldName = main                                    */
	public $Main = 0;

	/** @var string FieldName = sku                                  */
	public $Sku = "";

	/** @var string FieldName = ean                                  */
	public $Ean = "";

	/** @var string FieldName = images2                              */
	public $Images2 = "";

	/** @var int FieldName = image_processed                         */
	public $ImageProcessed = 0;

	/** @var int FieldName = added_import_id                         */
	public $AddedImportId = 0;

	/** @var int FieldName = processing_stock                        */
	public $ProcessingStock = 0;

	/** @var string FieldName = v_name                               */
	public $VName = "";

	/** @var string FieldName = title                                */
	public $Title = "";

	/** @var string FieldName = description                          */
	public $Description = "";

	/** @var datetime FieldName = create_date                        */
	public $CreateDate = "0000-00-00 00:00:00";

	/** @var string FieldName = title_de                             */
	public $TitleDe = "";

	/** @var string FieldName = title_en                             */
	public $TitleEn = "";

	/** @var string FieldName = title_es                             */
	public $TitleEs = "";

	/** @var string FieldName = title_fr                             */
	public $TitleFr = "";

	/** @var string FieldName = title_nl                             */
	public $TitleNl = "";

	/** @var string FieldName = categories_de                        */
	public $CategoriesDe = "";

	/** @var string FieldName = categories_en                        */
	public $CategoriesEn = "";

	/** @var string FieldName = categories_es                        */
	public $CategoriesEs = "";

	/** @var string FieldName = categories_fr                        */
	public $CategoriesFr = "";

	/** @var string FieldName = categories_nl                        */
	public $CategoriesNl = "";

	/** @var string FieldName = gender_de                            */
	public $GenderDe = "";

	/** @var string FieldName = gender_en                            */
	public $GenderEn = "";

	/** @var string FieldName = gender_es                            */
	public $GenderEs = "";

	/** @var string FieldName = gender_fr                            */
	public $GenderFr = "";

	/** @var string FieldName = gender_nl                            */
	public $GenderNl = "";

	/** @var string FieldName = description_de                       */
	public $DescriptionDe = "";

	/** @var string FieldName = description_en                       */
	public $DescriptionEn = "";

	/** @var string FieldName = description_es                       */
	public $DescriptionEs = "";

	/** @var string FieldName = description_fr                       */
	public $DescriptionFr = "";

	/** @var string FieldName = description_nl                       */
	public $DescriptionNl = "";

	/** @var int FieldName = translate_completed                     */
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

/**
 * @method Product GetById(int $id, bool $AutoCreate = false)
 * @method Product GetFirst(array|object $params = array())
 * @method Product[]|ModelBaseArray GetList(array|object $params = array(), int $recordCount = 1, int $page = 0, int $pageSize = 0)
 * @method ProductDb SetOrderByExp(string $customStr)
 */
abstract class ProductDbBase extends ModelDb
{

	protected $_SelectQuery = '
		SELECT
		/*fields*/
		  T1.*
		/*fields*/
		FROM products T1
		WHERE (1=1)
	';

	/**
	 * @return ProductDb
	 */
	public static function GetWithDetail($params = array())
	{
		return self::GetFromQuery('
			SELECT
			/*fields*/
			  T1.*,
			  T2.title_de,T2.`title_en`,T2.`title_es`,T2.`title_fr`,T2.`title_nl`,
			  T2.categories_de,T2.`categories_en`,T2.`categories_es`,T2.`categories_fr`,T2.`categories_nl`,
			  T2.gender_de,T2.`gender_en`,T2.`gender_es`,T2.`gender_fr`,T2.`gender_nl`,
			  T2.description_de,T2.`description_en`,T2.`description_es`,T2.`description_fr`,T2.`description_nl`,
			  IF(T2.id,T2.completed,0) AS translate_completed
			/*fields*/
			FROM products T1
			LEFT JOIN product_translations T2 ON T1.id=T2.product_id
			WHERE (1=1)
		', $params);
	}
}

class ProductModelMap extends ModelMap
{
	public $Name = 'products';
	public $ModelName = 'Product';

	protected $DbFields = array(
		"Id" => array(
			"type"     => VarTypes::INT,
			"name"     => "id",
			"field"    => "T1.id",
			"display"  => "Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"VendorId" => array(
			"type"     => VarTypes::INT,
			"name"     => "vendor_id",
			"field"    => "T1.vendor_id",
			"display"  => "Vendor Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Main" => array(
			"type"     => VarTypes::INT,
			"name"     => "main",
			"field"    => "T1.main",
			"display"  => "Main",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"Sku" => array(
			"type"     => VarTypes::STRING,
			"name"     => "sku",
			"field"    => "T1.sku",
			"display"  => "Sku",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Ean" => array(
			"type"     => VarTypes::STRING,
			"name"     => "ean",
			"field"    => "T1.ean",
			"display"  => "Ean",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Images2" => array(
			"type"     => VarTypes::STRING,
			"name"     => "images2",
			"field"    => "T1.images2",
			"display"  => "Images2",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"ImageProcessed" => array(
			"type"     => VarTypes::INT,
			"name"     => "image_processed",
			"field"    => "T1.image_processed",
			"display"  => "Image Processed",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"AddedImportId" => array(
			"type"     => VarTypes::INT,
			"name"     => "added_import_id",
			"field"    => "T1.added_import_id",
			"display"  => "Added Import Id",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"ProcessingStock" => array(
			"type"     => VarTypes::INT,
			"name"     => "processing_stock",
			"field"    => "T1.processing_stock",
			"display"  => "Processing Stock",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => 0 ),
		"VName" => array(
			"type"     => VarTypes::STRING,
			"name"     => "v_name",
			"field"    => "T1.v_name",
			"display"  => "V Name",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Title" => array(
			"type"     => VarTypes::STRING,
			"name"     => "title",
			"field"    => "T1.title",
			"display"  => "Title",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"Description" => array(
			"type"     => VarTypes::STRING,
			"name"     => "description",
			"field"    => "T1.description",
			"display"  => "Description",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" ),
		"CreateDate" => array(
			"type"     => VarTypes::DATETIME,
			"name"     => "create_date",
			"field"    => "T1.create_date",
			"display"  => "Create Date",
			"model"    => "datetime",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "0000-00-00 00:00:00" ),
		"TitleDe" => array(
			"type"     => VarTypes::STRING,
			"name"     => "title_de",
			"field"    => "T2.title_de",
			"display"  => "Title De",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"TitleEn" => array(
			"type"     => VarTypes::STRING,
			"name"     => "title_en",
			"field"    => "T2.title_en",
			"display"  => "Title En",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"TitleEs" => array(
			"type"     => VarTypes::STRING,
			"name"     => "title_es",
			"field"    => "T2.title_es",
			"display"  => "Title Es",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"TitleFr" => array(
			"type"     => VarTypes::STRING,
			"name"     => "title_fr",
			"field"    => "T2.title_fr",
			"display"  => "Title Fr",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"TitleNl" => array(
			"type"     => VarTypes::STRING,
			"name"     => "title_nl",
			"field"    => "T2.title_nl",
			"display"  => "Title Nl",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"CategoriesDe" => array(
			"type"     => VarTypes::STRING,
			"name"     => "categories_de",
			"field"    => "T2.categories_de",
			"display"  => "Categories De",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"CategoriesEn" => array(
			"type"     => VarTypes::STRING,
			"name"     => "categories_en",
			"field"    => "T2.categories_en",
			"display"  => "Categories En",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"CategoriesEs" => array(
			"type"     => VarTypes::STRING,
			"name"     => "categories_es",
			"field"    => "T2.categories_es",
			"display"  => "Categories Es",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"CategoriesFr" => array(
			"type"     => VarTypes::STRING,
			"name"     => "categories_fr",
			"field"    => "T2.categories_fr",
			"display"  => "Categories Fr",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"CategoriesNl" => array(
			"type"     => VarTypes::STRING,
			"name"     => "categories_nl",
			"field"    => "T2.categories_nl",
			"display"  => "Categories Nl",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"GenderDe" => array(
			"type"     => VarTypes::STRING,
			"name"     => "gender_de",
			"field"    => "T2.gender_de",
			"display"  => "Gender De",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"GenderEn" => array(
			"type"     => VarTypes::STRING,
			"name"     => "gender_en",
			"field"    => "T2.gender_en",
			"display"  => "Gender En",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"GenderEs" => array(
			"type"     => VarTypes::STRING,
			"name"     => "gender_es",
			"field"    => "T2.gender_es",
			"display"  => "Gender Es",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"GenderFr" => array(
			"type"     => VarTypes::STRING,
			"name"     => "gender_fr",
			"field"    => "T2.gender_fr",
			"display"  => "Gender Fr",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"GenderNl" => array(
			"type"     => VarTypes::STRING,
			"name"     => "gender_nl",
			"field"    => "T2.gender_nl",
			"display"  => "Gender Nl",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"DescriptionDe" => array(
			"type"     => VarTypes::STRING,
			"name"     => "description_de",
			"field"    => "T2.description_de",
			"display"  => "Description De",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"DescriptionEn" => array(
			"type"     => VarTypes::STRING,
			"name"     => "description_en",
			"field"    => "T2.description_en",
			"display"  => "Description En",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"DescriptionEs" => array(
			"type"     => VarTypes::STRING,
			"name"     => "description_es",
			"field"    => "T2.description_es",
			"display"  => "Description Es",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"DescriptionFr" => array(
			"type"     => VarTypes::STRING,
			"name"     => "description_fr",
			"field"    => "T2.description_fr",
			"display"  => "Description Fr",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"DescriptionNl" => array(
			"type"     => VarTypes::STRING,
			"name"     => "description_nl",
			"field"    => "T2.description_nl",
			"display"  => "Description Nl",
			"model"    => "string",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => "" ),
		"TranslateCompleted" => array(
			"type"     => VarTypes::INT,
			"name"     => "translate_completed",
			"field"    => "IF(T2.id,T2.completed,0)",
			"display"  => "Translate Completed",
			"model"    => "int",
			"required" => 0,
			"is_real"  => 0,
			"is_serialized" => 0,
			"default"  => 0 ),
		"ImageIds" => array(
			"type"     => VarTypes::STRING,
			"name"     => "image_ids",
			"field"    => "T1.image_ids",
			"display"  => "Image Ids",
			"model"    => "AppFileList",
			"required" => 0,
			"is_real"  => 1,
			"is_serialized" => 0,
			"default"  => "" )
	);

	protected $Relationships = array(
		array(
			"access_field" => "AttributesInfo",
			"view_name"    => "",
			"condition"    => "ProductAttribute.ProductId = Id",
			"condition2"   => "",
			"condition3"   => "",
			"type"         => Relation::ONE_TO_MANY,
			"reverse_field"=> "",
			"behaviour"    => RelationBehaviour::CASCADE_DELETE),
		array(
			"access_field" => "ProductTranslationInfo",
			"view_name"    => "",
			"condition"    => "ProductTranslation.ProductId = Id",
			"condition2"   => "",
			"condition3"   => "",
			"type"         => Relation::ONE_TO_ONE,
			"reverse_field"=> "ProductInfo",
			"behaviour"    => RelationBehaviour::DO_NOTHING)
	);
}