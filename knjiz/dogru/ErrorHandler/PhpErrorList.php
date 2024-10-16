<?php
class PhpErrorList
{
	const FILENAME = 'prv/php_errors.txt';
	public $MaxLog = 100;
	public $List = array();

	public function Add($err)
	{
		if(count($this->List) > 0)
			$err->Id = $this->List[0]->Id + 1;
		else
			$err->Id = 1;
		array_unshift($this->List, $err);
		while(count($this->List) > $this->MaxLog)
			array_pop($this->List);
		file_put_contents(self::FILENAME, serialize($this));
	}

	public static function Get()
	{
		static $inst = null;
		if(! $inst)
			if(file_exists(self::FILENAME))
				$inst = mb_unserialize(file_get_contents(self::FILENAME));
			if (!is_a($inst, 'PhpErrorList'))
				$inst = new PhpErrorList();
		return $inst;
	}

	public static function CallStackArray($ignore = 1)
	{
		$param = false;
		if (defined('DEBUG_BACKTRACE_IGNORE_ARGS'))
			$param = DEBUG_BACKTRACE_IGNORE_ARGS;
		$call = debug_backtrace($param);
		for($i = 0; $i < $ignore; $i++)
			array_shift($call);
		$sonuc = array();
		foreach($call as $stack)
		{
			if(! isset($stack['file']))
				continue;
			$stack['file'] = str_replace('\\', '/', $stack['file']);
			if(function_exists('RelativePath'))
				$stack['file'] = RelativePath($stack['file']);
			$s['dosya'] = $stack['file'] . ":" . $stack['line'];
			$s['func'] = @$stack['class']. @$stack['type'] . $stack['function'];
			$sonuc[] = $s;
		}
		return $sonuc;
	}
}
