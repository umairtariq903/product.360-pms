<?php
class AdminRunImportPage extends AppPageController
{
    public function Index()
    {
        $this->AppFile = VarAppFile::Get();
        $this->AppFile->AllowedExt = "csv";
        $this->AppFile->Accept = ".csv";
    }

    public static function RunImportFile($dosyaYolu)
    {
        $importLog = new ImportLog();
        $importLog->ImportId = $_GET["import_id"];
        $importLog->StartedTime = $importLog->CreatedTime = Tarih::Simdi();
        $importLog->UserId = KisiId();
        $importLog->Save();

        $import = ImportDb::Get()->GetById($_GET["import_id"]);
        if(! $import)
        {
            $importLog->FinishedTime = Tarih::Simdi();
            $importLog->Save();
            return "Hata oluştu";
        }

        $veriSonuc = null;
        if($import->FileType == Import::TxtFile)
            $veriSonuc = GetDataFromTxt($dosyaYolu,$import);
        elseif($import->FileType == Import::CsvFile)
            $veriSonuc = GetDataFromCsv($import, $dosyaYolu, $import->CsvDelimeter == "tab" ? "\t" : $import->CsvDelimeter);
        elseif($import->FileType == Import::XmlFile)
            $veriSonuc = GetDataFromXml($import, $dosyaYolu);
        if(! $veriSonuc)
        {
            $importLog->FinishedTime = Tarih::Simdi();
            $importLog->Save();
            return "Hata oluştu";
        }

        $dInfo = Config('app.DB_INFO_LOCAL');
        if(! isLocalhost())
        {
            global $STATUS;
            if($STATUS == "PRODUCTION")
                $dInfo = Config('app.DB_INFO');
            else
                $dInfo = Config('app.DB_INFO_TEST');
        }

        DbPdo::Set($dInfo['host'], $dInfo['db_name'], $dInfo['username'], $dInfo['password']);

        $tumVeriler = ProductDb::GetAll($import->CompanyId, $import->ProjectId);

        $dbVeriler = [];
        foreach($tumVeriler as $dbVeri)
            $dbVeriler[$dbVeri["ean"]] = $dbVeri;

        $eklenecekVeriler = [];
        $guncellenecekVeriler = [];
        foreach ($veriSonuc->Veriler as $key => $veri)
        {
            $product = null;
            if(isset($dbVeriler[$key]))
                $product = $dbVeriler[$key];

            if(! $product)
            {
                $veri["added_import_id"] = $import->Id;
                $eklenecekVeriler[] = $veri;
            }
            else if($product["main"] == 0)
                $guncellenecekVeriler[] = ["veri" => $veri, "dbVeri" => $product];
            /*else if($product["sp1_stock"] != $veri["sp1_stock"] || $product["sp1_cost"] != $veri["sp1_cost"])
            {
                $guncellenecekVeriler[] = ["veri" => $veri, "dbVeri" => $product];
            }*/
            else
                $importLog->SkipProductCount++;
        }

        if(count($eklenecekVeriler) > 0)
            ProductDB::SaveAll($eklenecekVeriler);
        if(count($guncellenecekVeriler) > 0)
        {
            foreach ($guncellenecekVeriler as $gun)
            {
                ProductDB::Update($gun["dbVeri"]["id"], $gun["veri"]);
            }
        }

        $importLog->AddedProductCount = count($eklenecekVeriler);
        $importLog->UpdatedProductCount = count($guncellenecekVeriler);
        $importLog->EmptyEanCount = $veriSonuc->EmptyEanCount;
        $importLog->IncorrectEanCount = $veriSonuc->IncorrectEanCount;

//        $importLog->CsvDosya = CsvDosyaGetir($import->CompanyId, $import->ProjectId);

        $importLog->FinishedTime = Tarih::Simdi();
        $importLog->Save();

        return 1;
    }
}
