<?php
require_once 'ExportWrapper.php';
class PdfWrap implements ExportWrapper
{
    /**
     * @var TCPDF
     */
    public $Pdf = null;
    public $SelectedFields = array();
    public $ColSizes = array();
    public $Katsayi = 1.50;

    public function __construct($header)
    {
        $this->Pdf = self::GetDefaultPdfObject($header);
    }

    /**
     *
     * @param string $header
     * @param string $fileName
     * @return \TCPDF
     */
    protected static function GetDefaultPdfObject($header)
    {
        // Include the main TCPDF library (search for installation path).
        require_once(KNJIZ_DIR . 'others/tcpdf/tcpdf.php');

        // create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // set document information
        $pdf->SetCreator(Kodlama::UTF8('(DGR Yazılım)'));
        $pdf->SetTitle(Kodlama::UTF8($header));
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        // set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // set margins
        $pdf->SetMargins(5, 5, 5);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(10);

        // set font
        $pdf->SetFont('dejavusans', '', 9);
        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // add a page
        $pdf->AddPage();

        return $pdf;
    }

    /**
     *
     * @param array[][] $records Çift boyutlu dizi olarak veri
     * @param array $selectedFields seçili alanların başlıkları
     * @param type $fileTitle Dosyanın başlığı
     * @param type $fileName Üretilecek dosyanın adı
     * @param type $colSizes Sütun genişlikleri
     */
    public function GeneratePdf($records, $selectedFields, $fileTitle, $fileName, $colSizes = array())
    {
        $this->Pdf = self::GetDefaultPdfObject($fileTitle);
        $this->Prepare($selectedFields, $fileTitle, $colSizes);
        foreach($records as $record)
            $this->WriteRow($record);
        $this->Finish($fileName);
    }

    /**
     *
     * @param array[][] $records Çift boyutlu dizi olarak veri
     * @param array $selectedFields seçili alanların başlıkları
     * @param type $fileTitle Dosyanın başlığı
     * @param type $fileName Üretilecek dosyanın adı
     * @param type $colSizes Sütun genişlikleri
     */
    public function GeneratePdfDgr($records, $selectedFields, $fileTitle, $fileName, $colSizes = array())
    {
        $this->Pdf = self::GetDefaultPdfObject($fileTitle);
        $this->Prepare($selectedFields, $fileTitle, $colSizes);
        foreach($records as $record)
            $this->WriteRowDgr($record);
        $this->Finish($fileName);
    }

    /**
     *
     * @param array[][] $records Çift boyutlu dizi olarak veri
     * @param array $selectedFields seçili alanların başlıkları
     * @param type $fileTitle Dosyanın başlığı
     * @param type $fileName Üretilecek dosyanın adı
     * @param type $colSizes Sütun genişlikleri
     */
    public function GeneratePdfNew($records, $selectedFields, $fileTitle, $fileName, $colSizes = array())
    {
        $this->Pdf = self::GetDefaultPdfObject($fileTitle);
        $this->Prepare($selectedFields, $fileTitle, $colSizes);
        foreach($records as $record)
            $this->WriteRow($record);
    }

    public function Finish($fileName, $columnWidths = array())
    {
        $this->Pdf->Output($fileName. '.pdf', 'D');
        App::End();
    }

    public function Prepare($selectedFields, $fileTitle, $colSizes = array(), $header = array())
    {
        $pdf = $this->Pdf;
        $this->SelectedFields = $selectedFields;
        $this->ColSizes = $colSizes;
        // Colors, line width and bold font
        $pdf->SetFillColor(225, 231, 242); // Mavi-gri
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetLineWidth(0.2);
        $pdf->SetFont('', 'B');
        $this->Katsayi = 1.95;

        // Page Header
        $pdf->Write(15, Kodlama::UTF8($fileTitle), '', 0, '', true );

        // Table Header
        $i = 0;
        foreach($selectedFields as $title)
        {
            $title = Kodlama::UTF8($title);
            $pdf->Cell($colSizes[$i++] * $this->Katsayi, 6, $title, 1, 0, 'C', 1);
        }
        $pdf->Ln();

        // Color and font restoration
        $pdf->SetFillColor(255, 255, 255);
        $pdf->SetTextColor(0);
        $pdf->SetFont('');
    }

    public function WriteRow($obj, $formatla = true)
    {
        $i = 0;
        foreach(array_keys($this->SelectedFields) as $name)
        {
            $val = is_object($obj) ? $obj->{$name} : $obj[$name];
            $val = Kodlama::UTF8($val);
            if (strlen($val) > $this->ColSizes[$i])
                $val = substr($val, 0, $this->ColSizes[$i]);
            $this->Pdf->Cell($this->ColSizes[$i++] * $this->Katsayi, 6, $val, 1, 0, 'L', 1);
        }
        $this->Pdf->Ln();
    }

    public function WriteRowDgr($obj, $formatla = true)
    {
        $i = 0;
        $maxRowHeight = 0;
        foreach(array_keys($this->SelectedFields) as $name)
        {
            $val = is_object($obj) ? $obj->{$name} : $obj[$name];
            $height = $this->Pdf->getStringHeight($this->ColSizes[$i++] * $this->Katsayi, $val);
            if ($height > $maxRowHeight) {
                $maxRowHeight = $height;
            }
        }

        $maxRowHeight +=1;

        $i = 0;
        foreach(array_keys($this->SelectedFields) as $name)
        {
            $val = is_object($obj) ? $obj->{$name} : $obj[$name];
            $this->Pdf->MultiCell($this->ColSizes[$i++] * $this->Katsayi, $maxRowHeight, $val, $border=1, $align='L', $fill=false, $ln=0, $x=null, $y=null, $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign='M', $fitcell=true);
        }
        $this->Pdf->Ln();
    }
}
