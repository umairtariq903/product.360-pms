<?php
class MemSession
{
	public static function Open($savePath, $sessionID)
	{
		return DB::Execute("
			CREATE TABLE IF NOT EXISTS `sessions` (
			  `id` char(32) NOT NULL,
			  `contents` varchar(20000) NOT NULL,
			  `modify_date` timestamp NOT NULL default '0000-00-00 00:00:00',
			  PRIMARY KEY  (`id`),
			  KEY `modify_date` (`modify_date`)
			) ENGINE=MEMORY", 'MemSesion Create');
		self::GarbageCollect();
	}

	public static function Close()
	{
		return true;
	}


	public static function Read($sessionID)
	{
		$sql = 'SELECT contents FROM sessions WHERE id = "' . DB::EscapeString($sessionID) . '"';
		return DB::FetchScalar($sql);
	}

	public static function Write($sessionID, $sessionData)
	{
		$sessionID = DB::EscapeString($sessionID);
		$sessionData = DB::EscapeString($sessionData);

		$sql = "INSERT INTO sessions (id, contents, modify_date)
			VALUES ('$sessionID', '$sessionData', NOW() )
			ON DUPLICATE KEY UPDATE contents = '$sessionData', modify_date = NOW()";
		return DB::Execute($sql);
	}

	public static function Destroy($sessionID)
	{
		return DB::Execute('DELETE FROM sessions
			WHERE id = ' . DB::EscapeString($sessionID));
	}

	public static function GarbageCollect($sessionMaxLifetime = null)
	{
		if($sessionMaxLifetime === null)
			$sessionMaxLifetime = 15 * 60;
		$sessionMaxLifetime = intval($sessionMaxLifetime);
		return DB::Execute("DELETE FROM sessions
			WHERE modify_date < DATE_ADD( NOW(), INTERVAL $sessionMaxLifetime SECOND)");
	}

	public static function Register()
	{
		DB::Connect();
		session_set_save_handler('MemSession::Open', 'MemSession::Close', 'MemSession::Read'
			, 'MemSession::Write', 'MemSession::Destroy', 'MemSession::GarbageCollect');
	}
}
