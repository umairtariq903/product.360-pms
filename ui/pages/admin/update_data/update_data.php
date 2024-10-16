<?php
class AdminUpdateDataPage extends AppPageController
{
    public static function UpdateData($file)
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        ini_set('error_reporting', E_ERROR);

        set_include_path(get_include_path() . PATH_SEPARATOR . KNJIZ_DIR . '/others/PHPExcel');
        require_once KNJIZ_DIR . '/others/PHPExcel/PHPExcel.php';
        require_once KNJIZ_DIR . '/others/PHPExcel/PHPExcel/IOFactory.php';
        $objPHPExcel = PHPExcel_IOFactory::load($file->Yol);

        $sheet = $objPHPExcel->getActiveSheet();


        $pAttrs = PAttributeDb::Get()->GetList();
        foreach ($pAttrs as $attr)
        {
            $attributes[$attr->Name] = $attr;
        }

        $headers = [];
        foreach ($sheet->getRowIterator(1, 1)->current()->getCellIterator() as $cell) {
            if($cell->getValue() == "pms_id" || isset($attributes[$cell->getValue()]))
                $headers[] = $cell->getValue();
        }

        if(count($headers) <= 0)
            return "Headers not found";
        if(! in_array("pms_id",$headers))
            return "pms_id field not found";

        $objects = [];
        $rowIterator = $sheet->getRowIterator(2);
        foreach ($rowIterator as $row)
        {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);
            $data = new stdClass();
            $col = 0;
            foreach ($cellIterator as $cell)
            {
                if(isset($headers[$col]))
                    $data->{$headers[$col]} = $cell->getValue();
                $col++;
            }

            $pFields = [];
            $aFields = [];
            $pmsId = "";
            $attrId = "";
            foreach($data as $key => $value)
            {
                if($key == "pms_id")
                {
                    $pmsId = $value;
                    continue;
                }
                if($attributes[$key]->IsVendor)
                    $pFields[] = "$key='$value'";
                else
                {
                    $aFields[] = "value_='$value'";
                    $attrId = $attributes[$key]->Id;
                }
            }

            if(count($pFields) > 0)
                DB::Update("vendor_products",implode(",",$pFields),"id='$pmsId'");
            if(count($aFields) > 0)
            {
                $productId = DB::FetchScalar("SELECT product_id FROM vendor_products WHERE id='$pmsId'");
                DB::Update("product_attributes",implode(",",$aFields),"product_id='$productId' AND attribute_id='$attrId'");
            }
        }
        return 1;
    }
}
