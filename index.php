<?php
require_once('config.php');
if(! defined('KNJIZ_DIR'))
	define('KNJIZ_DIR', __DIR__ . '/knjiz/');
require_once KNJIZ_DIR . 'LibLoader.php';
// Proje kodunu belirtiniz
App::$Kod = 'dgr_admin_panel';
App::$Encoding = 'UTF-8';
LibLoader::LoadAll();
AutoLoader::AddCodeFolder('code', array('bases', 'objects', 'Utils','logic','data'));
AutoLoader::Register();
require_once 'code/AppPageController.php';
require_once 'code/functions.php';
DgrPageRouter();
UygulamaBaslat();

KullaniciKimlik::BeniHatirlaKontrol();

if(isset($_GET["ilk_import"]))
{
    $sonuc = GetDataFromCsvManuelLink("https://files.channable.com/fLI1przI37g0uU74xNZ-cg==.csv");
    ArrayShortInfo($sonuc);
    die();
}

if(isset($_GET["export_csv"]))
{
    DownloadExportCsv();
    die();
}

if(isset($_GET["create_export_csv"]))
{
    ExportCsv();
    die();
}

if(isset($_GET["create_vexport_csv"]))
{
    $snc = VExportCsv();
    die($snc);
}

if(isset($_GET["vexport"]))
{
    if(DgrCode::Decode($_GET["vexport"]) == $_GET["vexport"])
        die("Hatalı link");
    DownloadVExportCsv();
    die();
}

if(@$_GET["mailtest"])
{
    Mailer::Send("ahmtdgru@gmail.com","tess","asas");
    die();
}

if(isset($_GET["main_data_import"]))
{
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    $list = GetDataFromCsvMainData("https://files.channable.com/qhnQE6kY4LfXeFQ2X9ryhA==.csv");
    ArrayShortInfo($list->Veriler);
    die();
    foreach ($list->Veriler as $obj)
    {
        $product = new Product();
        $product->Ean = $obj["ean"];
        $product->Images2 = $obj["images2"];
        $product->Title = $obj["title"];
        $product->Description = $obj["description"];
        if ($product->Save())
        {
            foreach ($obj["attrs"] as $attrId => $deger)
            {
                if($deger != "")
                {
                    $productAttribute = new ProductAttribute();
                    $productAttribute->ProductId = $product->Id;
                    $productAttribute->AttributeId = $attrId;
                    $productAttribute->Value = $deger;
                    $productAttribute->Save();
                }
            }
        }
    }
    echo "Bitti";
    die();
}

/*$list = GetAllArchiveProducts("Sp2", 1 , 2,5);
ArrayShortInfo($list);
die();*/

/*$pIds = DB::FetchList('SELECT
T2.product_id
FROM p_attributes T1
LEFT JOIN product_attributes T2 ON 1=1
LEFT JOIN vendor_products T3 ON T2.`product_id`=T3.product_id
WHERE T1.id=34 AND T2.`value_`="0,8 gr"
GROUP BY T2.`product_id`');
ArrayShortInfo($pIds,3);
die();*/

