<?php
function AbsolutePath($fileName)
{
	$path = realpath($fileName);
	if (preg_match("#^([A-Z]{1}):#", $path))
		$path = strtolower($path[0]) . substr($path, 1);
	return $path;
}

function FullPath($fileName, $base = '')
{
	if(! $base)
		$base = App::$Klasor;
	$full = str_replace('\\', '/', $fileName);
	$path = str_replace('\\', '/', AbsolutePath($base));
	if(! preg_match('#^([A-Z]{1}:|/)#i', $full))
		return "$path/$fileName";
	else
		return $full;
}

function RelativePath($fileName, $base = '')
{
	if(! $base)
		$base = App::$Klasor;
	$full = str_replace('\\', '/', $fileName);
	$path = str_replace('\\', '/', AbsolutePath($base));
	return preg_replace("#$path/#i", '', $full);
}

/**
* PHP versiyon 5.2'den 5.3+ e geçerken gerekli bir
* düzenleme yeni yerlerde kullanılmaması gerekiyor
*/
function eregi2($pattern, $string, array &$regs = null)
{
	return preg_match("/$pattern/i", $string, $regs);
}

/**
 * $value değeri setlenmişse $var değişkenine atar
 * $func parametresi verilmişse bu fonksiyondan geçirir
 */
function AssignIfSet(&$var, $row, $index, $func = null)
{
	if(!is_array($row) || !$row)
		return FALSE;
	$sonuc = array_key_exists($index, $row);
	if($sonuc)
		if($func)
			$var = call_user_func($func, $row[$index]);
		else
			$var = $row[$index];
	return $sonuc;
}

function GetResourceUrl($file, $type = 'js', $dir = '', $addAge = true)
{
	$age = 0;
	if (LibLoader::IsLoaded(LIB_AUTOLOADER))
		$age = AutoLoader::$classesFileAge;
	if(! preg_match('/\.(js|css|png|gif|jpg)$/i', $file))
		$file .= ".$type";
	$url = "pravi/";
	if ($dir && isFile("$dir/$file"))
		$url = "$dir/";
	else if (isFile("js/$file"))
		$url = "js/";
	else if (isFile($file))
		$url = "";
	else if (stristr(KNJIZ_DIR, App::$Klasor))
		$url = "knjiz/";
	$url .= $file;
	if ($addAge)
		$url .= "?v=$age";
	return $url;
}

/**
 * $dir verilmişse öncelik olarak bu klasörder arar
 * verilen java script dosyasını öncelikli olarak /js klasöründe arar.
 * bulmazsa LIB_DIRS altındaki js dosylarına yönlendirir.
 */
function AddJS($jsName, $type = 'js', $dir = '')
{
	$url = GetResourceUrl($jsName, $type, $dir);
	if ($type == 'js')
		return "<script type=\"text/javascript\" src=\"$url\"></script>";
	else if($type == 'css')
		return "<link href=\"$url\" rel=\"stylesheet\" type=\"text/css\" />";
	else
		return $url;
}

function AddCSS($cssName, $dir ='')
{
	if (! preg_match('/\.css$/i', $cssName))
		$cssName .= '.css';
	return AddJS($cssName, 'css', $dir);
}

function GetImgUrl($file)
{
	return AddJS($file, 'img');
}

/**
 * verilen dizi veya nesnede $key yoksa default değeri dönderir
 * @param array|Object $var
 * @param mixed $key
 * @param mixed $default
 * @return mixed
 */
function IfNull(&$var, $key, $default = '')
{
	if(is_array($var) && key_exists($key, $var))
		return $var[$key];
	elseif(is_object($var) && property_exists($var, $key))
		return $var->{$key};
	else
		return $default;
}

/**
 * Verilen değer boş olması durumunda default değeri döndürür
 * @param AnyType $value kontrol edilecek değer
 * @param AnyType $default value == '' olması durumunda atanacak değer
 * @return AnyType value != '' olması durumunda value aksi halde default değer döndürür
 */
