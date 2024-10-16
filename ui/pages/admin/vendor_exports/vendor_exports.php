<?php
class AdminVendorExportsPage extends AppPageController
{
	/**
	 * VendorExport listesi oluşturulurken her satır için çalışıtırılan kod
	 * @param VendorExport|stdClass $realData
	 * @param VendorExport|stdClass $rowData
	 * @param stdClass $attributes
	 * @param DataTable $dataTable
	 * @group DbList
	 */
	public function DataRenderRow($realData, $rowData, $attributes, $dataTable)
	{
        $attributes->Link = $realData->Link;
		parent::DataRenderRow($realData, $rowData, $attributes, $dataTable);
	}

    public static function Guncelle($id)
    {
        $snc = VExportCsv($id);
        return $snc == "Başarılı" ? 1 : $snc;
    }
}
