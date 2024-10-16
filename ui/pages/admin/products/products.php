<?php
class AdminProductsPage extends AppPageController
{
	/**
	 * Product listesi oluşturulurken her satır için çalışıtırılan kod
	 * @param Product|stdClass $realData
	 * @param Product|stdClass $rowData
	 * @param stdClass $attributes
	 * @param DataTable $dataTable
	 * @group DbList
	 */
	public function DataRenderRow($realData, $rowData, $attributes, $dataTable)
	{
        $attributes->ExistLastSupplier = $realData->LastSupplier != "" ? 1 : 0;
		parent::DataRenderRow($realData, $rowData, $attributes, $dataTable);
	}

	/**
	 * Arama kriterlerine ekstra parametre eklemek için kullanılır
	 * @param Product $params ModelParam türünde
	 * @group DbList
	 */
	public function DataProcessParam($params)
	{
	}
}
