<?php
class Transaction
{
	const T_APPLICATION = 0;
	const T_DATABASE = 1;
	public static $Type = self::T_APPLICATION;

	private static $ShowInLog = TRUE;
	private static $TransList = array();

	public $Active = TRUE;

	/** RollBack te çalıştırılacak sorgular listesi */
	private $DB_Rollback = array();
	/** RollBack te çalıştırılacak dosya sistemi için komutlar listesi*/
	private $FS_Rollback = array();
	/** Commit te çalıştırılacak dosya sistemi için komutlar listesi*/
	private $FS_Commit = array();

	private $LastAddedCount = 0;

	public static function IsDbTrans()
	{
		return self::$Type == self::T_DATABASE;
	}

	public static function IsActive()
	{
		$trans = self::GetCurrent();
		return ($trans && $trans->Active);
	}

	public static function Begin()
	{
		self::$TransList[] = new Transaction();
		if(self::IsDbTrans())
			DB::TransBegin();
	}

	public static function Commit()
	{
		/* @var $trans Transaction */
		$trans = array_pop(self::$TransList);
		if($trans)
			$trans->_Commit();
	}

	public static function Rollback()
	{
		/* @var $trans Transaction */
		$trans = array_pop(self::$TransList);
		if($trans)
			$trans->_Rollback();
	}

	/**
	 * Aktif olan transaction nesnesini döndürür
	 * @return Transaction
	 */
	public static function GetCurrent()
	{
		if(count(self::$TransList) > 0)
			return end(self::$TransList);
		return NULL;
	}

	/**
	 * Belirtilen fonksiyonu try-catch ve Transaction arasında çağırır ve sonucu dönderir.
	 * Fonksiyon sonucu 1 den farklı ise yapılan tüm DB işlemini geri alır.
	 */
	public static function TryCall($funcName)
	{
		$params = func_get_args();
		array_shift($params);
		try
		{
			Transaction::Begin();
			$sonuc = call_user_func_array($funcName, $params);
			if($sonuc != 1)
				Transaction::Rollback();
		}
		catch(Exception $exc)
		{
			Transaction::Rollback();
			$sonuc = $exc->getMessage();
		}
		return $sonuc;
	}

	public function FS_AddRollback($command)
	{
		$this->FS_Rollback[] = $command;
	}

	public function FS_AddCommit($command)
	{
		$this->FS_Commit[] = $command;
	}

	/**
	 * Veri tabanına insert işlemi yapıldıktan sonra çağrılması gerekir.
	 */
	public function AddInserted($tableName, $idName, $id)
	{
		if(self::IsDbTrans())
			return;
		$this->DB_Rollback[] = "DELETE FROM $tableName WHERE $idName = '$id'";
		$this->LastAddedCount = 1;
	}

	/**
	 * Veri tabanına delete işlemi yapılmadan önce çağrılması gerekir.
	 */
	public function AddWillDeleted($tableName, $where)
	{
		if(self::IsDbTrans())
			return;
		$GLOBALS['WRITE_MYSQL_LOG'] = self::$ShowInLog;
		$rows = DB::FetchAssoc("SELECT * FROM $tableName WHERE $where", '', 'ForTrans');
		$GLOBALS['WRITE_MYSQL_LOG'] = TRUE;
		foreach($rows as $row)
			$this->DB_Rollback[] = "INSERT INTO $tableName SET " . $this->GetRowSets($row);
		$this->LastAddedCount = count($rows);
	}

	/**
	 * Veri tabanına update işlemi yapılmadan önce çağrılması gerekir.
	 */
	public function AddWillUpdated($tableName, $where)
	{
		if(self::IsDbTrans())
			return;
		$GLOBALS['WRITE_MYSQL_LOG'] = self::$ShowInLog;
		$rows = DB::FetchAssoc("SELECT * FROM $tableName WHERE $where", '', 'ForTrans');
		$GLOBALS['WRITE_MYSQL_LOG'] = TRUE;
		foreach($rows as $row)
			if(isset($row['id']))
				$this->DB_Rollback[] = "UPDATE $tableName SET " . $this->GetRowSets($row)
					. "\nWHERE id = '$row[id]'";
		$this->LastAddedCount = count($rows);
	}

	/**
	 * Son eklenen update/delete işlemlerinde etkilenen satır sayısı 0 ise
	 * bu fonksiyon çağrılarak gereksiz duruma düşen sorgular transaction
	 * listesinden çıkartılır. Bu da bize hız ve bellekten kazanç sağlar.
	 */
	public function DeleteLast()
	{
		for($i=0; $i < $this->LastAddedCount; $i++)
			array_pop($this->DB_Rollback);
		$this->LastAddedCount = 0;
	}

	private function GetRowSets($row)
	{
		$sets = array();
		foreach($row as $key => $value)
		{
			if(is_null($value))
				$value = 'NULL';
			else if(! is_numeric($value))
				$value = "'" . addslashes($value) . "'";
			$sets[] = "`$key` = $value";
		}
		return implode(",\n\t", $sets);
	}

	private function _Commit()
	{
		if(self::IsDbTrans())
			DB::TransCommit();
		while($command = array_pop($this->FS_Commit))
			eval($command);
	}

	private function _Rollback()
	{
		$this->Active = FALSE;
		if(self::IsDbTrans())
			DB::TransRollback();
		// DB işlemlerini geri al
		while($sorgu = array_pop($this->DB_Rollback))
			DB::Execute($sorgu, 'Rollback');
		// Dosya işlemlerini geri al
		while($command = array_pop($this->FS_Rollback))
			eval($command);
	}
}