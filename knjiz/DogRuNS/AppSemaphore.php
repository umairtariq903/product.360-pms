<?php
namespace DogRu;

/**
 * Uygulam içerisinde aynı anda sadece bir progress de çalışması gereken kodlar için
 * dosya bazlı kilit sistemi kullanır.
 *
 */
class AppSemaphore
{
	private $lockFilePath;

	public static function begin($name, $expireHour = 1)
	{
		$inst = new AppSemaphore();
		$sid = session_id();
		srand();
		$pid = md5(rand(0, getrandmax()) . $sid);
		$fname = $inst->lockFilePath = "prv/$name.lock";

		if (file_exists($fname) && (time() - filemtime($fname) < 60*60*$expireHour))
			return false;

		@file_put_contents($fname, $pid);
		usleep(1e5); // 100ms bekle
		$lockedPID = @file_get_contents($fname);
		if ($pid === $lockedPID)
			return $inst;
		return false;
	}

	public function release()
	{
		if (file_exists($this->lockFilePath))
			@unlink($this->lockFilePath);
		return false;
	}
}
