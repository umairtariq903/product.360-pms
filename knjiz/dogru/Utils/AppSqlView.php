<?php
class AppSqlViewParam
{
	const T_DATE = 1;
	const T_SUB_QUERY = 2;
	const T_STRING = 3;
	const T_LIST = 4;
	const T_ARRAY = 5;

	public $Kodu = '';
	public $Adi = '';
	public $Tur = 0;
	public $AltSorgu = '';
	public $Value = '';

	public $sqlKod = '';

	public $Items = null;


	/**
	 * @return AppSqlViewParam
	 */
	public static function Get($kod, $ad, $tur = self::T_STRING, $sqlKod = '')
	{
		$p = new AppSqlViewParam();
		$p->Kodu = $kod;
		$p->Adi = $ad;
		$p->Tur = $tur;
		$p->sqlKod = $sqlKod;
		return $p;
	}
}

/**
 * @property AppSqlViewParam[] $Params
 * @property array $Fields
 * @property array $Rows
 * @property array $AltRow
 */
class AppSqlView
{
	public static $SqlDir = '';
	public static $PrimerySqlFile = '';
	public $grup;
	public $kod;
	public $desc;
	public $sumKod;
	public $tur = 0;

	public $Query = '';
	public $SumQuery = '';
	public $Params = array();
	public $RowAttributeFields = array();
	public $RowClickFunction = '';
	public $Sortable = TRUE;

	private $Fields = NULL;
	private $Rows = NULL;
	private $AltRow = NULL;

	public static function GetSQL($file, $kod, $force = true, $checkPrimeryFile = true)
	{
		if(! self::$SqlDir)
			ThrowException('AppSqlView::$SqlDir belirtilmemiş');
		$fileKod = explode('.', $kod);
		if(count($fileKod) == 2)
			list($file, $kod) = $fileKod;
		if($checkPrimeryFile &&  file_exists(self::$PrimerySqlFile))
		{
			$sql = self::GetSQL(self::$PrimerySqlFile, $file.'_'.$kod, false, false);
			if($sql)
				return $sql;
		}
		if($checkPrimeryFile)
			$file = self::$SqlDir . '/' . $file . '.sql';
		if(! file_exists($file))
			ThrowException("$file dosyası bulunamadı.");
		$content = file_get_contents($file);
		$reqs = array();
		if(preg_match_all("#/\*<$kod>\*/(.*)/\*</$kod>\*/#s", $content, $reqs))
			return $reqs[1][0];
		if($force)
			ThrowException("$file dosyasında '$kod' sorgusu bulunamadı. ");
		return '';
	}

	public function Build()
	{
		$this->Query = self::GetSQL($this->grup, $this->kod);
		if($this->sumKod)
			$this->SumQuery = self::GetSQL($this->grup, $this->sumKod, false);
		$this->BuiltRapor();
	}

	/**
	 * Raporlar da gösterilen sorgulamların , veri tabanından alınarak işlendiği fonksiyondur.
	 */
	private function BuiltRaporQuery(&$query, $runQry = TRUE)
	{
		foreach ($this->Params as $param)
			$query = str_replace("@$param->Kodu", $param->Value, $query);
		return $runQry ? DB::Query($query) : $query;
	}

	/**
	 * İlgili sorgulamaların işlenerek sonuçlarının sunulduğu fonksiyondur.
	 */
	public function BuiltRapor()
	{
		if($this->Fields != NULL)
			return;
		$this->BuiltRaporQuery($this->Query, FALSE);
		$qry = trim($this->Query);
		if(preg_match('/;$/', $qry))
			$qry = substr($qry, 0, -1);
		$qry .= ' LIMIT 1';
		$rs = DB::Query($qry);
		$fields = array();

		for($i = 0; $i < DB::RsNumFields($rs); $i++)
		{
			$field = DB::RsFieldMeta($rs, $i);
			$fields[$field->name] = $field;
		}
		$this->Fields = $fields;

		// Tablo atında görünecek olan alt bilgiler
		$alt_rows = array();
		$query = $this->SumQuery;
		if($query != '')
		{
			$rs = $this->BuiltRaporQuery($this->SumQuery);
			$fields = DB::Get()->GetFields($query, TRUE);
			while($alt_row = DB::RsFetchAssoc($rs))
			{
				foreach($fields as $name => $f)
				{
					if ($f->type == 'real')
						$alt_row[$name] = floatval($alt_row[$name]);
					else if ($f->type == 'int')
						$alt_row[$name] = intval($alt_row[$name]);
				}
				$alt_rows[] = $alt_row;
			}
		}
		$this->AltRow = $alt_rows;
		DB::RsFree($rs);
	}

	/**
	 * Rapor sorgulama sonuçlarının excel'e aktarılma işleminin gerçekleştirildiği fonksiyondur.
	 */
	public function __get($name)
	{
		if(property_exists($this, $name))
			return $this->{$name};
	}

	public function RowClick($funcName, $attrFields = '')
	{
		$this->RowClickFunction = $funcName;
		if(! is_array($attrFields))
			$attrFields = explode(',', $attrFields);
		$this->RowAttributeFields = $attrFields;
	}

	public function DelField($name)
	{
		unset($this->Fields[$name]);
	}
}

class AppSqlViewGrup
{
	public $kod;
	public $desc;
	public $List = array();

	public $DefaultRowClickFuncName = '';
	public $DefaultRowAtrFieldNames = array();

	public function RowClick($funcName, $attrFields = '')
	{
		$this->DefaultRowClickFuncName = $funcName;
		$this->DefaultRowAtrFieldNames = explode(',', $attrFields);
	}
}