function IfEmpty($value, $default = '')
{
	if($value != '')
		return $value;
	else
		return $default;
}

/**
 * transaction durumunda ise exception oluşturur, yoksa die çalıştırır.
 * @param string $hata
 * @throws Exception
 */
function ThrowException($hata)
{
	if(LibLoader::IsLoaded(LIB_DEBUG) && Debug::$IsAktif)
	{
		Debug::AddLog('Bitiş');
		Debug::End();
	}
	if(Transaction::IsActive() && $hata)
		throw new Exception($hata);
	else
		die($hata);
	return $hata;
}

/**
 * Verilen nesnenin public değişkenlerini diziye çevirir.
 * örn: $array[$key] = $obj->{$key}
 * @param Object $obj
 * @param bool $assoc key ile birlikte aktar
 * @return array
 */
function ObjToArray($obj, $assoc = false)
{
	$a = array();
	if($obj)
		foreach ($obj as $name => $value)
		{
			if ($assoc)
				$a[$name] = $value;
			else
				$a[] = $value;
		}
	return $a;
}

/**
 * verilen dizi de key ile object tedki eşlen property lere atar.
 * örn: $obj->{$key} = $array[$key]
 * @param array $array
 * @param Object $obj
 */
function ArrayToObj($array, $obj)
{
	foreach($array as $key => $value)
		if(key_exists($key, get_object_vars($obj)))
			$obj->{$key} = $value;
}

function OS_IsWindows()
{
	static $iswin = null;
	if ($iswin === null)
		$iswin = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN');
	return $iswin;
}

function filesize64($file)
{
	static $exec_works = null;

	$iswin = OS_IsWindows();

	if ($exec_works === null)
		$exec_works = (function_exists('exec') && !ini_get('safe_mode') && @exec('echo EXEC') == 'EXEC');

	// try a shell command
	if ($exec_works)
	{
		$cmd = ($iswin) ? "for %F in (\"$file\") do @echo %~zF" : "stat -c%s \"$file\"";
		$output = array();
		@exec($cmd, $output);
		if (is_array($output) && ctype_digit($size = trim(implode("\n", $output))))
			return $size;
	}

	// try the Windows COM interface
	if ($iswin && class_exists("COM"))
	{
		try
		{
			$fsobj = new COM('Scripting.FileSystemObject');
			$f = $fsobj->GetFile(realpath($file));
			$size = $f->Size;
		}
		catch(Exception $e)
		{
			$size = null;
		}
		if (ctype_digit($size))
			return $size;
	}

	// if all else fails
	return filesize($file);
}

/**
 * Verilen dosyanın boyutunu kısa ve formatlı bir şekilde gösterir.
 * Örn: 2.34 KB gibi
 * @param string $file dosyanın tam yolu ve adı
 * @param type $setup gösterilme derecesi(0=>B, 1=>KB, ...). Verilmezde uygun olan
 * otomatik seçilir.
 * @return string
 */
function GetFileSize($file, $setup = null)
{
    $FZ = ($file && @isFile($file)) ? filesize($file) : NULL;
	return GetShortSize($FZ, $setup);
}


/**
 * Verilen boyutu kısa ve formatlı bir şekilde gösterir.
 * Örn: 2.34 KB gibi
 * @param string $FZ çevrilecek olan büyüklük
 * @param type $setup gösterilme derecesi(0=>B, 1=>KB, ...). Verilmezde uygun olan
 * otomatik seçilir.
 * @return string
 */
function GetShortSize($FZ, $setup = null, $decimal = 2)
{
	$FS = array("B", "KB", "MB", "GB", "TB", "PB", "EB", "ZB", "YB");
	if ($setup && is_string($setup))
		$setup = array_search($setup, $FS);

	$sign = $FZ < 0 ? '-' : '';
	$FZ = abs($FZ);
	if (!$setup && $setup !== 0)
		$setup = floor(log($FZ, 1024));

	if ($setup == 'INT')
		return $sign . @Number::Format($FZ);

	$decimal = ($setup >= 1) ? $decimal : 0;
	$size = $FZ / pow(1024, $setup);
	return $sign . @Number::Format($size, $decimal) . ' ' . $FS[$setup];
}

