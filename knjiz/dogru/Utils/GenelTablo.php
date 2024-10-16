<?php
class GenelTablo
{
	public static $IdName = 'id';
	public static $ValueName = 'value';

	public static function GetValue($id, $default = '')
	{
		$id = DB::EscapeString($id);
		$idName = static::$IdName;
		$valName = static::$ValueName;
		$sonuc = DB::FetchScalar("SELECT `$valName` FROM genel WHERE $idName = '$id' ", '', "Genel($id)");
		return $sonuc === NULL ? $default : $sonuc;
	}

	public static function SetValue($id, $value)
	{
		$old = self::GetValue($id, null);
		$id = DB::EscapeString($id);
		$value = DB::EscapeString($value);
		$idName = static::$IdName;
		$valName = static::$ValueName;
		if ($old === NULL)
			return DB::Insert('genel', "$idName='$id', `$valName`='$value'", $idName, "Genel($id)");
		else
			return DB::Update('genel', "`$valName`='$value'", "$idName='$id'", "Genel($id)");
	}
}