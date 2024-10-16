<?php
require_once 'ExportWrapper.php';
class DocWrap implements ExportWrapper
{
	/**
	 * @var PHPWord
	 */
	public $Doc = null;
	/**
	 * @var PHPWord
	 */
	public $Section = null;
	/**
	 * @var PHPWord_Section
	 */
	public $Table = null;
	public $SelectedFields = array();
	public $ColSizes = array();
	public function __construct()
	{
		require_once KNJIZ_DIR . 'others/PHPWord/PHPWord.php';
		$doc = $this->Doc = new PHPWord();
		$doc->getProperties()
			->setCompany('Dogru')
			->setCreator('Dogru')
			->setTitle('Dogru')
			->setDescription('Dogru');
		$doc->setDefaultFontSize('9pt');
		$doc->setDefaultFontName('Times New Roman');
		$doc->addParagraphStyle('ErsNormal', array('align' => 'left', 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0));
		$margin = 1000;
		$section = $this->Section = $doc->createSection();
		$section->getSettings()
			->setMarginLeft($margin)
			->setMarginRight($margin)
			->setMarginTop($margin)
			->setMarginBottom($margin);
		$headerText = App::$Kod . " Otomasyonu Dokümantasyon Sistemi";
		$fontStyle = array('bold' => true, 'size' => '16pt');
		$section->createHeader()->addText(Kodlama::UTF8($headerText), $fontStyle);
	}

	public static function GenerateDocFile($records, $selectedFields, $fileTitle, $fileName, $colSizes)
	{
		$obj = new DocWrap();
		$obj->Prepare($selectedFields, $fileTitle, $colSizes);
		foreach($records as $record)
			$obj->WriteRow($record);
		$obj->Finish($fileName);
	}

	public function Finish($fileName, $columnWidths = array())
	{
		if (! preg_match("/.docx$/i", $fileName))
			$fileName .= '.docx';
		ob_end_clean();
		header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
		header('Content-Disposition: attachment;filename="'.$fileName.'"');
		header('Cache-Control: max-age=0');
		ob_end_clean();
		PHPWord_IOFactory::createWriter($this->Doc, 'Word2007')->save('php://output');
		App::End();
	}

	public function Prepare($selectedFields, $fileTitle, $colSizes = array(), $header = array())
	{
		$this->ColSizes = $colSizes;
		$this->SelectedFields = $selectedFields;
		$doc = $this->Doc;
		$styleTable = array('borderSize'=>6, 'borderColor'=>'006699', 'cellMargin'=>80);
		$styleFirstRow = array('borderBottomSize'=>18, 'borderBottomColor'=>'0000FF');
		$styleCell = array('valign'=>'center');
		$doc->addTableStyle('listTableStyle', $styleTable, $styleFirstRow);
		$table = $this->Table = $this->Section->addTable('listTableStyle');
		$fontStyle = array('bold'=>true, 'align'=>'center');
		$table->addRow();
		$i = 0;
		foreach($selectedFields as $title)
		{
			$colSize = $colSizes[$i++];
			$table->addCell($colSize * 100, $styleCell)->addText(
				Kodlama::UTF8($title), $fontStyle, 'ErsNormal');
		}
		return $doc;
	}

	public function WriteRow($obj, $formatla = true)
	{
		$this->Table->addRow();
		foreach(array_keys($this->SelectedFields) as $name)
		{
			$val = is_object($obj) ? $obj->{$name} : $obj[$name];
			$val = Kodlama::UTF8($val);
			$this->Table->addCell(0)->addText($val, null, 'ErsNormal');
		}
	}

}
