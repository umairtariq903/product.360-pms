<?php
class DB
{
	public static $Updating = false;
	public static $SEND_HEAVY_QUERY = true;

	public $UseCompress = false;
	public $Host;
	public $Port = 3306;
	public $DbName;
	public $DefaultDbName;
	public $User;
	public $Pass;
	public $link;
	private $CacheFile = '';
	private $CacheName = '';
	private $CacheTime = 0;
	private $CacheDb = array();
	private $WaitTimeout = 60;
	private $lastTime = 0;

	protected static $Instance = NULL;

	/**
	 * @return DB
	 */
	public static function Get()
	{
		
		
		if(DB::$Instance == NULL)
			DB::$Instance = new DB();
		return DB::$Instance;
	}

	public static function Set($host, $db_name = 'product_pms_server', $user = 'root', $pass, $force = false, $useComp = FALSE)
	{
		
		if(DB::$Instance && ! $force)
			return DB::$Instance;
		$db = DB::Get();
		
		$regs = array();
		if(preg_match('/(.*):(.*)/i', $host, $regs))
		{
			$host = $regs[1];
			$db->Port = $regs[2];
			
		}
		else
			$db->Port = 3306;
		$db->Host = $host;
		
		$db->DbName = 'product_pms_server';
		$db->User = 'root';
		
		$db->Pass = 'Dgr1234!';
		
	
		return $db;
	}

	public static function SetFromArray($array)
	{
		return self::Set($array['host'], $array['name'], $array['user'], $array['pass']);
	}

	public static function Reconnect($waitTimeout = 60)
	{
		self::Disconnect();
		self::Connect($waitTimeout);
	}

	public static function Disconnect()
	{
		$db = DB::Get();
		if(! $db->link)
			return 1;
		return mysqli_close($db->link);
	}

	public static function Connect($waitTimeout = 60)
	{
	
		$db = DB::Get();
		
		$u = DgrCode::Decode($db->User);
		$p = '';
		
		if($db->UseCompress)
		{
			$db->link = mysqli_init();
			mysqli_real_connect($db->link, "$db->Host", $u, $p, $db->DbName, $db->Port, null);
		}
		else
			$db->link = mysqli_connect($db->Host, $u, $p, $db->DbName, $db->Port);

		if($db->link == false)
			return false;
		$db->lastTime = time();
		$charSet = 'latin5';
		$collation = 'latin5_turkish_ci';
		if (App::IsUTF8())
		{
			$charSet = 'utf8';
			$collation = 'utf8_turkish_ci';
		}
		if(isLocalhost())
			$waitTimeout = 1000;
		$db->WaitTimeout = $waitTimeout;
		$queries = array(
			"SET lc_time_names = 'tr_TR'",
			"SET NAMES '$charSet' ",
			"SET CHARACTER SET $charSet ",
			"SET COLLATION_CONNECTION = '$collation'",
			"SET @@SESSION.sql_mode = 'NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'"
		);
		if($waitTimeout > 0)
			$queries[] = "SET wait_timeout=$waitTimeout";
		DB::ExecuteMulti($queries, 'Encoding settings');
		return DB::SelectDb($db->DbName);
	}

	public static function SelectDb($db)
	{
		
		return mysqli_select_db(DB::Get()->link, $db);
	}

