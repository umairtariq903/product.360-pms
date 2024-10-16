<?php
require_once __DIR__ . '/vendor/autoload.php';

ini_set('error_log','prv/php_errors.log');
ini_set('display_errors', '0');
ini_set('memory_limit','512M');

if (!setlocale(LC_TIME, 'TR') && !setlocale(LC_TIME, 'tr_TR') && !setlocale(LC_TIME, 'tr_TR.UTF-8'))
	setlocale(LC_TIME, 'tr_TR.utf8');
global $SITE_TIMEZONE;
if(!isset($SITE_TIMEZONE))
    $SITE_TIMEZONE = "Etc/GMT-3";
date_default_timezone_set($SITE_TIMEZONE);
error_reporting(E_ERROR);
global $LoadBeginTime;
if(!isset($LoadBeginTime))
	$LoadBeginTime = microtime(TRUE);

if(! defined('KNJIZ_DIR'))
	define('KNJIZ_DIR', str_replace('\\', '/', __DIR__) . '/');

define('LIB_ERRORHANDLE', KNJIZ_DIR . 'dogru/ErrorHandler/ErrorHandler.php');
define('LIB_DEBUG', KNJIZ_DIR . 'dogru/Debug/Debug.php');
define('LIB_MYSQL', KNJIZ_DIR . 'dogru/DB/DB.php');
define('LIB_PAGECONTROLLER', KNJIZ_DIR . 'dogru/PageController/PageController.php');
define('LIB_AUTOLOADER', KNJIZ_DIR . 'dogru/AutoLoader/AutoLoader.php');
define('LIB_TEMA', KNJIZ_DIR . 'dogru/Tema/Tema.php');
define('LIB_DBMODEL_UI', KNJIZ_DIR . 'dogru/DevTools/DbModelUI/DbModelUI.php');
define('LIB_DEVTOOLS', KNJIZ_DIR . 'dogru/DevTools/DevTools.php');
// Register edilebilr kütüphaneler
define('LIB_UTILS', KNJIZ_DIR . 'dogru/Utils');
define('LIB_DBMODEL', KNJIZ_DIR . 'dogru/DbModel');
define('LIB_DATAGRID', KNJIZ_DIR . 'dogru/DataGrid');
define('LIB_NET_SFTP', KNJIZ_DIR . 'others/SSH/Net/SFTP.php');
define('LIB_HTML_DOM', KNJIZ_DIR . 'others/simple_html_dom/simple_html_dom.php');


require_once KNJIZ_DIR . 'App.php';
require_once KNJIZ_DIR . 'functions.php';

class LibLoader
{
	// Sayfalara ekstra yüklenebilecek Php Kütüphaneleri
	public static $PagePhpLibs = array(
		'LIB_DATAGRID' => 'DataGrid için gerekli olan kütüphane',
		'LIB_NET_SFTP' => 'FTP/SFTP bağlantısı için gerekli olacak kütüphane',
		'LIB_HTML_DOM' => 'Html içerik parse etmek için kullanıbilecek kütüphane'
	);

	private static $Loaded = array();

	public static function Load($lib, $showError = true)
	{
		if(in_array($lib, self::$Loaded))
			return;
		if(isFile($lib))
		{
			self::$Loaded[] = $lib;
			require_once  $lib;
		}
		elseif($showError)
			echo "$lib yüklenemedi.";
	}

	public static function IsLoaded($lib)
	{
		return in_array($lib, self::$Loaded);
	}

	public static function Register($lib)
	{
		AutoLoader::AddFolder(FullPath($lib, KNJIZ_DIR));
	}

	public static function LoadAll()
	{
		// varsa PhpError kontrol
		LibLoader::Load(LIB_ERRORHANDLE, false);
		// Class otomatik yükleme
		LibLoader::Load(LIB_AUTOLOADER);
		// Sayfa Yönetimi
		LibLoader::Load(LIB_PAGECONTROLLER);
		// varsa Debug kontrol
		LibLoader::Load(LIB_DEBUG, false);
		if (isDir(KNJIZ_DIR . 'dogru/DevTools'))
		{
			if(@$_GET['act'] == 'developer')
				LibLoader::Load(LIB_DEVTOOLS);
			if(@$_GET['act'] == 'db_model')
				LibLoader::Load(LIB_DBMODEL_UI);
		}
		// MySQL bağlantı nesnesi
		LibLoader::Load(LIB_MYSQL);

		LibLoader::Register(dirname(LIB_MYSQL));
		// Utilities
		LibLoader::Register(LIB_UTILS);
		// Veritabanı modelleri ve sınıfları
		LibLoader::Register(LIB_DBMODEL);
		// DataGrid
		LibLoader::Register(LIB_DATAGRID);
//		Debug::Begin();
		if (@$_GET['act'] == 'file_upload')
			include_once KNJIZ_DIR. '/fileupload.php';

		if(@$_GET['SavePhoto'] == 1)
		{
			$filename = 'Photo_' . rand(10000, 99999). '.jpg';
			DosyaSistem::KlasorOlustur('apli_dat/prv');
			$FullFileName= "apli_dat/prv/".$filename;
			file_put_contents($FullFileName, file_get_contents('php://input') );
			$file = new stdClass();
			$file->name = $filename;
			$file->url = $FullFileName;
			$arr[] = 'Logo';
			$arr[] = null;
			$arr[] = $file;
			$arrEncode = json_encode($arr);

			die($arrEncode);
		}
	}
}

$match = array();
if (@$_GET['act'] == 'js'
	&& @$_GET['js'] != ''
	&& ! preg_match("/(http|\.\.)/i", $_GET['js'])
	&& preg_match("/\.(js|map|css|png|gif|jpg|htm|swf|tff|woff|woff2|eot|svg)$/i", $_GET['js'], $match))
{
	session_write_close();
	$fn = KNJIZ_DIR . $_GET['js'];
	$headers = getallheaders();
	// Checking if the client is validating his cache and if it is current.
	if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($fn)))
	{
		// Client's cache IS current, so we just respond '304 Not Modified'.
		header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($fn)).' GMT', true, 304);
	}
	else
	{
        // Image not cached or cache outdated, we respond '200 OK' and output the image.
        header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime($fn)).' GMT', true, 200);
        header('Content-Length: '.filesize($fn));
		$ext = $match[1];
		switch($ext)
		{
			case 'js':
				header('Content-Type: application/javascript');
				break;
			case 'css':
				header('Content-Type: text/css');
				header('X-Content-Type-Options: nosniff'); // For IE 8+
				break;
			case 'jpg':
				header('Content-Type: image/jpeg');
				break;
			case 'htm':
				header('Content-Type: text/html');
				break;
			case 'swf':
				header('Content-Type: application/x-shockwave-flash');
				break;
			case 'woff':
			case 'woff2':
				header("Content-Type: application/x-font-$ext");
				break;
			default:
				header("Content-Type: image/$ext");
				break;
		}
		header("Expires: " . date("D, j M Y H:i:s", strtotime("+2 hour")) . " GMT");
        readfile($fn);
	}
	exit;
}

if (@$_GET['act'] == 'js')
	exit;

if (@$_GET['rewrite'])
{
	$parts = explode('/', $_GET['rewrite']);
	$i = '';
	foreach($parts as $part)
	{
		$params = explode('=', $part);
		if (count($params) == 1)
		{
			$_GET['act' . $i] = $part;
			$i = $i ? intval($i) + 1 : 2;
		}
		else
			$_GET[$params[0]] = $params[1];
	}
	unset($_GET['rewrite']);
}
App::Init();

