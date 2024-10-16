<?php
require_once 'ExportWrapper.php';
class ExcelWrap implements ExportWrapper
{
	/**
	 *
	 * @var PHPExcel
	 */
	public $Excel = null;
	/**
	 *
	 * @var PHPExcel_Worksheet
	 */
	public $Sheet = null;

	public $SelectedFields = array();
	public $RowIndex = 1;
	public $StartIndex = 1;
	public $KeyToLetter = array();
	public $LastCell = 'A1';
	public $AutoFilter = true;

	public function __construct($FileName = '', $printArea = '')
	{
		set_include_path(get_include_path() . PATH_SEPARATOR . KNJIZ_DIR . '/others/PHPExcel');
		require_once KNJIZ_DIR . '/others/PHPExcel/PHPExcel.php';
		require_once KNJIZ_DIR . '/others/PHPExcel/PHPExcel/IOFactory.php';

		if($FileName != '')
		{
			$inputFileType = PHPExcel_IOFactory::identify($FileName);
			$objReader = PHPExcel_IOFactory::createReader($inputFileType);
			if (!is_file($FileName))
				$FileName = FullPath("ui/templates/$FileName");
			$this->Excel = $objReader->load($FileName);

			// PHPExcel'deki bilinmeyen bir problemden dolayı, orijinal excel dosyasının
			// yazdırma alanı bozuluyor. O yüzden kullanılan XLS dosyasının adına göre
			// manuel olarak yazdırma alanını tekrar elle vermek zorunda kaldık
			//
			// NOT: getPageSetup()->getPrintArea() düzgün bir şey döndürseydi, belki
			//	    orijinal yazdırma alanını alıp, tekrar set ederken bu değeri kullanmayı
			//		deneyebilirdik.
			if ($printArea)
				$this->Excel->getActiveSheet()->getPageSetup()->setPrintArea($printArea);
			else if ($FileName == 'fis_templateV1.xls')
				$this->Excel->getActiveSheet()->getPageSetup()->setPrintArea('A1:J47');
			else
				$this->Excel->getActiveSheet()->getPageSetup()->setPrintArea('A1:W78');
		}
		else
			$this->Excel = new PHPExcel();
		$this->Excel->setActiveSheetIndex(0);
		$this->Sheet = $this->Excel->getActiveSheet();
	}

	function SetStr($cell, $val)
	{
		$this->Sheet->setCellValueExplicit($cell, Kodlama::UTF8($val), PHPExcel_Cell_DataType::TYPE_STRING);
	}

	function SetNum($cell, $val)
	{
		$this->Sheet->setCellValueExplicit($cell, floatval($val), PHPExcel_Cell_DataType::TYPE_NUMERIC);
	}

	function SetFormula($cell, $val)
	{
		$this->Sheet->setCellValue($cell, $val);
	}