	public static function MySqlLog($query, $type = 1, $description = '')
	{
		$rs = array();
		$t1 = 1000 * microtime(true);
		$isDebug = LibLoader::IsLoaded(LIB_DEBUG) && Debug::$IsAktif;
		$db = DB::Get();
		
		if (time() - $db->lastTime >= $db->WaitTimeout)
			self::Reconnect();
		if($query != '')
		{
			if($type == 1)
				$rs = mysqli_query($db->link, $query);
			elseif($type == 2)
				mysqli_real_query($db->link, $query);
			elseif($type == 3)
				mysqli_multi_query($db->link, $query);
		}
		$db->lastTime = time();
		$rowCount = 0;
		if(mysqli_errno($db->link) > 0)
		{
			$err = mysqli_error($db->link);
			$matches = array();
			if (preg_match("|Duplicate entry '(.*)' for key '(.*)'|i", $err, $matches))
			{
				$duplicateVal = $matches[1];
				$indexName = $matches[2];
				return array($duplicateVal, $indexName);
			}
		}
		else{
			$err = "";
			if($type == 1){
				$rowCount = mysqli_num_rows($rs);
			}else
				$rowCount = mysqli_affected_rows($db->link);
		}
		$cost = 1000 * microtime(true) - $t1;

		global $totalCost;
		global $LoadBeginTime;
		global $qryCount;
		$TotalTime = 1000 * (microtime(true) - $LoadBeginTime);
		$totalCost += $cost;
		$qryCount++;

		$bilgi = "/**\n"
			. "* @Counter: $qryCount\n"
			. "* @Description: $description\n"
			. "* @Cost: $cost\n"
			. "* @TotalCost: $totalCost\n"
			. "* @Time: $TotalTime\n"
			. "* @RowCount: $rowCount\n"
			. "* @Error: $err\n"
			. "* @Url: $_SERVER[REQUEST_URI]\n"
			. "*/\n";
		if($query == '')
			$query = '/* Toplam süre: $TotalTime*/';

		//Hatalı sorgularda dogru a bildirim.
		if ($err)
		{
			$query = StringLib::RowTrim($query);
			if($isDebug)
				file_put_contents('prv/last_query.sql', $query);
			if(! self::$Updating)
			{
				ob_start();
				debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
				$CallStack = ob_get_clean();
				$body = "<pre>$bilgi\n$query\n$CallStack";
			}
			else
			{
				$body = "<pre>$query\nHata:$err<pre>";
				$CallStack = '';
			}
			$check_sum = md5($err);
			$sended_file = '';
			if(file_exists("prv/error.log"))
				$sended_file = file_get_contents("prv/error.log");
			file_put_contents('prv/last_error.html', $body);
			if(!isLocalhost() && !strpos($sended_file, $check_sum))
			{
				@$GLOBALS['DB_ERROR_LEVEL']++;
				// 2006 : MySql gone away
				if ($GLOBALS['DB_ERROR_LEVEL'] == 1 && self::ErrorNo() != 2006)
					$to = DgrPack::GetData('psorumlu_email');
				else
					$to = '';
				if (!$to)
					$to = 'destek@dgryazilim.net';
				$subject = "Hatalı sorgu oluştu.[ " . App::$URL . " ] [ proje=".App::$Kod." ]";
				if(isset($GLOBALS['version']))
					$subject .=" [ versiyon :".$GLOBALS['version']." ]";
				Mailer::Send($to, $subject, $body);
				$sended_file .= "\n$check_sum";
				file_put_contents("prv/error.log", $sended_file);
			}
			if(self::$Updating)
				throw new Exception("Hata:$err\n Sorgu: $query");
			if (! self::$Updating && !PageController::$_CurrentInstance)
				die("$err\r\n\r\n $query");
			if(class_exists('Transaction') && Transaction::IsActive())
				throw new Exception("Hata:$err\n Sorgu: $query");
		}
		else if (self::$SEND_HEAVY_QUERY && !isLocalhost() && $cost > 3*1000)
		{
			$query = StringLib::RowTrim($query);
			$sonSorguTmpYol = "prv/son_uzun_sorgu.txt";
			$fmtime = @filemtime($sonSorguTmpYol);
			if(time() - $fmtime > 180)
			{
				$simdi = Tarih::Simdi();
				file_put_contents($sonSorguTmpYol, $simdi);
				$body = "<pre>$bilgi\n$query\n$simdi\n" . date('d-m-Y H:i:s', $fmtime);
				$subject = "Çalışma süresi uzun sorgu [ " . App::$URL . " ][ proje=".App::$Kod." ]";
				if(isset($GLOBALS['version']))
					$subject .=" [ versiyon :".$GLOBALS['version']." ]";
				$to = 'destek@dgryazilim.net';
				Mailer::Send($to, $subject, $body);
			}
		}

		if($isDebug && Debug::$WriteMySqlLogs)
		{
			Debug::AddDbLog($query, $description, $cost);
			if($err){
				echo "<pre>Sorgu da hata var<hr>$bilgi\n$query\n<hr># Called Stack<hr>\n";
				echo $CallStack;
				echo "</pre>";
				if(! self::$Updating)
					die;
			}
		}
		return $rs;
	}

