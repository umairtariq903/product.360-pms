<?php
class AdminImportsDetayPage extends AppPageController
{
    public function Index()
    {
        $liste = [];
        if($this->Data->Id > 0)
            $liste = $this->Data->AttributesInfo;
        $this->AddJsVar("AttributesData", $liste);

        // $pAttributes = PAttributeDb::Get()->SetOrderByExp("id ASC")->GetList(array("IsVendor" => Condition::EQ(1)));
		//Get all fields
		$pAttributes = PAttributeDb::Get()->SetOrderByExp("id ASC")->GetList();

        $attrs = [];
        foreach ($pAttributes as $pAttribute)
            $attrs[$pAttribute->Id] = $pAttribute->Name;
        $this->AddJsVar("attrs",$attrs);
    }

	/**
	 * Kaydetmede sonrası yapılacak işlemler
	 * @param Import $model
	 * @group DbForm
	 */
	protected function DbModelFormAfterSave2($model,$obj)
	{
	}

	/**
	 * Kaydetme işlemi
	 * @param stdClass|Import $obj
	 * @group DbForm
	 */
	public static function DbModelForm_Save($obj)
	{
		return parent::DbModelForm_Save($obj);
	}
	/**
	 * @group DbFormNone
	 */
	public function PFieldWhereKeys_DbFormRender(FormItem $field)
	{
		$obj = $field->Column->DataObj;
		/* @var $obj Import*/
		/* @var $field FormTextArea */

        $field->attr("placeholder", "delivery=1 Day\nGelen alanlarda delivery olmalı ve 1 Day e eşit olmalıdır");
	}

	/**
	 * @group DbFormNone
	 */
	public function PFieldKeys_DbFormRender(FormItem $field)
	{
		$obj = $field->Column->DataObj;
		/* @var $obj Import*/
		/* @var $field FormTextArea */

        $field->attr("placeholder", "ean=1\ntitle=2");
	}

    public static function ResetAllProducts($id)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        ini_set('error_reporting', E_ERROR);

        $import = ImportDb::Get()->GetById($id);
        if(! $import)
            return "Import not found.";
        if($import->ImportType == Import::Main)
            return "You can only reset feeds";
        if($import->SpKey == "")
            return "SpKey value cannot be empty";
        if(! in_array($import->SpKey,Product::$SpKeys))
            return "SpKey value is incorrect";
        $spStock = $import->SpKey . "_stock";
        $spCost  = $import->SpKey . "_cost";

        return DB::Update("products","$spStock=0,$spCost='0.00'","company_id=$import->CompanyId AND project_id=$import->ProjectId");
    }
}
