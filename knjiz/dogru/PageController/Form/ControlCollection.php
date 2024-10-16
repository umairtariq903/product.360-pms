<?php
class ControlCollection extends ArrayObject
{
	/**
	 * Sınıfın yorumlarında tanımlı Control listesini
	 * döndürür
	 * @param FormPageController $class
	 * @return ControlCollection
	 */
	public static function FromDeclarations(FormPageController $class)
	{
		$c = new ReflectionClass($class);
		$collection = new ControlCollection();
		foreach($c->getProperties() as $prop)
		{
			$refProp = new ReflectionProperty($prop->class, $prop->name);
			$comment = $refProp->getDocComment();
			$parts = array();
			$matches = array();
			if (preg_match_all('/@(\w+)\s+([^\s]*)\s+(.*)?\r?\n/m', $comment, $matches)){
			  for($i=0; $i<count($matches[1]); $i++)
				$parts[$matches[1][$i]] = trim($matches[2][$i]);
			}
			if (! array_key_exists('var', $parts))
				continue;
			$controlClass = $parts['var'];
			$controlId = $prop->name;
			if (class_exists($controlClass) &&
				array_key_exists('Control', class_parents($controlClass)))
			{
				$control = new $controlClass($controlId);
				$collection[$controlId] = $control;
			}
		}
		return $collection;
	}

	public function AddControl($Id, $Value = '', $Type = Control::TYPE_TEXT)
	{
		switch($Type)
		{
			case Control::TYPE_HTML:
				$obj = new HtmlControl($Id, $Value);
				break;
			case Control::TYPE_SELECT:
				$obj = new ListControl($Id, $Value);
				break;
			default:
				$obj = new InputControl($Id, $Value);
				break;
		}
		return $this[$Id] = $obj;
	}

	/**
	 *
	 * @param string $Id
	 * @return InputControl
	 */
	final function GetInputControl($Id)
	{
		return $this[$Id];
	}

	/**
	 *
	 * @param string $Id
	 * @return HtmlControl
	 */
	final function GetHtmlControl($Id)
	{
		return $this[$Id];
	}

	/**
	 * @param type $Id
	 * @return ListControl
	 */
	final function GetListControl($Id)
	{
		return $this[$Id];
	}

	public function __get($name)
	{
		if (isset($this[$name]))
			return $this[$name];
		else
			return null;
	}
}