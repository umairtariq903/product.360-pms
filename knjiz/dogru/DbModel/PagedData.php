<?php
class PagedData
{
	const TYPE_TUM = 1;
	const TYPE_KUCUK1 = 2;
	const TYPE_KUCUK2 = 3;
	const PAGE_VAR = 'pg';

	public $RecordCount = 0;
	public $PageCount = 0;
	public $PageSize = 20;
	public $PageNo = 1;
	/**
	 * @var ModelBaseArray|ModelBase[]
	 */
	public $Records;
	public $Summary = array();
	public $PagingType = self::TYPE_KUCUK2;

	public function RecordStart() {
		return ($this->PageNo - 1) * $this->PageSize + 1;
	}

	public function RecordFinish() {
		return ($this->PageNo - 1) * $this->PageSize + count($this->Records);
	}

	public function RecordRange() {
		return $this->RecordStart() . '-' . $this->RecordFinish();
	}

	public function RecordRangeStr(){
		return "Toplam $this->RecordCount kayıt, Gösterilen " . $this->RecordRange() . " arası";
	}

	public function PagingUrlTemplate()
	{
		$url = $this->SayfaUrlVer(array(self::PAGE_VAR));
		if (!strstr($url, "?"))
			$url .= '?';
		else
			$url .= '&';
		$url.= self::PAGE_VAR . '=';
		return $url;
	}

	public function PageUrl($pageNo)
	{
		return $this->PagingUrlTemplate() . $pageNo;
	}

	public function Paging($type = NULL) {
		if($type === NULL)
			$type = $this->PagingType;
		return PagedData::SayfalamaVer('pg', $this->PageCount, $this->PageNo, $type);
	}

	/**
	 * Verilen aralık ve değerler göz önünde bulundurularak sayfalama linkleri
	 * için HTML TABLE kodları üretilir ve geriye döndürülür.
	 */
	public static function SayfalamaVer($pageVar, $pageCount, $currPage, $type = self::TYPE_KUCUK2)
	{
		if ($pageCount > 1)
		{
			$url = self::SayfaUrlVer(array($pageVar, 'pid2'));
			$url2 = $url;

			// URL'ye page parametresini değiştirilebilir
			// şekilde ekle ve URL'yi tamamla
			$url .= "&$pageVar=[pg]";

			$html = "<table class=paging-container border=1><tr>";
			// Her zaman ilk eleman "<<Geri"
			// son eleman ise "İleri>>" olacak.
			if ($currPage > 1)
				$html.= "<td class=paging-prev  style=\"text-align:left;\"><a
					href=\"". str_replace("[pg]", $currPage-1, $url).
					"\">&lt;&lt;&nbsp;Geri</a></td>";
			else if ($type == self::TYPE_KUCUK1)
				$html.= "<td class=paging-prev-passive  style=\"text-align:left;\">&lt;&lt;&nbsp;Geri</td>";

			if ($type == self::TYPE_TUM)
			{
				$spacePrinted = false;

				for($i=1; $i<=$pageCount; $i++)
				{
					// Tüm sayfaları yazdırmıyoruz :
					// Geçerli sayfanın 3 ilerisi, 3 gerisi ile
					// ilk sayfa ve son sayfa yazdırılacak
					if (! ($i == 1 || $i == $currPage || ($i >= $currPage - 3  && $i <= $currPage + 3) || $i >= $pageCount - 3  ) )
					{
						if (! $spacePrinted)
						{
							$html.= "<td class=paging-normal>...</td>";
							$spacePrinted = true;
						}
						continue;
					}

					// Sayfalama linkleri gösterilebilir
					$spacePrinted = false;
					if ($i == $currPage)
					{
						$className = "paging-current";
						$currentUrl= "Javascript:void(0)";
					}
					else
					{
						$className = "paging-normal";
						$currentUrl= str_replace("[pg]", $i, $url);
					}
					$html .= "<td class=\"$className\"><a href=\"$currentUrl\">$i</a></td>";
				}
			}// if $type
			else if ($type == self::TYPE_KUCUK1)
			{
				$html .= "<td class=paging-normal valign=middle align=center style=\"padding:2px;\">$currPage/$pageCount</td>";
			}
			else if ($type == self::TYPE_KUCUK2)
			{
				$html .= "<td class=paging-normal valign=middle align=center style=\"padding:2px;\"><select ";
				$html .= "onchange=\"window.location.href='$url2&$pageVar=' + this.value + '#results'\">";
				for($i=1; $i<=$pageCount; $i++)
				{
					if ($i == $currPage)
						$selected = " selected ";
					else
						$selected = " ";
					$html .= "<option value=\"$i\" $selected>$i</option>";
				}
				$html .= "</select>";
				$html .= "</td>";
			}

			if ($currPage < $pageCount )
				$html.= "<td class=paging-prev ><a
				href=\"".($currPage < $pageCount ? str_replace("[pg]", $currPage+1, $url) : "").
				"\" >İleri&nbsp;&gt;&gt;</a></td>";
			else if ($type == self::TYPE_KUCUK1)
				$html.= "<td class=paging-prev-passive style=\"text-align:right;\">İleri&nbsp;&gt;&gt;</td>";

			$html .= "</tr></table>";
			return $html;
		}
		else
			return "";
	}

	/**
	 * Geçerli sayfanın URL'sini verir. Gözardı edilmesini
	 * istenen parametre de ayrıca belirtilebilir.
	 */
	public static function SayfaUrlVer($ignore = '')
	{
		if (! is_array($ignore))
			$ignore = array($ignore);

		$uri = "$_SERVER[SCRIPT_NAME]?";
		foreach($_GET as $name=>$value)
			if (! in_array($name, $ignore) && is_string($value))
				$uri .= "$name=" . urlencode($value) . "&";
		return substr($uri, 0, -1);
	}

	/**
	 * SELECT sorgusu çalıştırarak sonucu bir dizi olarak verir
	 * @param string $query sonuç istenen SELECT sorgusu
	 * @param string $description sorgu ile ilgili tanım
	 * @param string $user_func eğer verilirse her bir satırın geçirileceği fonksiyon adı
	 * @return array of array
	 */
	public static function Query($query, $user_func = '', $description = '')
	{
		$pageCount = 1;
		$page = intval(@$_GET['pg']);
		$pageSize = isset($_GET['pageSize']) ? intval($_GET['pageSize']) : 20;
		$recordCount = DB::FetchScalar("SELECT COUNT(*) FROM ($query) tbl ");
		if($pageSize > 0)
			$pageCount = ceil($recordCount / floatval($pageSize));
		if ($page <= 0 || $page > $pageCount)
			$page = 1;
		$query = "SELECT * FROM ($query) tbl
			LIMIT " . (($page - 1) * $pageSize) . ", $pageSize;";
		return DB::FetchArray($query, $user_func, $description);
	}
}
?>
