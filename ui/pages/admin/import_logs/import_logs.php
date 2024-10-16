<?php
class AdminImportLogsPage extends AppPageController
{
	/**
	 * ImportLog listesi oluşturulurken her satır için çalışıtırılan kod
	 * @param ImportLog|stdClass $realData
	 * @param ImportLog|stdClass $rowData
	 * @param stdClass $attributes
	 * @param DataTable $dataTable
	 * @group DbList
	 */
	public function DataRenderRow($realData, $rowData, $attributes, $dataTable)
	{
        if(! $realData->FinishedTime)
            $rowData->FinishedTime = "RUNNING";
		parent::DataRenderRow($realData, $rowData, $attributes, $dataTable);
	}

	/**
	 * Arama kriterlerine ekstra parametre eklemek için kullanılır
	 * @param ImportLog $params ModelParam türünde
	 * @group DbList
	 */
	public function DataProcessParam($params)
	{
	}

    public static function CsvDosyaUrlGetir($id)
    {
        $import = ImportLogDb::Get()->GetById($id);
        return $import->CsvDosya->Yol;
    }
}