/**
 *
 * @param array|object $array ekrana yazdırılacak olan değişken
 * @param int $level kaçıncı seviyeye kadar gösterilecek
 * @param type $html ekrana html formatta mı üretilsin?
 * @param type $first bu parametre iç kullanım içindir
 * @return string
 */
function ArrayShortInfo($array, $level = 1, $html = true, $first = true, $print = true)
{
	if(is_array($array))
		$sonuc = 'Array(' . count($array) . ')';
	elseif(is_object($array))
		$sonuc = 'Object(' . get_class($array) . ')';
	else
		$sonuc = str_replace("\n", "\n\t", $array);
	if($level > 0 && (is_array($array) || is_object($array)))
	{
		$sonuc .= "{";
		foreach($array as $key => $value)
		{
			$val = ArrayShortInfo($value, $level - 1, $html, FALSE, FALSE);
			$val = str_replace("\n", "\n\t", $val);
			$sonuc .= "\n\t$key => $val";
		}
		$sonuc .= "\n}";
	}
	if($first && $html)
		$sonuc = "<pre>$sonuc</pre>";
	if($first && $print)
		echo $sonuc;
	return $sonuc;
}

/**
 * Verilen ifadenin serilize edilmiş bir string olup olmadığına bakar
 * @param string $s
 * @return bool
 */
function IsSerialized($s)
{
	return is_string($s) && preg_match('/^[a-z]:/i', $s);
}

/**
 * Verilen ifadenin JSON string'i olup olmadığını test eder.
 * Geriye false veya object'in kendisini döndürür.
 * @param string $s
 * @return boolean
 */
function IsJsonString($s)
{
	if (!is_string($s) || !preg_match('/^[\[\{]/', $s))
		return false;
	$object = @json_decode($s);
	if (json_last_error() != JSON_ERROR_NONE)
		return false;
	return $object;
}

function mb_unserialize($string)
{
    $string = preg_replace_callback('!s:(\d+):"(.*?)";!s', function($dizi){
		return 's:'. strlen($dizi[2]) . ':"' . $dizi[2] . '";';
	}, $string);
    return unserialize($string);
}

/**
 * string olarak verilen funksiyon ismini çalıştırır. Php 5.2 de desteklenmeyen
 * Sınıf statik fonksiyonlarını da destekler. Fonksiyon parametreleri ek parametre
 * olarak eklenir.
 * @param string $funcName
 * @return mixed
 */
function CallUserFunc($funcName)
{
	$params = func_get_args();
	array_shift($params);
	if(is_string($funcName) && preg_match('/::/', $funcName))
		$funcName = explode ('::', $funcName);
	return call_user_func_array($funcName, $params);
}


function StrToFloat($value)
{
	$value = str_replace(Number::$THOUSAND_SEPARATOR, '', $value);
	return str_replace(Number::$DECIMAL_SEPARATOR, '.', $value);
}

function CheckDgrPass($user, $pass)
{
	$match = array();
	if(! preg_match('/^#(.*)/', $user, $match))
		return false;
	if(isLocalhost())
		return true;
	$user = $match[1];
	$u = DgrCode::Encode($user);
	$p = DgrCode::Encode($pass);
	$resp = GetDgrWebServis("dgr_login", "u=$u&p=$p");
	if($resp != DgrCode::Encode($user . $pass))
		return false;
	return true;
}

