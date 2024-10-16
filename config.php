<?php 

if (preg_match("/config\.php/", $_SERVER['SCRIPT_NAME']))
{
	header('Location:index.php?');
	exit;
}

$SITE_TIMEZONE = "Europe/Amsterdam";

$SITE_KLASORU = 'C:/xampp/xampp_7.2/htdocs/PMS_SERVER/';
// $SITE_KLASORU = '/var/www/vhosts/product.360-pms.com/httpdocs/';
// $SITE_URL = "https://".$_SERVER['HTTP_HOST']."/";
$SITE_URL = "http://".$_SERVER['HTTP_HOST']."/PMS_SERVER/";

$STATUS = 'TEST' ; // TEST | PRODUCTION
require_once 'knjiz/LibLoader.php';
//require_once '../dgr/dogru_libs_net/LibLoader.php';
$DebugIsActive = false;
LibLoader::Load(LIB_PAGECONTROLLER);
LibLoader::Load(LIB_DEBUG);
LibLoader::Load(LIB_TEMA);
Debug::$IsAktif = false;
LibLoader::Load(LIB_MYSQL);

$TEMA = 't-demo';
$TEMA_ADRES_SATIRI_SECIMI = false;
$SITE_ADI = 'Product PMS Server';