	/**
	 * SELECT sorgusu çalıştırma
	 */
	public static function Query($query, $description = '') {
		return DB::MySqlLog($query, 1, $description);
	}

	/**
	 * SELECT dışında diğer MySql sorgularını çalıştırma
	 */
	public static function Execute($query, $description = '') {
		$ret = DB::MySqlLog($query, 2, $description);
		if (is_array($ret) && count($ret) > 0)
			return $ret;
		if(mysqli_errno(DB::Get()->link) > 0)
			return mysqli_error(DB::Get()->link);
		else
			return 1;
	}

	/**
	 * SELECT dışında diğer MySql sorgularını çalıştırma
	 */
	public static function ExecuteMulti($queries, $description = '') {
		if(is_array($queries))
			$queries = implode (';', $queries);
		DB::MySqlLog($queries, 3, $description);
		$link = DB::Get()->link;
		while(mysqli_more_results($link))
			mysqli_next_result($link);

		if(mysqli_errno($link) > 0)
			return mysqli_error($link);
		else
			return 1;
	}

	public static function Delete($tableName, $where, $desc = ''){
		if($where){
			$trans = Transaction::GetCurrent();
			if($trans && $trans->Active)
				$trans->AddWillDeleted($tableName, $where);
			$sonuc = DB::Execute("DELETE FROM $tableName WHERE $where", $desc);
			if($sonuc === 1 && (self::LastAffectedRowCount() == 0) && $trans && $trans->Active)
				$trans->DeleteLast();
			return $sonuc;
		}
	}

	protected static function ShowDuplicateError($tableName, $modelName, $indexName, $duplicateVal)
	{
		$row = DB::FetchSingle("SHOW KEYS FROM `$tableName` WHERE Key_name = '$indexName'");
		$fieldText = $row['Column_name'];
		$objName = $tableName;
		if ($modelName)
		{
			$obj = new $modelName;
			/* @var $obj ModelBase */
			$map = $obj->GetModelMap();
			if ($map->TableAlias)
				$fieldText = $map->TableAlias . '.' . $fieldText;
			$f = ArrayLib::SearchObj($map->DbFields, array('FieldName' => $fieldText));
			if ($f)
				/* @var $f DbField */
				$fieldText = $f->DisplayName;
			$objName = $modelName;
		}
		$err = "değeri $duplicateVal olan başka bir $objName zaten bulunduğu için kayıt yapılamadı";
		if (App::IsUTF8())
			$err = Kodlama::UTF8($err);
		$err = $fieldText . " $err"; // $fieldText App::$Encoding ile aynı kodlamaya sahip!
		if(DB::$Updating)
			throw new Exception($err);
		if (! DB::$Updating && !PageController::$_CurrentInstance)
			die($err);
		if(class_exists('Transaction') && Transaction::IsActive())
			throw new Exception($err);
	}

	public static function Update($tableName, $sets, $where, $desc = '', $modelName = ''){
		if($where){
			$trans = Transaction::GetCurrent();
			if($trans)
				$trans->AddWillUpdated($tableName, $where);
			$sonuc = DB::Execute("UPDATE $tableName\n" .
				"SET $sets\n" .
				"WHERE $where", $desc);
			if (is_array($sonuc) && count($sonuc) > 0)
				self::ShowDuplicateError($tableName, $modelName, $sonuc[1], $sonuc[0]);
			if($sonuc === 1 && (self::LastAffectedRowCount() == 0) && $trans)
				$trans->DeleteLast();
			return $sonuc;
		}
	}

