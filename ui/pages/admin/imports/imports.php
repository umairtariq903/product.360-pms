<?php
class AdminImportsPage extends AppPageController
{
	/**
	 * Import listesi oluşturulurken her satır için çalışıtırılan kod
	 * @param Import|stdClass $realData
	 * @param Import|stdClass $rowData
	 * @param stdClass $attributes
	 * @param DataTable $dataTable
	 * @group DbList
	 */
	public function DataRenderRow($realData, $rowData, $attributes, $dataTable)
	{
		parent::DataRenderRow($realData, $rowData, $attributes, $dataTable);
	}

	/**
	 * Arama kriterlerine ekstra parametre eklemek için kullanılır
	 * @param Import $params ModelParam türünde
	 * @group DbList
	 */
	public function DataProcessParam($params)
	{

	}

    public static function RunImportFtp($importId)
    {
        return RunImportFtp($importId);
    }
    public static function RunImportUrl($importId)
    {
        return RunImportUrl($importId);
    }
    public static function AddWorkImport($importId)
    {
        return AddWorkImport($importId);
    }
}