function isLocalhost()
{
	// Sunucu sadece yerelde çalışmak için ayarlanmışsa
	//
	// NOT: server adresi (ServerName direktifi)
	// httpd.conf'da düzgün ayarlanmışsa
	// o zaman sadece kullanıcının eriştiği IP değeri geliyor
	// bu da aslında tam istenen şey değil
	//
	// NOT 2: httpd.conf "Listen" direktifi istek alınacak
	//	server ip'sini kısıtlayabiliyor
	$addr = @$_SERVER['SERVER_ADDR'] ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
	return $addr === '127.0.0.1' || substr($addr, 0, 10) === '192.168.1.' || substr($addr, 0, 4) === 'dev.' ||
		$addr === '::1' || $_SERVER['SERVER_NAME']	=== 'localhost' || $_SERVER['SERVER_NAME']	=== 'dev.kasatakip.com' || $_SERVER['SERVER_NAME']	=== 'dev.dgryazilim.com';
}

function GetDgrWebServis($act2, $ext = '', $targetServer = DgrPack::TS_YONETIM)
{
	$url = 'http://' . DgrPack::$ServerUrl[$targetServer]
		. "/servisler/$act2" . ($ext ? "/$ext" : '');
	session_write_close();
	$sonuc = @file_get_contents($url);
	session_start();
	return $sonuc;
}

function IfGlobalUser($user)
{
	return preg_match('/#(.*)/', $user);
}

function GetWebAddr($addr, $withHttp = FALSE)
{
	$matches = array();
	if(preg_match('#https?://(.*)#', $addr, $matches))
		$addr = $matches[1];
	$http = "http://";
	if(isset($matches[0]) && preg_match('#https://(.*)#', $matches[0]))
		$http = "https://";
	return $withHttp && $addr ? "$http$addr" : $addr;
}

function UpperCase($str)
{
	$str = str_replace(array('I', 'i'),array('ı','İ'), $str);
	return mb_convert_case($str, MB_CASE_UPPER,'UTF-8');
}

function LowerCase($str)
{
	$str = str_replace(array('I', 'i'),array('ı','İ'), $str);
	return mb_convert_case($str, MB_CASE_LOWER,'UTF-8');
}

function TitleCase($str)
{
	$str = str_replace(array('I', 'i'),array('ı','İ'), $str);
	return mb_convert_case($str, MB_CASE_TITLE,'UTF-8');
}

function Bool($if, $true = 1, $false = 0)
{
	return $if ? $true : $false;
}

function IfTrue($if, $true, $false = '')
{
	return $if ? $true : $false;
}

function InCarray($id, $carray)
{
	$carray = explode(',', $carray);
	return in_array($id, $carray);
}

function SetVar(&$var, $val)
{
	$var = $val;
}

function SessionStart()
{
	if(@$GLOBALS['useMemSession'])
			MemSession::Register();
	session_start();
}

require_once KNJIZ_DIR . 'dogru/Utils/FileCache.php';

function isFile($filename)
{
	return is_file($filename);
	return FileCache::isFile($filename);
}

function isDir($dir)
{
	return FileCache::isDir($dir);
}

function UrlExists($url)
{
	if (extension_loaded('curl'))
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,            $url);
		curl_setopt($ch, CURLOPT_HEADER,         true);
		curl_setopt($ch, CURLOPT_NOBODY,         true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT,        15);
		$r = curl_exec($ch);
		$file_headers = explode("\n", $r);
	}
	else
		$file_headers = @get_headers ($url);
	if(preg_match("#HTTP/1.0.*404#i", $file_headers[0]))
		return false;
	return true;
}

/**
 * Verilen object dizisini, ikinci parametresi ile verilen
 * property değerine göre sıralar. <br>
 * Ör: $list = ObjSort($list, 'Ad');
 * @param type $array
 * @param type $property
 * @return type
 */
function ObjSort(&$array, $property)
{
	return ArrayLib::CustomSort($array, $property);
}

function CustId($encoded = false)
{
	$cid = @$GLOBALS['CUSTOMER_ID'];
	if (!$cid)
		$cid = Config('app.CUSTOMER_ID');
	return $encoded ? DgrCode::Encode($cid) : $cid;
}