	public static function Insert($tableName, $sets, $idName = 'id', $desc = '', $modelName = ''){
		$sonuc = DB::Execute("INSERT INTO $tableName\n" .
			"SET $sets", $desc);
		if (is_array($sonuc) && count($sonuc) > 0)
			self::ShowDuplicateError($tableName, $modelName, $sonuc[1], $sonuc[0]);
		$trans = Transaction::GetCurrent();
		if($sonuc == 1 && $trans)
			$trans->AddInserted($tableName, $idName, mysqli_insert_id(DB::Get()->link));
		return $sonuc;
	}

	/**
	 * verilen SELECT sorgusu çalıştırılır ve field alanı(verilemeişse ilk alan)
	 * dizisi döndürür (tek boyutlu dizi)
	 * @return array
	 */
	public static function GetList($query, $field = 0, $description = '') {
		$rows = DB::Get()->checkCache($query);
		if ($rows === NULL)
		{
			$rs = DB::MySqlLog($query, 1, $description);
			$rows = array();
			while ($row = mysqli_fetch_array($rs))
				$rows[] = $row[$field];
			mysqli_free_result($rs);
			DB::Get()->saveCache($query, $rows);
		}
		return $rows;
	}

	/**
	 * SELECT sorgusu çalıştırarak sonucu bir dizi olarak verir.
	 * @example SELECT id from tablo => [1,2,3,4,5,...]
	 * @param string $query sonuç istenen SELECT sorgusu
	 * @param string $description sorgu ile ilgili tanım
	 * @return array
	 */
	public static function FetchList($query, $description = '') {
		$rs = DB::MySqlLog($query, 1, $description);
		$rows = array();
		while ($row = mysqli_fetch_array($rs, MYSQLI_NUM))
		{
			if(count($row) == 1)
				$rows[] = $row[0];
			else
				$rows[$row[0]] = $row[1];
		}

		mysqli_free_result($rs);
		return $rows;
	}

	protected static function FetchRows($query, $func, $user_func='', $description = '')
	{
		$rows = DB::Get()->checkCache($query);
		if ($rows === NULL)
		{
			$rs = DB::MySqlLog($query, 1, $description);
			$rows = array();
			while ($row = $func($rs))
				$rows[] = $row;
			mysqli_free_result($rs);
			DB::Get()->saveCache($query, $rows);
		}
		if ($user_func)
			for($i=0; $i < count($rows); $i++)
				$rows[$i] = CallUserFunc($user_func, $rows[$i]);
		return $rows;
	}

	/**
	 * SELECT sorgusu çalıştırarak sonucu bir dizi olarak verir
	 * @param string $query sonuç istenen SELECT sorgusu
	 * @param string $user_func eğer verilirse her bir satırın geçirileceği fonksiyon adı
	 * @param string $description sorgu ile ilgili tanım
	 * @return array of array
	 */
	public static function FetchArray($query, $user_func = '', $description = '') {
		return self::FetchRows($query, 'mysqli_fetch_array', $user_func, $description);
	}

	public static function WalkList($query, $user_func, $description = '') {
		$rs = DB::MySqlLog($query, 1, $description);
		while ($row = mysqli_fetch_array($rs))
			CallUserFunc($user_func, $row);
		mysqli_free_result($rs);
	}

	public static function WalkListAssoc($query, $user_func, $description = '') {
		$rs = DB::MySqlLog($query, 1, $description);
		while ($row = mysqli_fetch_assoc($rs))
			CallUserFunc($user_func, $row);
		mysqli_free_result($rs);
	}

