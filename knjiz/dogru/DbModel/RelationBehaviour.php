<?php

class RelationBehaviour
{
	const DO_NOTHING		= 0;
	const CASCADE_DELETE	= 1;
	const CASCADE_UPDATE	= 2;
	const CASCADE_ALL		= 3;
	const PREVENT_DELETION	= 4;

	public $Type = NULL;

	public function __construct($Type = RelationBehaviour::DO_NOTHING)
	{
		$this->Type = $Type;
	}

}
?>