function GetCustomerParam($name = '', $pid = '', $cid = '')
{
	if (! $pid)
		$pid = $GLOBALS['PRODUCT_ID'];
	if (! $cid)
		$cid = CustId();
	$url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$params = array(
		'name' => $name,
		'pid'  => $pid,
		'cid'  => $cid,
		'url'  => $url
	);
	return DgrCode::Encode(Kodlama::JSON($params));
}

function numcmp($a, $b)
{
    if(abs($a - $b) < FLOAT_ERR) return 0;
    if((float)$a    > (float)$b) return 1;
    if((float)$a    < (float)$b) return -1;
}

function GetPropVal($obj, $propName)
{
	return $obj->{$propName};
}

function ModulIsAktif($name)
{
	return \DogRu\AppModules::IsAktif($name);
}

function Trans($key, $value = null, $code = '')
{
	$trans = \DogRu\AppTranslate::get();
	if ($value === NULL)
		return $trans->getValue($key);
	return $trans->setValue($key, $value, $code);
}

function TransValues($code, $values)
{
	$trans = \DogRu\AppTranslate::get();
	$trans->setValue('', $values, $code);
}

function TransCheck($key, $default)
{
	$val = Trans($key);
	return $val ? $val : $default;
}

function Config($key, $value = null)
{
	$trans = \DogRu\AppConfig::get();
	if ($value === NULL)
	{
		$val = DgrCode::Decode($trans->getValue($key));
		$v = Kodlama::JSONTryParse($val);
		if ($v === null)
			$v = $val;
		if (is_object($v))
			$v = (array)$v;
		return $v;
	}
	return $trans->setValue($key, $value);
}

function ConfigValues($values)
{
	$trans = \DogRu\AppConfig::get();
	return $trans->setValue('', $values);
}

function ConfigCheck($key, $default)
{
	$val = Config($key);
	return $val ? $val : $default;
}

function Swap(&$a, &$b)
{
	$t = $a;
	$a = $b;
	$b = $t;
}

function CheckExternalProgramExists($program)
{
	// Windows
	if (stristr(PHP_OS, "WIN"))
		return file_exists(str_replace ("/", "\\", $program));

	// Generic Linux (./program formatında çalıştırılanlar)
	if (stristr($program, '/'))
		return file_exists(str_replace("./", "", $program));

	// Linux (Program doğrudan komut olarak çalıştırılıyor)
	$output = array();
	exec("command -v $program || { echo \"not installed\" >&2; }", $output);
	$output = implode('', $output);

	return $output != '' && ! preg_match("/not installed/", $output);
}

/**
 * PDF birleştirme işlemi için kullanılacak komut satırı programının (GhostScript)
 * sistemdeki tam yolunu döndürür. <br>
 * t-config.php dosyasında bir yol belirtilmişse, bu yolu, yoksa default
 * yolu döndürür
 *
 * @staticvar type $gs
 * @return \string
 */
function GetGhostScriptExe()
{
	static $gs = null;

	if ($gs !== null)
		return $gs;

	$gs = strval(@$GLOBALS['GHOSTSCRIPT_EXECUTABLE']);
	if (! $gs)
	{
		if (stristr(PHP_OS, "WIN"))
		{
			// Default olarak Program Files/gs/gsXXX klasörüne bakılacak
			$dir = "c:/Program Files/gs/";
			if (is_dir($dir)){
				$dirHandler = opendir($dir);
				while($file = readdir($dirHandler))
					if (preg_match("/gs[0-9]+\.[0-9]+/", $file))
					{
						$dir .= $file . "/";
						break;
					}
			}
			$gs = $dir . "bin/gswin64c.exe";
		}
		else
			$gs = "gs";
	}

	return $gs;
}

/**
 * GhostScript programının sisteme kurulu olup olmadığını denetler.
 *
 * @return bool
 */
function CheckGhostScriptExists()
{
	static $result = null;
	if ($result !== null)
		return $result;
	return $result = CheckExternalProgramExists(GetGhostScriptExe());
}