	/**
	 * SELECT sorgusu çalıştırarak sonucu bir dizi olarak verir
	 * @param string $query sonuç istenen SELECT sorgusu
	 * @param string $user_func eğer verilirse her bir satırın geçirileceği fonksiyon adı
	 * @param string $description sorgu ile ilgili tanım
	 * @return array of array
	 */
	public static function FetchAssoc($query, $user_func = '', $description = '') {
		return self::FetchRows($query, 'mysqli_fetch_assoc', $user_func, $description);
	}

	/**
	 * SELECT sorgusu çalıştırarak sonucu bir <b>nesne dizisi</b> olarak verir
	 * @param string $query sonuç istenen SELECT sorgusu
	 * @param string $user_func eğer verilirse her bir satırın geçirileceği fonksiyon adı
	 * @param string $description sorgu ile ilgili tanım
	 * @return array of array
	 */
	public static function FetchObject($query, $user_func = '', $description = '') {
		return self::FetchRows($query, 'mysqli_fetch_object', $user_func, $description);
	}

	/**
	 * SELECT sorgusu çalıştırarak sonucun ilk kayıtını verir
	 * @param string $query sonuç istenen SELECT sorgusu
	 * @param string $description sorgu ile ilgili tanım
	 * @param string $user_func eğer verilirse sonucun geçirileceği fonksiyon adı
	 * @return array
	 */
	public static function FetchSingle($query, $user_func = '', $description = '', $fetchType = MYSQLI_BOTH) {
		$row = DB::Get()->checkCache($query);
		if ($row === NULL)
		{
			$rs = DB::MySqlLog($query, 1, $description);
			$row = mysqli_fetch_array($rs, $fetchType);
			mysqli_free_result($rs);
			DB::Get()->saveCache($query, $row);
		}
		if(! $row)
			return FALSE;
		else if($user_func == '')
			return $row;
		else
			return CallUserFunc($user_func, $row);
	}

	/**
	 * SELECT sorgusu çalıştırarak sonucun ilk kayıtın ilk alanını verir
	 * @param string $query sonuç istenen SELECT sorgusu
	 * @param string $description sorgu ile ilgili tanım
	 * @return int|string|float
	 */
	public static function FetchScalar($query, $description = '') {
		$row = DB::FetchSingle($query, '', $description);
		if($row)
			return $row[0];
		else
			return NULL;
	}

	/**
	 * verilen tablodaki ilgili kayıtların sayısı getirir
	 */
	public static function FetchCount($table, $where = '')
	{
		if ($where)
			$where = "WHERE $where";
		return DB::FetchScalar("SELECT COUNT(*) FROM $table $where");
	}

	/**
	 * verilen tablodaki belirtilen alana ait son kayıtı dönderir
	 */
	public static function FetchLast($table, $field, $where = '')
	{
		if ($where)
			$where = "WHERE $where";
		return DB::FetchScalar("SELECT $field FROM $table $where ORDER BY $field DESC LIMIT 1");
	}

	public static function LastAffectedRowCount()
	{
		return mysqli_affected_rows(DB::Get()->link);
	}

	public static function InsertedId()
	{
		return mysqli_insert_id(DB::Get()->link);
	}

	public static function EscapeString($str)
	{
		return mysqli_real_escape_string(DB::Get()->link, $str);
	}

	public static function ErrorNo()
	{
		return mysqli_errno(DB::Get()->link);
	}

	public static function Error()
	{
		return mysqli_error(DB::Get()->link);
	}

	public static function RsFetchArray($rs)
	{
		return mysqli_fetch_array($rs);
	}

	public static function RsFetchAssoc($rs)
	{
		return mysqli_fetch_assoc($rs);
	}

	public static function RsFree($rs)
	{
		return mysqli_free_result($rs);
	}

	public static function RsNumFields($rs)
	{
		return mysqli_num_fields($rs);
	}

	public static function RsFieldMeta($rs, $fieldnr)
	{
		return self::SetFieldType(mysqli_fetch_field_direct($rs, $fieldnr));
	}

