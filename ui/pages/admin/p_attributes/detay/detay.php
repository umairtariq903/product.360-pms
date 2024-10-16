<?php
class AdminPAttributesDetayPage extends AppPageController
{
	/**
	 * Kaydetme işlemi
	 * @param stdClass|PAttribute $obj
	 * @group DbForm
	 */
	public static function DbModelForm_Save($obj)
	{
        $currentData = PageController::$_CurrentInstance->Data;
        if($currentData->IsVendor == 1)
            return "Vendor alanları üzerinde değişiklik yapamazsınız.";
        $kayit = PAttributeDb::Get()->GetFirst(array("Name" => Condition::EQ($obj->Name)));
        if($kayit && $kayit->Id != $currentData->Id)
            return "Bu alan daha önce eklenmiştir.";

		return parent::DbModelForm_Save($obj);
	}
}