/**
 * Göreceli veya tam yolu verilen PDF dosyalarını birleştirip, tek dosya
 * haline getirir ve ikinci parametre belirtilen adla kaydeder
 *
 * @param string[] $pdfs
 * @param string[] $outputFile
 * @return string|int
 */
function MergePdfs($pdfs = array(), $outputFile = 'prv/output.pdf')
{
	if (! CheckGhostScriptExists())
		return "Sisteme GhostScript yüklenmediği için işlem yapılamadı";

	$gs = GetGhostScriptExe();
	$outputFile = FullPath($outputFile);
	$pdfs = array_map('FullPath', $pdfs);
	if (stristr(PHP_OS, "WIN"))
	{
		$gs = str_replace ("/", "\\", $gs);
		$gs = '"'. $gs . '"';

		$outputFile = str_replace("/", "\\", $outputFile);
		$pdfs = array_map(function($pdf) { return str_replace("/", "\\", $pdf);}, $pdfs);
	}

	$command = "$gs -dBATCH -dNOPAUSE -q -sDEVICE=pdfwrite -sOutputFile=$outputFile " .
		implode(" ", $pdfs);

	$output = array();
	exec($command, $output);

	if (file_exists($outputFile))
		return 1;

	return "HATA: " . implode("\n", $output);
}

/**
 * HTML'den PDF'e dönüşüm yapmak için kullanılacak kütüphanenin WebKit Html to Pdf (wkhtmltopdf)
 * sistemdeki tam yolunu döndürür. <br>
 * t-config.php dosyasında bir yol belirtilmişse, bu yolu, yoksa default
 * yolu döndürür
 *
 * @staticvar type $wk
 * @return \string
 */
function GetWkHtmlToPdfExe()
{
	static $wk = null;

	if ($wk !== null)
		return $wk;
	$isWindows = stristr(PHP_OS, "WIN");

	$wk = isLocalhost() && $isWindows ? '' : strval(Config('app.WKHTMLPDF_EXECUTABLE'));
	if (! $wk)
	{
		if ($isWindows)
			// Default olarak Program Files/wkhtmltopdf/bin klasörüne bakılacak
			$wk = 'C:/Program Files/wkhtmltopdf/bin/wkhtmltopdf.exe';
		else
			$wk = "wkhtmltopdf";
	}

	return $wk;
}

/**
 * WkHtmlToPdf programının sisteme kurulu olup olmadığını denetler.
 *
 * @return bool
 */
function CheckWkHtmlToPdfExists()
{
	static $result = null;
	if ($result !== null)
		return $result;
	return $result =  CheckExternalProgramExists(GetWkHtmlToPdfExe());
}

/**
 * Verilen HTML metnini PDF'e çevirerek, üçüncü parametrede
 * verilen dosya konumuna kaydeder
 *
 * @param string $html
 * @param string $orientation 'Landscape' veya 'Portrait'
 * @param string $outputFile
 * @return string|int
 */