	public static function ServerInfo()
	{
		return mysqli_get_server_info(DB::Get()->link);
	}

	public static function beginCache($name = '', $time = 60)
	{
		if (!$name){
			$acts = array();
			if (isset($_GET['act']))
				$acts[] = $_GET['act'];
			if (isset($_GET['act2']))
				$acts[] = $_GET['act2'];
			if (isset($_GET['act3']))
				$acts[] = $_GET['act3'];
			$acts[] = substr(md5(implode('_', $_GET)), -8);
			$name = implode('_', $acts);
		}
		$db = DB::Get();
		$db->CacheName = $name;
		$db->CacheTime = $time;
		$db->CacheFile = "prv/dbcache_$name.pdt";
	}

	public static function endCache()
	{
		$db = DB::Get();
		$db->CacheName = '';
		$db->CacheFile = '';
		$db->CacheTime = 0;
	}

	public function checkCache($query)
	{
		if (! $this->CacheName)
			return null;
		$fileAge = time() - @filemtime($this->CacheFile);
		if ($fileAge > $this->CacheTime)
			return null;
		$this->CacheDb = PhpFileReadArray($this->CacheFile);
		if (!$this->CacheDb)
			$this->CacheDb = array();
		$key = md5($query);
		if (isset($this->CacheDb[$key]))
			return $this->CacheDb[$key];
		return null;
	}

	public function saveCache($query, $result)
	{
		if (! $this->CacheName)
			return;
		$key = md5($query);
		$this->CacheDb[$key] = $result;
		PhpFileWriteArray($this->CacheFile, $this->CacheDb);
	}

	public function GetTables($dbName = '')
	{
		if($dbName)
			$this->DbName = $dbName;
		else
			$dbName = $this->DbName;
		if(! $dbName)
			return array();
		$this->Connect();

		// Aşağıdaki metot yukarıdakine göre biraz daha dar kapsamlı
		// ama en azından biraz daha hızlı çalışıyor gibi.
		$tables = $this->FetchArray("SHOW TABLES FROM $dbName ");
		$info = array();
		foreach($tables as $table)
			$info[] = array(
				'table' => $table[0],
				'field' => $this->GetPrimaryKeyName("$dbName.$table[0]"));
		return $info;
	}

	public function GetPrimaryKeyName($tableName)
	{
		$primary = $this->FetchArray("SHOW INDEX FROM $tableName WHERE Key_name = 'PRIMARY'");
		if (count($primary) == 1)
			return $primary[0]['Column_name'];
		return '';
	}

	/**
	 *
	 * @param type $query
	 * @return array
	 */
	public function GetFields($query, $useSpecialChar = false, $orgTable = '')
	{
		$query = self::QryAddLimit($query, 1);
		$rs = mysqli_query($this->link, $query);
		$fields = array();
		while($field = mysqli_fetch_field($rs))
			$fields[$field->name] = $field;
		if (! $orgTable && array_key_exists('id', $fields))
			$orgTable = $fields['id']->table;
		$i = 1;
		foreach($fields as $field)
		{
			$field->SiraNo = $i++;
			if ($useSpecialChar)
				$field->orgname = "`$field->orgname`";
			if ($field->table == '')
				$field->Alies = $field->name;
			else
				$field->Alies = "$field->table.$field->orgname";
			$field->Name = $field->name;
			$field->IsReal = $orgTable == $field->orgtable ? 1 : 0;
			$field->PhpName = StringLib::UcFirst($field->Name);
			$field->DispName= ucwords(str_replace("_", " ", $field->Name));
			self::SetFieldType($field);
			$fields[$field->name] = $field;
		}
		return $fields;
	}

