<?php
//@ConfigsVars-Start
ConfigValues(array(
	'app.DB_INFO'       => '2_UY5dRd9p8JaXQsvYO6jdRd9p8IiXOs5URM1iP24v8MznRcDqOdDURsno8IiXT79aSMrWR6GXEI5lSMvZNd5kRdCXAo5lO79oTcvnOo4v8LLWBsGqCZ1eFY5y_57',
	'app.DB_INFO_LOCAL' => '2_UY5dRd9p8JaXC34sBIyjBoqm8IiXOs5URM1iP24v8MznRcDqOdDURsnoNd9aSNLaSI4h8NHoP75jO6na8JaXSMvkSo4h8MzWSd9sRd5Z8JaXGsPnC34oCo0XV0_8',
	'app.DB_INFO_TEST'  => '2_UY5dRd9p8JaXQsvYO6jdRd9p8IiXOs5URM1iP24v8MziSbvoP75rP75USsHoSo4h8NHoP75jO6na8JaXOsPnNdDaSdCXAo5lO79oTcvnOo4v8KLm8ZSsP6mtE25y_82',
	'app.MAILER_AYAR'   => '2_UY5dRd9p8JaXScnpRoruO6rZP7SjOcviBNDn8IiXT79aSI4v8M0jOsvcSNG/CZKlB6vjQsXjP6nWSMfaSsXjPYrYRcmXAo5lO79o8JaXO4LPT20lDcar9JTYJ5DME24h8M1qSsSXEI4m8IiXRsvnSo4v8JGtDY4h8N9oQo4v8NDhSY5y_13',
	'app.USE_BS_UI'     => TRUE,
	'app.USE_METRONIC'  => TRUE
));
//@ConfigsVars-End

define('USE_NEW_JQUERY', TRUE);

// Veritabanı bağlantı ayarları
// (Temaya ozgu DB ayarı varsa, config.php'e default ayar
// buraya da temaya özgü ayar yazılacaktır)
$dInfo = Config('app.DB_INFO_LOCAL');
if(! isLocalhost())
{
    global $STATUS;
    if($STATUS == "PRODUCTION")
        $dInfo = Config('app.DB_INFO');
    else
        $dInfo = Config('app.DB_INFO_TEST');
}

DB::Set($dInfo['host'], $dInfo['db_name'], $dInfo['username'], $dInfo['password']);

// Bilgilendirme email'leri hangi email tarafindan gonderilecek ve
// hangi email'e yanit yazilacak:
$SiteEmailFrom = 'a.dogru@360-onlinemarketing.com';

$GLOBALS['BASE_TEMA'] = 'b-default';
$GLOBALS['BASE_TEMA_TYPE'] = 'default';

$mailerAyar = Config('app.MAILER_AYAR');
if(count($mailerAyar) > 0)
    Mailer::UsePhpMailer($mailerAyar['host'], $mailerAyar['user'], $mailerAyar['pass'], $mailerAyar['auth'], $mailerAyar['port'], $mailerAyar['ssl']);
