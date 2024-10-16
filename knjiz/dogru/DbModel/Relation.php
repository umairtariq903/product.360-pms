<?php

class Relation
{
	/**
	 * Şu an için geçerli olan ilişki türleri
	 */
	const ONE_TO_MANY	= 1;
	const ONE_TO_ONE	= 2;

	/**
	 * @var int
	 */
	public $Type = Relation::ONE_TO_MANY;

	/**
	 * @var int
	 */
	public $Behaviour = RelationBehaviour::DO_NOTHING;

	/**
	 * @var string
	 */
	public $AccessField = '';

	/**
	 * @var string
	 */
	public $ViewName = '';

	/**
	 * @var DbField[]
	 */
	public $ParentFields = NULL;

	/**
	 * @var DbField[]
	 */
	public $ChildFields = NULL;

	/**
	 * @var string[]
	 */
	public $Operators = NULL;

	/**
	 * İlişkideki karşı tarafın alan adı
	 * @var string
	 */
	public $ReverseField = "";

	/**
	 *
	 * @param DbField[] $parents
	 * @param DbField[] $children
	 * @param string[] $operators
	 * @param string $AccessField
	 * @param int $type
	 * @param int $behaviour
	 */
	public function __construct(
		$parents,
		$children,
		$operators,
		$AccessField = '',
		$RevAccessField = '',
		$type = Relation::ONE_TO_MANY,
		$behaviour = RelationBehaviour::DO_NOTHING)
	{
		$this->ParentFields = $parents;
		$this->ChildFields = $children;
		$this->Operators = $operators;
		$this->AccessField = $AccessField;
		$this->ReverseField = $RevAccessField;
		$this->Type = $type;
		$this->Behaviour = $behaviour;
	}

	/**
	 * @return ModelBase
	 */
	public function GetChildModel()
	{
		return $this->ChildFields[0]->GetModel();
	}

	/**
	 * @return ModelBase
	 */
	public function GetParentModel()
	{
		return $this->ParentFields[0]->GetModel();
	}

	public function GetPhpOperator($op)
	{
		switch ($op)
		{
			case '='	: return '==';
			case '<>'	: return '!=';
			default:
				return $op;
		}
	}

	/**
	 *
	 * @param ModelBase $obj
	 * @return string[]
	 */
	public function GetConditions($obj)
	{
		$obj = (object)$obj;
		$conditions = array();
		for($i=0; $i<count($this->ChildFields); $i++)
		{
			$pField = $this->ParentFields[$i];
			$cField = $this->ChildFields[$i];
			$op = $this->Operators[$i];
			if ($cField->IsExpression())
			{
				$cond = true;
				if (! $pField->IsExpression())
				{
					$phpOp = $this->GetPhpOperator($op);
					eval("\$cond = ($cField->FieldName $phpOp \$obj->$pField->FieldName);");
				}
				if ($cond)
					continue;
				else
					return null;
			}
			if ($pField->IsExpression())
				$pValue = $pField->FieldName;
			else
			{
				$pValue = $obj->{  $pField->FieldName  };
				// Alttaki kısım, 1-M ilişkilerde, kayıt yeniyse, tüm alt kayıtların
				// gelmesine sebep oluyordu. Bir süre sonra silinmeli #TODO
				//if ($pValue <= 0)
				//	continue;
			}
			$cName = $this->ChildFields[$i]->FieldName;
			if($pValue == '')
				$pValue = "'$pValue'";
			if (array_key_exists($cName, $conditions))
			{
				$cond = $conditions[$cName];
				/* @var $cond Condition */
				if (! is_a($cond, 'ConditionList'))
				{
					$list = new ConditionList();
					$list->Add($cName, $cond->cond, $cond->value);
					$cond = $list;
				}
				$cond->Add($cName, OPRT::GetFromOperator($op), $pValue);
				$conditions[$cName] = $cond;
			}
			else
				$conditions[$cName] = Condition::Field (OPRT::GetFromOperator($op), $pValue);
		}
		return $conditions;
	}

	public function GetSubQueryConditions()
	{
		$parentMap = $this->GetParentModel()->GetModelMap();
		$childMap = $this->GetChildModel()->GetModelMap();
		$conditions = array();
		for($i=0; $i<count($this->ChildFields); $i++)
		{
			$pField = $this->ParentFields[$i];
			$cField = $this->ChildFields[$i];
			$op = $this->Operators[$i];
			if ($cField->IsExpression())
				continue;
			if ($pField->IsExpression())
				$pValue = $pField->FieldName;
			else
				$pValue = $parentMap->DbFields[$pField->FieldName]->FieldName;

			$cName = $cField->FieldName;
			if (array_key_exists($cName, $conditions))
			{
				$cond = $conditions[$cName];
				/* @var $cond Condition */
				if (! is_a($cond, 'ConditionList'))
				{
					$list = new ConditionList();
					$list->Add($cName, $cond->cond, $cond->value);
					$cond = $list;
				}
				$cond->Add($cName, OPRT::GetFromOperator($op), $pValue);
				$conditions[$cName] = $cond;
			}
			else
				$conditions[$cName] = Condition::Field (OPRT::GetFromOperator($op), $pValue);
		}
		return $conditions;
	}
}