	public function ConvertChartSet($charset = 'utf8', $collate = 'utf8_turkish_ci')
	{
		set_time_limit(0);
		$db = $this->DbName;
		$tables = DB::FetchList("SHOW TABLES");
		$queries = array(
			"ALTER DATABASE `$db` DEFAULT CHARSET $charset COLLATE $collate",
			"SET foreign_key_checks = 0");
		foreach($tables as $tb)
		{
			$columns = DB::FetchArray("SHOW FULL columns FROM $tb");
			$alter = array();
			$sql = "ALTER TABLE `$tb`\n";
			foreach($columns as $col)
			{
				$col = (object)$col;
//				if ($col->Field == 'id')
//					continue;
				if ($col->Collation && !preg_match('/utf8/i', $col->Collation)) // limit to charset
				{
					$default = is_null($col->Default) ? '' : 'DEFAULT \'' . DB::EscapeString($col->Default) . '\'';
					$comment = $col->Comment == '' ? '' : ' COMMENT \'' . DB::EscapeString($col->Comment) . '\'';
					$alter[] = "	CHANGE `$col->Field` `$col->Field` $col->Type"
						. " CHARSET $charset COLLATE $collate $default "
						. ($col->Null == 'YES' ? '' : 'NOT NULL') . " $comment";
				}
			}
			$alter[] = "DEFAULT CHARSET $charset COLLATE $collate ENGINE = MYISAM";
			$queries[] = $sql . implode(",\n", $alter);
		}
		$queries[] = "SET foreign_key_checks = 1";
		foreach($queries as $qry)
			DB::Execute($qry);
	}

	public static function SetFieldType($field)
	{
		// Alan türü ve default değer
		// NUMERIC (INT, FLOAT, v.b.)
		if (in_array($field->type, array(1, 2, 3, 4, 5, 8, 9, 16, 246)))
		{
			if ($field->type == 5 || $field->type == 246)
				$field->Type = 'float';
			else
				$field->Type = 'int';
			$field->Default = '0';
		}
		// DATE, DATETIME v.b.
		else if ($field->type == 10)
		{
			$field->Type = 'date';
			$field->Default = '"0000-00-00"';
		}
		else if ($field->type == 11)
		{
			$field->Type = 'time';
			$field->Default = '"00:00:00"';
		}
		else if ($field->type == 12)
		{
			$field->Type = 'datetime';
			$field->Default = '"0000-00-00 00:00:00"';
		}
		else
		{
			$field->Type = 'string';
			$field->Default = '""';
		}
		$field->type = $field->Type;
		return $field;
	}

	public static function TransBegin()
	{
		self::Get()->Execute('START TRANSACTION', 'Start Transaction');
	}

	public static function TransCommit()
	{
		self::Get()->Execute('COMMIT', 'Commit Transaction');
	}

	public static function TransRollback()
	{
		self::Get()->Execute('ROLLBACK', 'Rollback Transaction');
	}

	public static function QryAddLimit($query, $limit)
	{
		$query = preg_replace("/(\s*;\s*)$/", "", $query);
		$regexp = "/LIMIT\s+[0-9,]+$/i";
		$replace = "LIMIT $limit";
		if (preg_match($regexp, $query))
			$query = preg_replace($regexp, $replace, $query);
		else
			$query .= "\n$replace";
		return $query;
	}

	public static function CheckTable($tur)
	{
		$db = DB::Get();
		if ($tur == 0)
			return DB::FetchAssoc("
				SELECT table_name AS `Tablo`,
					round(((data_length + index_length) / 1024 / 1024), 2) `Durum`
				FROM information_schema.TABLES
				WHERE table_schema = '$db->DbName'
				ORDER BY (data_length + index_length) DESC");

		$tables = $db->FetchArray("SHOW TABLES FROM `$db->DbName`");
		$sonuc = array();
		foreach($tables as $t)
		{
			$opt = $tur == 3 ? 'REPAIR' : ($tur == 2 ? 'OPTIMIZE' : 'CHECK');
			$table = $db->FetchSingle("$opt TABLE `$t[0]`");
			$sonuc[] = array('Tablo' => $t[0], 'Durum' => $table['Msg_text']);
		}
		return $sonuc;
	}

}
