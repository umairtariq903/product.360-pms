<?php
function GetExt($file)
{
	$parts = explode('.', $file);
	return end($parts);
}

if (! function_exists("filesize64"))
{
	function filesize64($file)
	{
		static $exec_works = null;

		$iswin = strtoupper(substr(PHP_OS, 0, 3)) == 'WIN';

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
}

function Download($path, $name = '')
{
	$path = str_replace('../', '', $path);
	while(ob_get_level() > 0)
		ob_end_clean();

	$name = $name ? $name : basename($path);

	if (is_file($path = realpath($path)) === true)
	{
		$file = @fopen($path, 'rb');
		$size = filesize64($path);
		$speed = 1024;

		if (is_resource($file) === true)
		{
			set_time_limit(0);

			if (strlen(session_id()) > 0)
				session_write_close();

			$range = array(0, $size - 1);

			if (array_key_exists('HTTP_RANGE', $_SERVER) === true)
			{
				$range = array_map('intval', explode('-', preg_replace('~.*=([^,]*).*~', '$1', $_SERVER['HTTP_RANGE'])));

				if (empty($range[1]) === true)
					$range[1] = $size - 1;

				foreach($range as $key => $value)
					$range[$key] = max(0, min($value, $size - 1));

				if (($range[0] > 0) || ($range[1] < ($size - 1)))
					header(sprintf('%s %03u %s', 'HTTP/1.1', 206, 'Partial Content'), true, 206);
			}

			header('Accept-Ranges: bytes');
			header("Content-Range: bytes $range[0]-$range[1]/$size");

			header('Pragma: public');
			header('Cache-Control: public, no-cache');
			header('Content-Type: application/octet-stream');
			if ($size < 1000 * 1000 * 1000 * 16.)
				header('Content-Length: ' . ($range[1] - $range[0] + 1));
			header('Content-Disposition: attachment; filename="' . $name . '"');
			header('Content-Transfer-Encoding: binary');

			if ($range[0] > 0)
			{
				fseek($file, $range[0]);
			}

			while((feof($file) !== true) && (connection_status() === CONNECTION_NORMAL))
			{
				echo fread($file, round($speed * 1024));
				flush();
			}

			fclose($file);
		}

		exit();
	}
	else
		header(sprintf('%s %03u %s', 'HTTP/1.1', 404, 'Not Found'), true, 404);

	return false;
}
if ($_GET['f'] == 'test_rewrite')
	die('OK');
$base = @$_GET['t'] == 0 ? $GLOBALS['APP_BACKUP_DIR'] : 'apli_dat';
$filePath = $_GET['f'];
$fileName = '';
if (preg_match("/([^;|]*)[;|](.*)$/", $filePath, $parts))
{
	$filePath = $parts[1];
	$fileName = $parts[2];
	$ext = GetExt($filePath);
	if($ext != GetExt($fileName))
		$fileName .= ".$ext";
}
Download("$base/$filePath", $fileName);
