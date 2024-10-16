<?php

class VarKullaniciTurList extends VarListItem
{
    public function LoadListItems()
    {
        $this->BuildFromArray(User::$Turler,true);
    }
}

class VarKullaniciTurKisiyeGoreList extends VarListItem
{
    public function LoadListItems()
    {
        $list = [];
        $kisi = Kisi();
        if ($kisi)
            foreach (User::$Turler as $key => $value)
            {
                if ($kisi->Tur < $key)
                    $list[$key] = $value;
            }

        $this->BuildFromArray($list,true);
    }
}

class VarCompanyList extends VarListItem
{
    public function LoadListItems()
    {
        $this->BuildFromObjList(CompanyDb::Get()->GetList(),"Id","Name",true);
    }
}

class VarProjectList extends VarListItem
{
    public function LoadListItems()
    {
        $this->BuildFromObjList(ProjectDb::Get()->GetList(),"Id","Name",true);
    }
}

class VarImportFromAtList extends VarListItem
{
    public function LoadListItems()
    {
        $this->BuildFromArray(Import::$FromAts,true);
    }
}

class VarVendorList extends VarListItem
{
    public $Multiple=0;
    public function LoadListItems()
    {
        $this->BuildFromObjList(VendorDb::Get()->GetList(),"Id","Name",true);
    }
}

class VarPAttributesExportList extends VarListItem
{
    public $Multiple = 0;
    public function LoadListItems()
    {
        $this->BuildFromObjList(PAttributeDb::Get()->GetList(),"Id","Name",true);
    }
}