class AppSqlViewListBase
{
	/**
	 *
	 * @var AppSqlViewGrup[]
	 */
	public $SorguList = array();

	/**
	 * @var AppSqlViewGrup
	 */
	private $CurrentGrup = null;
	/**
	 * @var AppSqlView
	 */
	private $CurrentStats = null;

	public $RowClickFuncList = array();

	/**
	 * @return static
	 */
	public static function Get(PageController $page, $act, $grup = null)
	{
		$as = new static;
		$page->SorguList = $grup ? array($grup => $as->SorguList[$grup]) : $as->SorguList;
		if(@$_GET[$act] == 'rapor_detay')
		{
			$rapor = $as->GetStats($_GET['kat'], $_GET['kod']);
			$page->Title = $rapor->desc;
			$page->StateParams = array('kat', 'kod');
			$dt = new DataTableModelDb($page);
			$dt->DataGrid->ShowSearch = FALSE;
			$dt->DataGrid->Sortable = $rapor->Sortable;
			ModelDb::$SumQuery = $rapor->SumQuery;
			$dt->Build($rapor->Query);
			foreach($as->RowClickFuncList as $pname => $func)
				if(isset($dt->Columns[$pname]))
				{
					unset($dt->Columns[$pname]);
					$dt->RowAttributes['row_id'] = $pname;
					$dt->DataGrid->RowClickFunc = $func;
					break;
				}
			$page->DataTables = array('Rapor' => $dt);
			$page->rapor = $rapor;
			$page->Template = 'AppSqlViewDetay.tpl';
		}
		return $as;
	}

	/**
	 * @return AppSqlView
	 */
	public function GetStats($kat, $kod)
	{
		$grp = @$this->SorguList[$kat];
		if(! $kat)
			ThrowException("$kat grubu bulunamadı.");
		$rpr = @$grp->List[$kod];
		/* @var $rpr AppSqlView */
		if(! $rpr)
			ThrowException("$kod raporu bulunmadı.");
		set_time_limit(60);
		foreach($rpr->Params as $param)
		{
			$param->Value = Kodlama::KodlamaDuzelt(@$_POST[$param->Kodu]);
			if ($param->Tur == AppSqlViewParam::T_SUB_QUERY)
			{
				$param->AltSorgu = AppSqlView::GetSQL($rpr->grup, $param->sqlKod);
				$param->Items = DB::FetchArray($param->AltSorgu);
			}
		}
		$rpr->Build();
		return $rpr;
	}

	public function BeginGrup($kod, $desc)
	{
		$grp = new AppSqlViewGrup();
		$grp->kod = $kod;
		$grp->desc = $desc;
		$this->CurrentGrup = $grp;
		$this->SorguList[$kod] = $grp;
		return $grp;
	}

	public function AddStats($kod, $desc, $sortable = TRUE)
	{
		$sts = new AppSqlView();
		$sts->kod = $kod;
		$sts->desc = $desc;
		$sts->sumKod = $kod . 'Sum';
		$sts->grup = $this->CurrentGrup->kod;
		$sts->RowClick($this->CurrentGrup->DefaultRowClickFuncName, $this->CurrentGrup->DefaultRowAtrFieldNames);
		$sts->Sortable = $sortable;
		$this->CurrentGrup->List[$kod] = $this->CurrentStats = $sts;
		return $sts;
	}

	/**
	 * @return AppSqlViewParam
	 */
	public function AddParam($name, $desc, $type = AppSqlViewParam::T_STRING, $sqlKod = '')
	{
		$prm = AppSqlViewParam::Get($name, $desc, $type);
		$prm->sqlKod =$sqlKod;
		return $this->CurrentStats->Params[$name] = $prm;
	}

	public function AddParamDate($name, $desc)
	{
		return $this->AddParam($name, $desc, AppSqlViewParam::T_DATE);
	}

	public function AddParamQry($name, $desc, $sqlKod)
	{
		return $this->AddParam($name, $desc, AppSqlViewParam::T_SUB_QUERY, $sqlKod);
	}

	public function AddParamList($name, $desc, $list)
	{
		$prm = $this->AddParam($name, $desc, AppSqlViewParam::T_LIST);
		$prm->Items = $list;
		return $prm;
	}

	public function AddParamArray($name, $desc, $list)
	{
		$prm = $this->AddParam($name, $desc, AppSqlViewParam::T_ARRAY);
		$prm->Items = $list;
		return $prm;
	}

	public function LoadUserFavorite()
	{
		$key = "$_SESSION[UserUniqueKey]?favori_query_ids";
		$query = "SELECT deger FROM user_storage WHERE id='$key'";
		$list = DB::FetchScalar($query);
		if ($list)
			$list = explode(';', $list);
		else
			return;
		foreach($this->SorguList as $grp)
			foreach($grp->List as $rpr)
				$rpr->tur = in_array("$rpr->grup|$rpr->kod", $list) ? 1 : 0;
	}

	public static function ChangeUserFavorite($fav, $isAdd)
	{
		$key = "$_SESSION[UserUniqueKey]?favori_query_ids";
		$query = "SELECT deger FROM user_storage WHERE id='$key'";
		$list = DB::FetchScalar($query);
		if ($list)
			$list = explode(';', $list);
		else
			$list = array();
		if ($isAdd == 1)
			$list[] = $fav;
		elseif(($k = array_search($fav, $list)) !== false)
			unset($list[$k]);
		$list = implode(';', $list);
		$query = "REPLACE INTO user_storage SET id='$key', deger='$list'";
		DB::Execute($query);
	}
}