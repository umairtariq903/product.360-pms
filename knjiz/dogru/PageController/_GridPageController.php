<?php
class GridPageController extends PageController
{
	/**
	 *
	 * @var string ModelBase sınıfının adı
	 */
	public $ModelName = '';
	/**
	 *
	 * @var string ModelDb sınıfının adı. Boş bırakılırsa, ModelBase sınıfına
	 *		yazılan nesneye sorularak bulunur
	 */
	public $ModelDbName= '';
	/**
	 *
	 * @var string[] Grid üzerinden görüntülenecek sütunların adları. Boş
	 *		bırakılırsa ModelBase nesnesindeki tüm değerler otomatik getirilir
	 */
	public $VisibleColumns = null;

	/**
	 *
	 * @var DbField[]
	 */
	private $_colsChecked = false;
	/**
	 *
	 * @var ModelDb
	 */
	protected $ModelDb = null;
	/**
	 *
	 * @var ModelMap
	 */
	protected $ModelMap= null;
	public function __construct($tpl, $dir, $acts)
	{
		parent::__construct($tpl, $dir, $acts);

		if ($this->ModelDbName == '')
		{
			$obj = new $this->ModelName;
			$this->ModelDb = $obj->GetDb();
		}
		else if (class_exists($this->ModelDbName))
			$this->ModelDb = call_user_func(array($this->ModelDbName, 'Get'));
		else
			$this->ModelDb = call_user_func($this->ModelDbName);
		$this->ModelMap = $this->ModelDb->GetModelMap();
		$this->SetTemplateUri(KNJIZ_DIR . '/dogru/Templates/bases/grid_form.tpl', false);
	}

	public function FormPosted()
	{
		parent::FormPosted();
		if (@$_GET['grid'] == '1')
			die($this->GetGridData());
	}

	/**
	 * POST/GET ile gönderilen parametrelere uygun
	 * bir ModelBase nesnesi döndürür. Bu nesne
	 * sorgulama amaçlı kullanılır.
	 *
	 * @param type $params
	 * @return ModelBase
	 */
	public function GetParamObj($params)
	{
		$name = $this->ModelDb->GetModelName();
		$ins = new $name(true);
		if (is_array($params))
			$ins->SetFromArray($params);
		$ins->SetFromObj($params);
		return $ins;
	}

	public function GetGridData()
	{
		// Gönderilenler
		$echo = intval(@$_POST['sEcho']);
		$start= intval(@$_POST['iDisplayStart']);
		$pSize= intval(@$_POST['iDisplayLength']);
		$orderBy=intval(@$_POST['iSortCol_0']);
		$orderDir=@$_POST['sSortDir_0'];

		$params = null;
		if (@$_POST['params'] != '')
		{
			$params= json_decode($_POST['params']);
			Kodlama::KarakterKodlamaDuzelt($params);
		}
		if (!$params)
			$params = new stdClass();
		$params = $this->GetParamObj($params);

		$page = $start/$pSize + 1;
		$cols = $this->GetColumns();
		$fieldNames = array_keys($cols);
		$field = $fieldNames[$orderBy];
		$field = $this->ModelMap->DbFields[$field]->FieldName;
		$list = $this->ModelDb->SetOrderByExp("$field $orderDir")
					->GetPage($params, $page, $pSize);

		$rows = array();
		foreach($list->Records as $obj)
		{
			$values = array();
			foreach($cols as $name => $def)
			{
				if (array_key_exists($name, $this->CellRenderers))
					$obj->{$name} = call_user_func (
						array($this, $this->CellRenderers[$name]),
						$obj, $obj->{$name});
				$values[] = addslashes($obj->{$name});
			}
			$rows[] = $values;
		}
		return '{ '.
			'"sEcho": ' . $echo . ',' .
			'"iTotalRecords": '.$list->RecordCount.',' .
			'"iTotalDisplayRecords": '.$list->RecordCount.',' .
			'"aaData" : '. Kodlama::JSON($rows). ' }';
	}

	public function GetColumns()
	{
		if (! $this->_colsChecked)
		{
			$cols = array();
			if ($this->VisibleColumns == null || count($this->VisibleColumns) == 0)
				$this->VisibleColumns = array_keys($this->ModelMap->DbFields);
			foreach($this->VisibleColumns as $name)
			{
				if (array_key_exists($name, $this->ModelMap->DbFields))
				{
					$def = $this->ModelMap->DbFields[$name];

					// Id alanı ve serileştirilmiş alanlar gözardı
					if ($name == 'Id' || $def->IsSerialized || $def->ModelIsArray)
						continue;
				}
				else
				{
					$def = new stdClass();
					$def->Type = VarTypes::STRING;
					$parts = explode(':', $name);
					$title = $name;
					$name = $parts[0];
					if (count($parts) > 1)
						$title = $parts[1];
					$def->DisplayName = $title;
				}
				$this->_colsChecked[$name] = array('Type' => $def->Type, 'Display' => $def->DisplayName);
			}
			$this->VisibleColumns = array_keys($this->_colsChecked);
		}
		return $this->_colsChecked;
	}

	public function Render()
	{
		$smarty = SmartyWrap::Load();
		$smarty->assign('Columns', $this->GetColumns());
		parent::Render();
	}
}
