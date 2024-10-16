<?php
class VendorProduct extends VendorProductBase
{
    /** @var string FieldName = VendorAds                           */
    /** @var string FieldName = PhotoUrlProcessedLink               */
    /** @var Product FieldName = ProductBilgi               */
    public function __get($name)
    {
        global $SITE_URL;
//        if($name == "VendorName")
//            return $this->VendorName = DB::FetchScalar("select name_ from vendors WHERE id='$this->VendorId'");
        if($name == "ProductBilgi")
            return $this->ProductBilgi = ProductDb::Get()->GetById($this->ProductId);
        if($name == "PhotoUrlProcessedLink")
        {
            if($this->PhotoIds && count($this->PhotoIds) > 0)
            {
                $arrs = [];
                foreach ($this->PhotoIds as $iId)
                    $arrs[] = $SITE_URL . $iId->Yol;
                return $this->PhotoUrlProcessedLink = implode("||",$arrs);
            }
            else
            {
                if($this->ProductBilgi && $this->ProductBilgi->ImageIds && count($this->ProductBilgi->ImageIds) > 0)
                {
                    $arrs = [];
                    foreach ($this->ProductBilgi->ImageIds as $iId)
                        $arrs[] = $SITE_URL . $iId->Yol;
                    return $this->PhotoUrlProcessedLink = implode("||",$arrs);
                }
            }
            return $this->PhotoUrlProcessedLink = "";
        }
        return parent::__get($name); // TODO: Change the autogenerated stub
    }
}

class VendorProductDb extends VendorProductDbBase
{
}
