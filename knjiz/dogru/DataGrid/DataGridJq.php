<?php
require_once 'DataGrid.php';
class DataGridJq extends DataGrid
{
	public function GetJSON($params)
	{
		// Gönderilenler
		$pageNum = intval(@$params['page']);
		$pSize = intval(@$params['rows']);
		$orderBy  = intval(@$params['q']);
		$orderDir = @$params['sord'];
		$start = ($pageNum - 1) * $pSize;
		$list = $this->DataTable->GetPagedData($params, $orderBy, $orderDir, $start, $pSize);
		$sonuc = new stdClass();
		$sonuc->page = $list->PageNo;
		$sonuc->total= $list->PageCount;
		$sonuc->records = $list->RecordCount;
		for($i=0; $i<count($list->Records); $i++)
		{
			$r = new stdClass();
			$r->id = $i+1;
			$r->cell= $list->Records[$i];
			$list->Records[$i] = $r;
		}
		$sonuc->rows = $list->Records;
		return Kodlama::JSON($sonuc);
	}
	/**
	 * @param PageController $pageController
	 * @param string $tableId verinin aktarılacağı table id
	 */
	public function Render(PageController $pageCtrl, $tableId, $ajaxResource = 0)
	{
		if ($ajaxResource)
			$this->DataTable->Data = null;
		$pageCtrl->AddResource("dogru/js/jqGrid.js");
		$pageCtrl->AddResource(JS_JQGRID);
		$pageCtrl->AddJsOnloadFunc("JqGrid('$tableId', \"".
			addslashes(Kodlama::JSON($this))."\", $ajaxResource);");
	}
}

