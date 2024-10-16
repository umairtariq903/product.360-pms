<?php

namespace DogRu;

use \Relation AS Rltn;
use RelationBehaviour AS RltnBhv;
/**
 * VT da olan ilişkileri kontrol eder
 *
 */
class AppCheckDbRelations
{
	public $ForeignKeys = array();

	/**
	 * @return AppCheckDbRelations
	 */
	public static function Get()
	{
		static $ints = null;
		if ($ints)
			return $ints;
		$ints = new AppCheckDbRelations();
		foreach($ints->getModelBaseInsts() as $obj)
			foreach($obj->GetModelMap()->Relationships as $rel)
				$ints->addRelationQry($rel);
		return $ints;
	}

	private function addRelationQry(\Relation $rel, $force = false)
	{
		if (!$force && ($rel->Type != Rltn::ONE_TO_MANY ||
			($rel->Behaviour != RltnBhv::CASCADE_DELETE && $rel->Behaviour != RltnBhv::PREVENT_DELETION)) )
			return;
		$pModel = $rel->GetParentModel();
		$cModel = $rel->GetChildModel();
		if (!$cModel || !$pModel)
			return;
		$pMap = $pModel->GetModelMap();
		$cMap = $cModel->GetModelMap();
		$tbl = $pMap->Name;
		$child = $cMap->Name;
		$conds = array();
		$outConds = array();
		$pAlias = '';
		$cAlias = '';
		$lastChildField = null;
		for($i = 0; $i < count($rel->Operators); $i++)
		{
			$pFld = $rel->ParentFields[$i]->FieldName;
			$inCond = isset($pMap->DbFields[$pFld]);
			if ($inCond)
				$pFld = $pMap->DbFields[$pFld]->FieldName;
			$parts = explode('.', $pFld);
			if (count($parts) > 1)
				$pAlias = $parts[0];
			$cFld = $rel->ChildFields[$i]->FieldName;
			if (isset($cMap->DbFields[$cFld]))
				$lastChildField = $cFld = $cMap->DbFields[$cFld]->FieldName;
			$parts = explode('.', $cFld);
			if (count($parts) > 1)
			{
				$cAlias = $parts[0];
				$lastChildField = str_replace("$cAlias.", '', $lastChildField);
			}
			// parent ve child alias ları aynı olabilir diye değiştiriyoruz
			$pFld = preg_replace("/$pAlias\./", 'm.', $pFld);
			$cFld = preg_replace("/$cAlias\./", 'd.', $cFld);
			$pAlias = 'm';
			$cAlias = 'd';

			$cnd = "$cFld " . $rel->Operators[$i] . " $pFld";
			if ($inCond)
				$conds[] = $cnd;
			else
				$outConds[] = $cnd;
		}
		$conds = implode(' AND ', $conds);
		$outConds = implode(' AND ', $outConds);
		$tbl .= " AS $pAlias";
		$child .= " AS $cAlias";
		if ($outConds)
			$outConds .= ' AND ';
		$notExists = "NOT EXISTS(SELECT * FROM $tbl WHERE $conds)";
		$qry = "FROM $child WHERE $outConds\n $notExists";
		$this->ForeignKeys[] = $fKey = (object) array(
			'master' => $pMap->Name,
			'detail' => $cMap->Name,
			'mClass' => get_class($pModel),
			'field'  => $rel->AccessField,
			'cond'   => "$outConds$conds",
			'selQry' => "SELECT * $qry;",
			'cntQry' => "SELECT COUNT(*) $qry;",
			'tmpQry' => 'DROP TABLE IF EXISTS temp_ids;'
						. "\nCREATE TEMPORARY TABLE temp_ids \n SELECT id $qry;"
			);
		$clearAlias = function($exp){
			return str_replace(array(' m.', ' d.', ' AS m ', ' AS d '), ' ', $exp);
		};
		$delQueries = $updQueries = array();
		if ($pMap->Name == $cMap->Name)
		{
			$delQueries[] = $fKey->tmpQry;
			$delQueries[] = "DELETE FROM $cMap->Name WHERE id IN (SELECT id FROM temp_ids);";

			$updQueries[] = $fKey->tmpQry;
			$updQueries[] = "UPDATE $cMap->Name SET $lastChildField = 0\n"
				."WHERE id IN (SELECT id FROM temp_ids);";
		}
		else
		{
			$qry = str_replace(' d.', " $cMap->Name.", $qry);
			$qry = $clearAlias($qry);
			$delQueries[] = "DELETE $qry;";

			$outConds = $clearAlias(" $outConds");
			$notExists = str_replace(' d.', " $cMap->Name.", $notExists);
			$notExists = $clearAlias($notExists);
			$updQueries[] = "UPDATE $cMap->Name SET $lastChildField = 0\n"
				."WHERE $outConds\n $notExists;";
		}
		$fKey->delQry = implode("\n", $delQueries);
		$fKey->updQry = implode("\n", $updQueries);
	}

	/**
	 * @return \ModelBase[]
	 */
	private function getModelBaseInsts()
	{
		$list = array();
		$classes = \ClassesFile::Get()->ClassesPath;
		foreach(array_keys($classes) as $cls)
			if (substr($cls, -8) == 'ModelMap')
			{
				$model = substr($cls, 0, -8);
				$base = $model . 'Base';
				if (class_exists($model) && is_subclass_of($model, 'ModelBase')
					&& class_exists($base))
					$list[] = new $model;
			}
		return $list;
	}

	public function getCountQueries()
	{
		return \ArrayLib::ObjPropertyList($this->ForeignKeys, 'cntQry');
	}

	public function getListQueries()
	{
		return \ArrayLib::ObjPropertyList($this->ForeignKeys, 'selQry');
	}

	public function getDeleteQueries()
	{
		return \ArrayLib::ObjPropertyList($this->ForeignKeys, 'delQry');
	}

	public function getLeaps($all = true)
	{
		$list = array();
		foreach($this->ForeignKeys as $key)
		{
			$cnt = \DB::FetchScalar($key->cntQry);
			$key->cnt = $cnt;
			if ($all || $cnt > 0)
				$list[] = $key;
		}
		return $list;
	}

	/**
	 * @return Rltn
	 */
	public function getRelation($parentClass, $childName)
	{
		$obj = new $parentClass;
		/*@var $obj \ModelBase */
		foreach($obj->GetModelMap()->Relationships as $rel)
			if ($rel->AccessField == $childName)
				return $rel;
		return null;
	}

	public function addRelation($parentClass, $childName)
	{
		$rel = $this->getRelation($parentClass, $childName);
		$this->addRelationQry($rel, true);
	}

}
