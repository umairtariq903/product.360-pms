<?php
if(! defined('NET_SFTP_LOCAL_FILE'))
	define('NET_SFTP_LOCAL_FILE', 1);
if(! defined('NET_SFTP_STRING'))
	define('NET_SFTP_STRING',  2);

class FtpClient
{
	public $Host;
	public $Port = 21;

	public $errors = array();

	public $ins = null;

	/**
	 * @return FtpClient|Net_SFTP
	 */
	public static function Get($isSFTP, $Host, $Port)
	{
		if (! $isSFTP)
			return new FtpClient($Host, $Port);
		LibLoader::Load(LIB_NET_SFTP);
		return new Net_SFTP($Host, $Port, 60);
	}

	public function __construct($Host, $Port)
	{
		$this->Host = $Host;
		$this->Port = $Port;
	}

	public function login($user, $pass)
	{
		$this->ins = ftp_connect($this->Host, $this->Port, 30);
		if ($this->ins)
		{
			if(! ftp_login($this->ins, $user, $pass))
				$this->errors[] = 'Login işlemi yapılamadı';
            ftp_pasv($this->ins,true);
		}
		else
			$this->errors[] = 'Sunucuya bağlantı kurulamadı';

		return ! $this->errors;
	}

	public function disconnect()
	{
		ftp_close($this->ins);
	}

	public function getLastError()
    {
        return end($this->errors);
    }

	public function chdir($dir)
	{
		return ftp_chdir($this->ins, $dir);
	}

	public function mkdir($dir)
	{
		return ftp_mkdir($this->ins, $dir);
	}

	public function chmod($mode, $filename)
	{
		return ftp_chmod($this->ins, $mode, $filename);
	}

	public function put($remote_file, $data, $mode = NET_SFTP_STRING)
	{
		if($mode == NET_SFTP_STRING)
		{
			$f = App::$TmpDir . '/ftp_temp_file.tmp';
			file_put_contents($f, $data);
			$data = $f;
		}
		$sonuc = ftp_put($this->ins, $remote_file, $data, FTP_BINARY);
		if($mode == NET_SFTP_STRING)
			@unlink($f);
		return $sonuc;
	}

	public function get_file($remote_file, $local_file = false)
	{
		$local = $local_file ? $local_file : App::$TmpDir . '/tmp_';
		$resp = ftp_get($this->ins, $local, $remote_file , FTP_BINARY);
		if (! $resp)
			return $local_file ? $resp : '';
		return $local_file ? TRUE : file_get_contents($local);
	}

	public function delete($path, $recursive = true)
	{
		$handle = $this->ins;
		if (@ftp_delete($handle, $path) === false)
		{
			if ($recursive)
			{
				$children = @ftp_nlist($handle, $path);
				if($children)
					foreach($children as $p)
						$this->delete($p);
			}
			@ftp_rmdir($handle, $path);
		}
	}
}
