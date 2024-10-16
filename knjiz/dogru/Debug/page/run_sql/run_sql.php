<?php
class DebugRunSqlPage extends PageController
{
	public function Index()
	{
		$q = trim($_GET['sql']);
		if(! preg_match('/^select/i', $q))
			return $this->ShowError('SADECE "SELECT" sorguları çalıştırılabilinir');
		$time = microtime(true);
		$rs = DB::Query($q);
		$this->Maliyet = round(1000 * (microtime(TRUE) - $time), 2) . ' ms';
		for($i = 0; $i < DB::RsNumFields($rs); $i++) {
			$field = DB::RsFieldMeta($rs, $i);
			$fields[$field->name] = $field;
		};
		$this->Fields = $fields;

		$rows = array();
		while($row = DB::RsFetchArray($rs))
		{
			foreach($fields as $name => $f)
			{
				if ($f->type == 'real')
					$row[$name] = floatval ($row[$name]);
				else if ($f->type == 'int')
					$row[$name] = intval($row[$name]);
			}
			$rows[] = $row;
		}
		$this->Rows = $rows;
	}
}