function HtmlToPdf($html, $orientation = 'Portrait',
	$outputFile = "prv/result.pdf",
	$headerHtml = '', $footerHtml = '',
	$disableJavascript = true,
	$viewPortSize = '',
	$disableSmartShrinking = true)
{
	if (!CheckWkHtmlToPdfExists())
		return "Sisteme WebKit PDF/Resim çevirici yüklenmediği için işlem yapılamadı";

	$wk = GetWkHtmlToPdfExe();

	$outputFile = FullPath($outputFile);
	$htmlFile = FullPath("prv/temp.htm");
	file_put_contents($htmlFile, $html);
	DosyaSistem::Sil($outputFile);

	$headerOption = '';
	$headerFile = '';
	$footerFile = '';
	if ($headerHtml)
	{
		$headerFile = FullPath("prv/header.html");
		file_put_contents($headerFile, $headerHtml);
		$headerOption = " --header-html $headerFile ";
	}

	if ($footerHtml)
	{
		$footerFile = FullPath("prv/footer.html");
		file_put_contents($footerFile, $footerHtml);
		$headerOption .= " --footer-html $footerFile ";
	}

	if (stristr(PHP_OS, "WIN"))
	{
		$wk = str_replace ("/", "\\", $wk);
		$wk = '"'. $wk . '"';

		$outputFile = str_replace("/", "\\", $outputFile);
		$htmlFile = str_replace("/", "\\", $htmlFile);
		$headerOption = str_replace("/", "\\", $headerOption);
	}

	if ($disableJavascript)
		$disableJavascript = '--disable-javascript';
	else
		$disableJavascript = '';

	if ($viewPortSize)
		$viewPortSize = "--viewport-size $viewPortSize";
	if ($disableSmartShrinking)
		$disableSmartShrinking = '--disable-smart-shrinking';
	$command = "$wk --print-media-type $disableJavascript $viewPortSize ".
		"--margin-bottom 22mm --margin-top 10mm $disableSmartShrinking ".
		"--orientation $orientation $headerOption $htmlFile $outputFile 2>&1";
	$output = array();
	exec($command, $output);

	DosyaSistem::Sil($htmlFile);
	if ($headerFile)
		DosyaSistem::Sil($headerFile);
	if ($footerFile)
		DosyaSistem::Sil($footerFile);
	if (file_exists($outputFile))
	{
		chmod($outputFile, 0777);
		return 1;
	}

	return "HATA: " . StringLib::Cut(implode("\n", $output), 100);
}

/**
 * Verilen HTML metnini JPG'e çevirerek, üçüncü parametrede
 * verilen dosya konumuna kaydeder
 *
 * @param string $html
 * @param int $width
 * @param string $outputFile
 * @return string|int
 */
function HtmlToImage($html, $width = 1280, $outputFile = "prv/result.jpg")
{
	if (!CheckWkHtmlToPdfExists())
		return "Sisteme WebKit PDF/Resim çevirici yüklenmediği için işlem yapılamadı";

	$wk = GetWkHtmlToPdfExe();

	$outputFile = FullPath($outputFile);
	$htmlFile = FullPath("prv/temp.htm");
	file_put_contents($htmlFile, $html);
	DosyaSistem::Sil($outputFile);

	if (stristr(PHP_OS, "WIN"))
	{
		$wk = str_replace ("/", "\\", $wk);
		$wk = '"'. $wk . '"';
		$wk = str_replace("wkhtmltopdf.exe", "wkhtmltoimage.exe", $wk);

		$outputFile = str_replace("/", "\\", $outputFile);
		$htmlFile = str_replace("/", "\\", $htmlFile);
	}
	else
		$wk = preg_replace("/wkhtmltopdf$/", "wkhtmltoimage", $wk);

	$command = "$wk --width $width $htmlFile $outputFile 2>&1";
	$output = array();
	exec($command, $output);

	DosyaSistem::Sil($htmlFile);
	if (file_exists($outputFile))
	{
		chmod($outputFile, 0777);
		return 1;
	}

	return "HATA: " . StringLib::Cut(implode("\n", $output), 100);
}

function isDeveloperPage()
{
	return in_array(@$_GET['act'], array('developer', 'cisc', 'db_model'));
}

function PhpFileWriteArray($filename, $array, $withKeys = true)
{
	$SerializeArray = function ($array, $withKeys) use(&$SerializeArray){
		$dizi = array();
		foreach($array as $key => $value)
		{
			if (is_array($value))
				$value = $SerializeArray($value, $withKeys);
			elseif (! is_numeric($value))
				$value = "'" . addslashes($value) . "'";
			if ($withKeys)
				$value = "'" . addslashes($key) . "'=>$value";
			$dizi[] = $value;
		}
		return 'array('. implode(',', $dizi) .')';
	};
	$dizi = $SerializeArray($array, $withKeys);
	$content = "<?php return $dizi;";
	file_put_contents($filename, $content);
	chmod($filename, 0777);
}

function PhpFileReadArray($filename)
{
	if (file_exists($filename))
		return include($filename);
	return NULL;
}
