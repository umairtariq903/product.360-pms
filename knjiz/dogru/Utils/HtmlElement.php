<?php
class HtmlElement
{
	public $tagName = '';
	public $shortTag = false;
	public $hasValue = true;

	protected $Attrs = array();
	protected $Style = array();
	protected $_html = null;

	/**
	 * @var HtmlElement[]
	 */
	protected $Children = array();
	/**
	 * @var HtmlElement[]
	 */
	protected $Siblings = array();

	public function __construct($tag = '', $short = false)
	{
		if($tag)
			$this->tagName = $tag;
		$this->shortTag = $short;
	}

	/**
	 * @param string $key
	 * @param string $val eğer verilirse setler
	 * @return FormItem|string attr değeri istendiği durumlarda string dönderir.
	 */
	public function attr()
	{
		$params = func_get_args();
		if(count($params) < 1)
			return $this;
		$key = $params[0];
		if(count($params) == 1)
			return IfNull($this->Attrs, $key);
		if ($key != 'value' || $this->hasValue)
		{
			if($params[1] === NULL)
				$this->Attrs[$key] = NULL;
			else
				$this->Attrs[$key] = "$params[1]";
		}
		$this->attrChanged($key);
		return $this;
	}

	protected function attrChanged($key)
	{
		return $key;
	}

	public function css()
	{
		$params = func_get_args();
		if(count($params) < 1)
			return $this;
		$key = $params[0];
		if(count($params) == 1)
			return $this->Style[$key];
		$this->Style[$key] = "$params[1]";
		return $this;
	}

	/**
	 * @param string $tagName
	 * @return HtmlElement[]
	 */
	public function find($tagName)
	{
		$all = array();
		foreach($this->Children as $child)
		{
			if (!is_object($child))
				continue;
			if (strtolower($tagName) == strtolower($child->tagName))
				$all[] = $child;
			if (is_object($child) && method_exists($child, 'find'))
				$all = array_merge($all, $child->find($tagName));
		}
		return $all;
	}

	/**
	 * @param string $class eklenecek sınıf adı
	 * @return FormItem
	 */
	public function addClass($class)
	{
		$class = $this->attr('class') . " $class ";
		return $this->attr('class', trim($class));
	}

	public function hasClass($class)
	{
		return preg_match("/\b$class\b/i", $this->attr('class'));
	}

	/**
	 * @param string $class silinecek sınıf adı
	 * @return FormItem
	 */
	public function removeClass($class)
	{
		$class = preg_replace("/\b$class\b/i", '', $this->attr('class'));
		return $this->attr('class', $class);
	}

	public function innerHTML()
	{
		$params = func_get_args();
		if(count($params) == 1)
			$this->Children = array($params[0]);
		else
		{
			$html = '';
			foreach($this->Children as $child)
				$html .= "$child";
			return $html;
		}
		return $this;
	}

	public function addChild($child)
	{
		$this->Children[] = $child;
		return $this;
	}

	public function removeChildAt($index)
	{
		unset($this->Children[$index]);
		return $this;
	}

	public function removeChild($item)
	{
		$index = array_search($item, $this->Children);
		if ($index >=0 )
			$this->removeChildAt($index);
		return $this;
	}

	public function removeChildren()
	{
		$this->Children = array();
		return $this;
	}

	public function addSibling()
	{
		$params = func_get_args();
		$this->Siblings = array_merge($this->Siblings, $params);
		return $this;
	}

	public function appendTo(HtmlElement $parent)
	{
		$parent->addChild($this);
		return $this;
	}

	public function __toString()
	{
		return $this->html();
	}

