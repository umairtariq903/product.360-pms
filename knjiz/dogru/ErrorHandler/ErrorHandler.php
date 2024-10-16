<?php
require_once 'PhpErrorList.php';
require_once 'PhpError.php';

$GLOBALS['CANCEL_FATAL_ERROR_HANDLER'] = 0;

function ErsErrorHandler($errno, $errstr, $errfile, $errline)
{
	if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        return;
    }
	if(@$_GET['act'] == 'cisc')
		return;

	$err = new PhpError();
	$err->ErrFile = RelativePath($errfile);
	$err->ErrLine = $errline;
	$err->ErrStr = $errstr;
	$err->ErrNo = $errno;
	$err->SesionId = session_id();
	$err->ErrTime = date('d-m-Y H:i:s');
	$err->BackTree = PhpErrorList::CallStackArray();
	PhpErrorList::Get()->Add($err);

	if(!defined('E_DEPRECATED'))
		define ('E_DEPRECATED', 8192);
	if(!defined('E_USER_DEPRECATED'))
		define ('E_USER_DEPRECATED', 16384);
	$recoverable = array(
		E_DEPRECATED, E_STRICT, E_USER_DEPRECATED
	);
	if (! in_array($errno, $recoverable))
	{
		if(class_exists("Transaction") && Transaction::IsActive())
			ThrowException($err->Show());
		else if (Debug::$IsAktif)
			echo $err->Show();
	}
    /* execute PHP internal error handler */
    return false;
}

function fatal_handler()
{
	$errstr  = "";
	$error = error_get_last();
	if( $error !== NULL)
		$errstr  = $error["message"];
	if(preg_match('/Class .* not found/i', $errstr) && LibLoader::IsLoaded(LIB_AUTOLOADER))
	{
		AutoLoader::UnlinkClassPath();
		echo "ClassPath.pdt recreated.";
		return;
	}

	if ($GLOBALS['CANCEL_FATAL_ERROR_HANDLER'] == 1)
		return;

	if ($error !== null && in_array($error['type'], array(E_ERROR, E_PARSE)))
	{
		$gets = print_r($_GET, true);
		$posts = print_r($_POST, true);
		$error = print_r($error, true);
		$dir = App::$TmpDir . 'logs';
		if (! is_dir($dir))
			mkdir($dir);
		file_put_contents($dir . '/last_fatal_error.log',
			"#HATA\n$error\n#GET\n$gets\n#POST\n$posts");
	}
}

set_error_handler("ErsErrorHandler");
register_shutdown_function( "fatal_handler" );