	public function SaveToFile($fileName)
	{
		try
		{
			$objWriter = PHPExcel_IOFactory::createWriter($this->Excel, 'Excel2007');
			//$objWriter->setTempDir('ui/templates_c');
			$objWriter->save($fileName);
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
	}

	/*
	 * Tarayıcıya excel dosyasını gönder
	 */
	public function SendToBrowser($fileName)
	{
		@ob_end_clean();
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'. $fileName . '"');
		header('Cache-Control: max-age=0');
		@ob_end_clean();
		try
		{
			$objWriter = PHPExcel_IOFactory::createWriter($this->Excel, 'Excel2007');
			//$objWriter->setTempDir('ui/templates_c');
			$objWriter->save('php://output');
		}
		catch(Exception $e)
		{
			echo $e->getMessage();
		}
		if(Debug::$IsAktif)
			file_put_contents(FullPath('prv/memory.txt'), memory_get_peak_usage() / 1024.0 / 1024.0);
		App::End();
	}

	private function getColumnNext($letter = '', $inc = 1)
	{
		$idx = PHPExcel_Cell::columnIndexFromString($letter);
		return PHPExcel_Cell::stringFromColumnIndex($idx + $inc - 1);
	}

	public function Prepare($selectedFields, $fileTitle, $colSizes = array(), $header = array())
	{
		$sh = $this->Sheet;
		$this->SelectedFields = $selectedFields;
		$sh->setTitle(Kodlama::UTF8($fileTitle));

		$letter = 'A';
		foreach($header as $label => $text)
		{
			$labelCell = $letter . $this->RowIndex;
			$letter2 = $this->getColumnNext($letter);
			$valCell = $letter2.$this->RowIndex;

			if ($label == 'baslik')
			{
				$letter2 = $this->getColumnNext($letter, count($selectedFields) - 1);
				$valCell = $letter2.$this->RowIndex;
				$sh->mergeCells($labelCell . ':' . $valCell);
				$sh->getStyle($labelCell)->getFont()->setBold(true);
				$sh->getStyle($labelCell)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sh->getStyle($labelCell)->getFont()->setSize(16);
				$this->SetStr($labelCell, $text);
			}
			else
			{
				$sh->getStyle($labelCell)->getFont()->setBold(true);
				$this->SetStr($labelCell, $label);
				$this->SetStr($valCell, $text);
			}

			$this->RowIndex++;
		}

		$cell = 'A'. $this->RowIndex;
		$this->StartIndex = $this->RowIndex;
		foreach($selectedFields as $field)
		{
			$cell = $letter . $this->RowIndex;
			$this->SetStr($cell, $field);
			$sh->getStyle($cell)->getFont()->setBold(true);
			$letter = $this->getColumnNext($letter);
		}
		$this->LastCell = $cell;
		$this->RowIndex++;
	}

	public function WriteRow($obj, $formatla = true)
	{
		$letter = 'A';
		$cell = &$this->LastCell;
		foreach(array_keys($this->SelectedFields) as $key)
		{
			$cell = $letter . $this->RowIndex;
			try{
				if (is_object($obj))
				{
					if (preg_match("/\->/", $key))
						eval("\$val = @\$obj->$key;");
					else
						$val = $obj->{$key};
				}
				else
					$val = $obj[$key];

				$var = html_entity_decode($val, ENT_QUOTES, 'UTF-8');
				$val = preg_replace('#<br\s*/?>#i', "\n", $var);
				$unformatted = Number::UndoFormat($val);
//                if ($formatla && (eregi2("(butce|fiyat)", $key) || is_numeric($val) || is_numeric($unformatted)))
                if ($formatla && (is_numeric($val) || is_numeric($unformatted)))
                {
                    if (is_numeric($unformatted))
                        $val = $unformatted;
                    $this->SetNum($cell, $val);
                    if (is_float($val) ||  is_real($val) || is_double($val) )
                        $this->Sheet->getStyle($cell)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                }
                else if ($formatla && Tarih::IsDate($val))
                    $this->SetStr($cell, Tarih::ToNormalDate($val, '-', false, '.'));
                else
                    $this->SetStr($cell, strval($val));
				$this->Sheet->getStyle($cell)->getAlignment()->setWrapText(true);
			}catch(Exception $ex){$ex = '';}

			$this->KeyToLetter[$key] = $letter;

			$letter = $this->getColumnNext($letter);
		}
		$this->RowIndex++;
	}

	public function Finish($fileName, $columnWidths = array(), $sendToBrowser = true)
	{
		$keyToLetter = &$this->KeyToLetter; // Hangi alan, hangi harfe denk geliyor
		$sh = $this->Sheet;
		if (count($columnWidths) > 0)
		{
			$keys = array_keys($columnWidths);
			if (is_numeric($keys[0]))
			{
				$widths = array();
				$keys = array_keys($this->SelectedFields);
				$i = 0;
				foreach($columnWidths as $width)
				{
					if(is_numeric($width) && $width < 15)
						$width = 15;
					$widths[$keys[$i++]] = $width;
				}
				$columnWidths = $widths;
			}
		}

		foreach($columnWidths as $key => $width)
		{
			if ($width == 'default' || ! in_array($key, array_keys($keyToLetter)))
				continue;
			else if ($width == 'auto')
				$sh->getColumnDimension($keyToLetter[$key])->setAutoSize(true);
			else
				$sh->getColumnDimension($keyToLetter[$key])->setWidth(intval($width));
		}

		$region = "A$this->StartIndex:". $this->LastCell;
		$sh->getStyle($region)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		$sh->getStyle($region)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
		if ($this->AutoFilter)
			$sh->setAutoFilter($region);
		if ($sendToBrowser)
			$this->SendToBrowser($fileName.'_' .time().'.xlsx');
		else
			$this->SaveToFile($fileName);
	}

	public static function ExcelOlarakCiktiUret($records, $selectedFields, $fileTitle
			, $fileName, $columnWidths = array(), $header = array(), $AutoFilter = true)
	{
		set_time_limit(0);
		ini_set('memory_limit','1024M');
		$excel = new ExcelWrap();
		$excel->Prepare($selectedFields, $fileTitle, $columnWidths, $header);
		$excel->AutoFilter = $AutoFilter;
		foreach($records as $obj)
			$excel->WriteRow($obj);
		$excel->Finish($fileName, $columnWidths, true);
	}

	public static function ExcelOlarakKaydet($records, $selectedFields, $fileTitle,
			$fileName, $columnWidths = array(), $header = array(), $AutoFilter = true)
	{
		$excel = new ExcelWrap();
		$excel->Prepare($selectedFields, $fileTitle, $columnWidths, $header);
		$excel->AutoFilter = $AutoFilter;
		foreach($records as $obj)
			$excel->WriteRow($obj);
		$excel->Finish($fileName, $columnWidths, false);
	}
}