	/**
	 * @return HtmlElement|string
	 */
	public function html()
	{
		if($this->_html !== NULL)
			return $this->_html;
		$params = func_get_args();
		if(count($params) == 1)
		{
			$this->_html = $params[0];
			return $this;
		}

		if (isset($this->Column))
		{
			if ($this->Column->Readonly)
				if($this->DisplayText)
					return $this->DisplayText;
				else
					return $this->val();
			if ($this->Column->Disabled)
				$this->Attrs['disabled'] = 'disabled';
		}
		$str = "<$this->tagName ";
		$style = '';
		if (isset($this->Attrs['disabled']))
			$this->Style['background'] = '#dddddd';
		foreach($this->Style as $name => $value)
			$style .= "$name: $value;";
		if($style)
			$this->Attrs['style'] = $style;
		foreach($this->Attrs as $name => $value)
			if($value === NULL)
				$str .= "$name ";
			else
				$str .= $name . "='" . addcslashes($value, "'"). "' ";
		if (!$this->shortTag)
			$str .= '>'. $this->innerHTML() ."</$this->tagName>";
		else
			$str .= '/>';
		foreach($this->Siblings as $sibling)
			$str .= "$sibling";
		return $str;
	}

	public function hide()
	{
		$this->css('display', 'none');
	}

	/**
	 * @return static
	 */
	public static function InitNew($id = '')
	{
		$obj = new static();
		if($id)
			$obj->attr('id', $id);
		return $obj;
	}

	public static function Get($tag, $short = false)
	{
		return new HtmlElement($tag, $short);
	}

	public static function GetA($link = '', $html = '', $target = '_blank')
	{
		if(!$html)
			$html = $link;
		$a = self::Get('a');
		if($link)
			$a->attr('href', $link);
		else
			$a->attr('href', 'javascript:void(0)');
		if($html)
			$a->innerHTML($html);
		if($target)
			$a->attr('target', $target);
		return $a;
	}

	public static function GetSpan($html = '', $class = '')
	{
		$s = self::Get('span');
		if($class)
			$s->addClass ($class);
		if($html)
			$s->innerHTML($html);
		return $s;
	}

	public static function GetDIV($html = '', $class = '')
	{
		$s = self::Get('div');
		if($class)
			$s->addClass ($class);
		if($html)
			$s->innerHTML($html);
		return $s;
	}

	public static function GetUL($html = '', $class = '')
	{
		$s = self::Get('ul');
		if($class)
			$s->addClass($class);
		if($html)
			$s->innerHTML($html);
		return $s;
	}

	public static function GetLI($html = '', $class = '')
	{
		$s = self::Get('li');
		if($class)
			$s->addClass($class);
		if($html)
			$s->innerHTML($html);
		return $s;
	}

	/**
	 * @return FormItem
	 */
	public static function GetImg($src, $class = '')
	{
		$img = self::Get('img', TRUE)->attr('src', $src);
		if($class)
			$img->addClass($class);
		return $img;
	}

	/**
	 * @return FormItem
	 */
	public static function GetCheckbox($id, $class = '')
	{
		$cbx = self::Get('input', TRUE)
			->attr('type', 'checkbox')
			->attr('id', $id);
		if($class)
			$cbx->addClass($class);
		return $cbx;
	}

	public static function GetButton($text, $clickFunc = '', $class = '')
	{
		$btn = self::Get('button')->innerHTML($text);
		if ($clickFunc)
		{
			if (!preg_match("/\(.*\)$/", $clickFunc))
				$clickFunc .= "(this, event)";
			$btn->attr('onclick', $clickFunc);
		}
		if($class)
			$btn->addClass($class);
		return $btn;
	}

	public static function GetTable($rows, $classes = array())
	{
		$table = HtmlElement::Get('TABLE')->addClass('table table-striped table-bordered');
		if (count($rows) > 0)
		{
			$head = HtmlElement::Get('THEAD')->appendTo($table);
			$tr = HtmlElement::Get('TR')->appendTo($head);
			$i = 0;
			foreach(array_keys($rows[0]) as $key)
			{
				$td = HtmlElement::Get('TH')->innerHTML($key)->appendTo($tr);
				if (isset($classes[$i]))
					$td->addClass($classes[$i]);
				$i++;
			}
		}
		$body = HtmlElement::Get('TBODY')->appendTo($table);
		foreach($rows as $row)
		{
			$tr = HtmlElement::Get('TR')->appendTo($body);
			$i = 0;
			foreach($row as $cell)
			{
				$td = HtmlElement::Get('TD')->innerHTML($cell)->appendTo($tr);
				if (isset($classes[$i]))
					$td->addClass($classes[$i]);
				$i++;
			}
		}
		return $table;
	}
}