if(isset($_GET["hata_kontrol"]))
{

    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    $eanRows = DB::FetchArray('SELECT ean, COUNT(*) AS cnt
        FROM products
        WHERE project_id = 1
        GROUP BY ean 
        HAVING COUNT(*) > 1 LIMIT 10000');
    foreach($eanRows as $row)
    {
        $ean = $row["ean"];
        $products = ProductDb::Get()->SetOrderByExp("T1.id ASC")->GetList(array("ProjectId" => Condition::EQ(1),"Ean" => Condition::EQ($ean)));
        foreach ($products as $key => $product)
        {
            if($product->Main == 0)
            {
                $product->Delete();
                break;
            }
            if((count($products) - 1) == $key)
                $product->Delete();
        }
    }
    die("Bitti -> " . count($eanRows) . " -> " . Tarih::Simdi());
}

if(isset($_GET["import_images"]))
{

    global $SITE_URL;

    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    $params = Product::AsParams();
//    $params->CompanyId = Condition::EQ(1);
//    $params->ProjectId = Condition::EQ(1);
//    $params->Main = 1;
//    $params->Sp1Stock = Condition::GT(0);
    $params->Images2 = Condition::NotEQ("");
//    $params->ImageIds = Condition::IsNull();
    $params->ImageProcessed = Condition::EQ(0);
    $products = ProductDb::Get()->GetList($params,1,1,100);
    $duzenlenenIdler = [];
    foreach ($products as $product)
    {
        $duzenlenenIdler[] = $product->Id;
        $partImages = preg_split('/\|\|?\|?/', $product->Images2);
        foreach ($partImages as $key => $imgLink)
        {
            $parts = explode('.', $imgLink);
            $ext = end($parts);
            $dosya_adi = AppFile::$TEMP_DIR . 'image_'. $key.time().'.'. $ext;

            $dosyaIcerigi = file_get_contents($imgLink);

            if ($dosyaIcerigi !== false)
            {
                $kaydetSonucu = file_put_contents($dosya_adi, $dosyaIcerigi);

                if ($kaydetSonucu !== false)
                {
                    $appFile = new AppFile();
                    $appFile->Yol = $dosya_adi;
                    $appFile->Ad = $product->Ean . "-" . $product->Id . "-" .  $key . "-" . time() . ".".$ext;
                    $product->ImageIds[] = $appFile;
//                echo "Dosya başarıyla indirildi ve lokal olarak kaydedildi.";
                }
                else
                {
                    echo "Dosya lokal olarak kaydedilemedi.";
                }
            }
            else
            {
                echo "Dosya indirilemedi.";
            }
        }

//        $product->Images2 = "";
        $product->ImageProcessed = 1;
        $product->Save();
    }
//    echo implode(",",$duzenlenenIdler);
//    header("Location: ".$SITE_URL."?import_images=1");
//    exit;
    die();
}

if(isset($_GET["import_vendor_images"]))
{

    global $SITE_URL;

    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    $params = VendorProduct::AsParams();
    $params->PhotoUrl = Condition::NotEQ("");
    $params->PhotoProcessed = Condition::EQ(0);
    $vendorProducts = VendorProductDb::Get()->GetList($params,1,1,10);
    $duzenlenenIdler = [];
    foreach ($vendorProducts as $vendorProduct)
    {
        if($vendorProduct->PhotoUrl != $vendorProduct->ProductBilgi->Images2)
        {
            $duzenlenenIdler[] = $vendorProduct->Id;
            $partImages = preg_split('/\|\|?\|?/', $vendorProduct->PhotoUrl);

            foreach ($partImages as $key => $imgLink)
            {
                $parts = explode('.', $imgLink);
                $ext = end($parts);
                $dosya_adi = AppFile::$TEMP_DIR . 'image_'. $key.time().'.'. $ext;

                $dosyaIcerigi = file_get_contents($imgLink);

                if ($dosyaIcerigi !== false)
                {
                    $kaydetSonucu = file_put_contents($dosya_adi, $dosyaIcerigi);

                    if ($kaydetSonucu !== false)
                    {
                        $appFile = new AppFile();
                        $appFile->Yol = $dosya_adi;
                        $appFile->Ad = $vendorProduct->Ean . "-" . $vendorProduct->Id . "-" .  $key . "-" . time() . ".".$ext;
                        $vendorProduct->PhotoIds[] = $appFile;
//                echo "Dosya başarıyla indirildi ve lokal olarak kaydedildi.";
                    }
                    else
                    {
                        echo "Dosya lokal olarak kaydedilemedi.";
                    }
                }
                else
                {
                    echo "Dosya indirilemedi.";
                }
            }
        }

        $vendorProduct->PhotoProcessed = 1;
        $vendorProduct->Save();
    }
    die();
}

if(isset($_GET["run_work_import"]))
{
    print_r(RunWorkImport());
    die();
}
if(isset($_GET["processing_update"]))
{
    $veriSonuc = GetDataFromCsvManuelLink("https://360-wms.com/api/web/v1/processingEanStockFeedCvs/cvs?key=i3DGUwtGnN");

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

    DB::Update("products","processing_stock=0","company_id=1 AND project_id=2");

    foreach ($veriSonuc->Veriler as $veri)
        ProductDb::UpdateProcess($veri);
    ArrayShortInfo($veriSonuc,3);
    die("Bitti");
}
if(isset($_GET["add_work_imports"]))
{
    $param = Import::AsParams();
    $param->AutoRun = Condition::EQ(1);
    $imports = ImportDb::Get()->GetList($param);
    foreach ($imports as $import)
        AddWorkImport($import->Id);
    print_r(RunWorkImport());
    die();
}




/*$xml_data = file_get_contents("https://wholesale.brasty.com/_data/wholesale-feed-czW3E2g72cISgDaqJGx5.xml");

// XML verisini işle
$xml = new SimpleXMLElement($xml_data);

// Ekrana bastır
echo "<pre>";
$items = $xml->items->item;
foreach ($items as $item) {
    echo "Article EAN: " . $item->articleEAN . "<br>";
    echo "Article ID: " . $item->articleId . "<br>";
    echo "Brand: " . $item->brand . "<br>";
    echo "Portfolio: " . $item->portfolio . "<br>";
    echo "Article Name: " . $item->articleName . "<br>";
    echo "Volume: " . $item->volume . "<br>";
    echo "Price Without VAT: " . $item->priceWithoutVat . "<br>";
    echo "Currency: " . $item->currency . "<br>";
    echo "Stock Quantity: " . $item->stockQuantity . "<br>";
    echo "<br>";
}
echo "</pre>";
die();*/




/*$import = ImportDb::Get()->GetById(8);
ArrayShortInfo($import->PFields,3);
$snc = GetDataFromXml($import);
ArrayShortInfo($snc,3);
die();*/

/*$params = Product::AsParams();
$params->Main = 1;
$params->Sp1Stock = Condition::GT(0);
$products = ProductDb::Get()->GetList($params);
foreach ($products as $product)
    echo $product->Images . "<br>";
die();*/


/*$filename = 'https://files.channable.com/U1z_AlKCkwCMJWrFjBGK5g==.csv';
$file = fopen($filename, 'r');

$i = 0;
// CSV dosyasını satır satır okuyun
while (($row = fgetcsv($file)) !== false) {
    // Her satırın bir dizi olduğunu unutmayın
    // Bu dizi üzerinde işlemler yapabilirsiniz
    print_r($row);
    $i++;
    if( $i > 100)
        break;
}

// Dosyayı kapatın
fclose($file);
die();*/

if(@$_GET["translate"] == "cosmetics")
{

    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    $params = Product::AsParams();
    $params->CompanyId = Condition::EQ(1);
    $params->ProjectId = Condition::EQ(2);
    $params->Main = Condition::EQ(1);
    $params->TranslateCompleted = Condition::EQ(0);
    $products = ProductDb::GetWithDetail()->GetList($params,1,1,300);
    $fields = ["Title","Description","Categories","Gender"];
    $langKeys = ["en","nl","de","fr","es"];
    $tr = new \Stichoza\GoogleTranslate\GoogleTranslate();
    foreach($products as $product)
    {
        $ptInfo = $product->ProductTranslationInfo;
        if(! $ptInfo)
            $ptInfo = new ProductTranslation();
        $ptInfo->ProductId = $product->Id;
        $hata = 0;

        foreach ($fields as $fieldName)
        {
            if(isset($product->{$fieldName}) && $product->{$fieldName} != "")
            {
                foreach ($langKeys as $langKey)
                {
                    $tr->setTarget($langKey);
                    if(isset($ptInfo->{$fieldName.StringLib::UcFirst($langKey)}))
                    {
                        $ptInfo->{$fieldName.StringLib::UcFirst($langKey)} = $tr->translate($product->{$fieldName});
                        if($ptInfo->{$fieldName.StringLib::UcFirst($langKey)} == "")
                            $hata = 1;
                    }
                }
            }
        }
        if ($hata == 0)
            $ptInfo->Completed = 1;
        else
            $ptInfo->Completed = 0;
        $ptInfo->Save();
    }


    $params = Product::AsParams();
    $params->CompanyId = Condition::EQ(1);
    $params->ProjectId = Condition::EQ(1);
    $params->Main = Condition::EQ(1);
    $params->TranslateCompleted = Condition::EQ(0);
    $products = ProductDb::GetWithDetail()->GetList($params,1,1,700);
    $fields = ["Categories"];
    $langKeys = ["en","nl","de","fr","es"];
    $tr = new \Stichoza\GoogleTranslate\GoogleTranslate();
    foreach($products as $product)
    {
        $ptInfo = $product->ProductTranslationInfo;
        if(! $ptInfo)
            $ptInfo = new ProductTranslation();
        $ptInfo->ProductId = $product->Id;
        $hata = 0;

        foreach ($fields as $fieldName)
        {
            if(isset($product->{$fieldName}) && $product->{$fieldName} != "")
            {
                foreach ($langKeys as $langKey)
                {
                    $tr->setTarget($langKey);
                    if(isset($ptInfo->{$fieldName.StringLib::UcFirst($langKey)}))
                    {
                        $ptInfo->{$fieldName.StringLib::UcFirst($langKey)} = $tr->translate($product->{$fieldName});
                        if($ptInfo->{$fieldName.StringLib::UcFirst($langKey)} == "")
                            $hata = 1;
                    }
                }
            }
        }
        if ($hata == 0)
            $ptInfo->Completed = 1;
        else
            $ptInfo->Completed = 0;
        $ptInfo->Save();
    }
    ArrayShortInfo(count($products),3);
    die();
}

//Giriş yapan  kişiyi verir
$girisYapan = KullaniciKimlik::GirisYapmisKisiVer();

// Smarty yüklendiğinde çalıştırılacak fonksiyon
SmartyWrap::$OnLoadFunction = 'OnLoadSmarty';
// PageRouter devreye giriyor;
PageRouter::Render2(AppPageController::getErrorPage(),false);
App::End();
