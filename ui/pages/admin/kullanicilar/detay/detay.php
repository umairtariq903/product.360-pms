<?php
class AdminKullanicilarDetayPage extends AppPageController
{
	/**
	 * Alanlara ait özellikleri çalışma zamanında değiştirmek için callback
	 * @param ColumnTemplate $col
	 */
	public static function ColumnPropertyRenderer($col)
	{
        $id = IfNull($_GET,"id",0);
        if ($col->Name == "Password" || $col->Name == "PasswordAgain")
            if ($id > 0)
                $col->Required = 0;
            else
                $col->Required = 1;
	}

	/**
	 * @group DbFormNone
	 */
	public function PasswordAgain_DbFormRender(FormItem $field)
	{
		$obj = $field->Column->DataObj;
		/* @var $obj User*/
		/* @var $field FormInputPass */
	}

	/**
	 * Kaydetme işlemi
	 * @param stdClass|User $obj
	 * @group DbForm
	 */
	public static function DbModelForm_Save($obj)
	{
        if ($obj->Password != "")
        {
            if ($obj->Password != $obj->PasswordAgain)
                return "Parola ve Parola Tekrar alanları birbirine eşit olmalıdır";
            $obj->Password = DgrCode::Encode($obj->Password);
        }
        else
            unset($obj->Password);
		return parent::DbModelForm_Save($obj);
	}

	/**
	 * @group DbFormNone
	 */
	public function Password_DbFormRender(FormItem $field)
	{
		$obj = $field->Column->DataObj;
		/* @var $obj User*/
		/* @var $field FormInputStr */

        $field->val("");
	}
}
