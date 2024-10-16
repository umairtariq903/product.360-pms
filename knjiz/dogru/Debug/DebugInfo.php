<?php
class DebugInfo
{
	public $Id = 0;
	public $Level = 0;
	public $Index = 0;
	public $Description = '';
	public $Cost = 0.0;
	public $TotalCost = 0.0;
	public $Time = 0.0;
	public $RowCount = 0;
	public $Query = '';
	public $Error = '';
	public $Memory = 0;
	public $MemoryPeak = 0;
	public $BackTree = array();
	public $LongDesc = '';

	public static function PrintR($array)
	{
		foreach($array as $key => $value)
			$array[$key] = json_decode($value);
		Kodlama::KarakterKodlamaDuzelt($array);
		ob_start();
		print_r($array);
		return ob_get_clean();
	}

}
?>
