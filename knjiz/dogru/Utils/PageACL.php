<?php
class AccessCtrl
{
	public $Name = '';
	public $Visible = false;
	public $Enable = false;

	public function __construct($name)
	{
		$this->Name = $name;
	}

	public function SetFromStr($str)
	{
		$parts = explode(":", $str);
		if(count($parts) != 2)
			return false;
		$this->Visible = $parts[0];
		$this->Enable = $parts[1];
		return true;
	}

	public function GetAsStr()
	{
		return ($this->Visible ? '1' : '0') . ':' . ($this->Enable ? '1' : '0');
	}
}

class AccessCtrlNode extends AccessCtrl
{
	public $Level = 0;
	public $Act = '';
	public $Parent = null;
	public $Authorization = true;
	/**
	 * @var AccessCtrlNode[]
	 */
	public $Children = array();

	/**
	 * @var AccessCtrl[]
	 */
	public $Actions = array();

	public function __construct($name = '', $children = array(), $actions = array())
	{
		parent::__construct($name);
		if($children)
			$this->Children = $children;
		if($actions)
			$this->Actions = $actions;
	}

	public function SetFromStr($str, $recursion = true)
	{
		parent::SetFromStr($str);
		if($recursion)
		{
			foreach($this->Children as $child)
				$child->SetFromStr($str, $recursion);
			foreach($this->Actions as $act)
				$act->SetFromStr($str);
		}
	}

	public function GetHref()
	{
		$href = 'act';
		if ($this->Level > 1)
			$href .= $this->Level;
		$href .= "=$this->Act";
		if ($this->Parent && $this->Level > 1)
			return $this->Parent->GetHref() . "&$href";
		else
			return "?$href";
	}

	public function SetLevels()
	{
		foreach($this->Children as $key => $c)
		{
			$c->Act = $key;
			$c->Level = $this->Level + 1;
			$c->Parent = $this;
			$c->SetLevels();
		}
	}

	public function GetHtml($onlyChilds = false)
	{
		$chlds = array();
		foreach($this->Children as $c)
			if($c->Visible)
				$chlds[] = $c->GetHtml();
		$href = $this->GetHref();
		$a = "<a href='$href'>$this->Name</a>";
		$glue = $onlyChilds ? '<span class="menu-separator"></span>' : '';
		$chlds = implode($glue, $chlds);
		if($onlyChilds)
			return $chlds;
		else
		{
			if($chlds)
				$chlds = "<ul>$chlds</ul>";
			return "<li>$a $chlds</li>";
		}
	}

	public function GetArray($withThis = false, $level = 0)
	{
		$this->Lvl = $level;
		$array = array();
		if($withThis)
			$array[] = $this;
		foreach($this->Actions as $key => $act)
		{
			$act->Lvl = $level + 1;
			$act->Url = $this->Url . "." . $key;
			$array[] = $act;
		}
		foreach($this->Children as $key => $child)
		{
			if (!$child->Authorization)
				continue;
			$child->Lvl = $level + 1;
			$child->Url = @$this->Url . "." . $key;
			$ca = $child->GetArray(true, $level + 1);
			$array = array_merge($array, $ca);
		}
		return $array;
	}

	public function ToString()
	{
		$dizi = array();
		$this->ToStringArray($dizi);
		return implode("\n", $dizi);
	}

	public function FromString($yetkiler)
	{
		$yetkiler = explode("\n", $yetkiler);
		foreach($yetkiler as $yetki)
		{
			$parts = explode(":", $yetki, 2);
			if(count($parts) != 2)
				continue;
			$acts = explode(".", $parts[0]);
			$Ctrl = $this->xpath($acts);
			if($Ctrl)
				$Ctrl->SetFromStr($parts[1]);
		}
	}

	/**
	 * @return AccessCtrl
	 */
	public function xpath($acts, $nearest = false)
	{
		if(count($acts) == 0)
			return $this;
		$act = array_shift($acts);
		$ctrl = @$this->Children[$act];
		if($ctrl && $ctrl->Authorization)
			return $ctrl->xpath($acts, $nearest);
		$ctrl = @$this->Actions[$act];
		if($nearest && ! $ctrl)
			return $this;
		return $ctrl;
	}

	private function ToStringArray(&$dizi, $pre = '')
	{
		if($pre)
			$pre = $pre . ".";
		foreach($this->Children as $key => $child)
		{
			$dizi[] = $pre . "$key:" . $child->GetAsStr();
			$child->ToStringArray($dizi, $pre . $key);
		}
		foreach($this->Actions as $act)
			$dizi[] = $pre . "$act->Name" . $act->GetAsStr();
	}
}

class PageACL
{
	/**
	 * @var PageACL
	 */
	public static $Instance = null;
	/**
	 * @var AccessCtrlNode
	 */
	public $ACL = null;

	public function __construct($Children = array())
	{
		$this->ACL = new AccessCtrlNode('', $Children);
		$this->ACL->SetLevels();
	}

	public function Gorme($params)
	{
		$node = $this->GetNode($params);
		if($node)
			return $node->Visible;
		return true;
	}

	public function Islem($params)
	{
		$node = $this->GetNode($params);
		if($node)
			return $node->Visible && $node->Enable;
		return true;
	}

	public static function InitNew()
	{
		return self::$Instance = new static();
	}

	/**
	 * @return static
	 */
	public static function Get()
	{
		if (self::$Instance)
			return self::$Instance;
		return self::InitNew();
	}

	/**
	 *
	 * @param string $path
	 * @example admin.proje nokta ile ayrılmış olarak children yapısı
	 * @return AccessCtrlNode
	 */
	public function GetNode($path)
	{
		$acl = $this->ACL;
		if(is_string($path))
			$acl = $acl->xpath(explode('.', $path), TRUE);
		else
		{
			$i = 2;
			$act = 'act';
			while(($newAcl = @$acl->Children[$path[$act]]) && $newAcl->Authorization)
			{
				$act = 'act' . $i++;
				$acl = $newAcl;
			}
			$newAcl = @$acl->Actions[$path['islem']];
			if($newAcl)
				$acl = $newAcl;
		}
		// Kök dizinde olması, yetkilendirme ağacında bulunamaması demektir.
		// örnk. guest dizini
		return $acl != $this->ACL ? $acl : NULL;
	}

	/**
	 * Yetki kontrolünde üst yöneticiler için gerekli sorgulamaların yapıldığı fonksiyondur.
	 * @param type $methodName
	 * @param PageController $page
	 */
	public static function CheckIslemYetki($methodNames, PageController $page)
	{
		$calledMethod = strtolower($_REQUEST['cpaint_function']);
		$authorizedAjaxMethods = $page->GetAuthorizedAjaxMethods();
		$NoAuthorizedAjaxMethods = array_map('strtolower', $page->GetNoAuthorizedAjaxMethods());
		if(is_string($authorizedAjaxMethods))
			$authorizedAjaxMethods = $methodNames;
		$authorizedMethods = array_map('strtolower', $authorizedAjaxMethods);
		$detayParams = array();
		$regs = array();
		for($i=0;$i<count($authorizedMethods); $i++)
			if (preg_match("/(.*)\:(.*)/i", $authorizedMethods[$i], $regs))
			{
				$authorizedMethods[$i] = $regs[2];
				$detayParams[$regs[2]] = $regs[1];
			}
		if (in_array($calledMethod, $authorizedMethods) && ! in_array($calledMethod, $NoAuthorizedAjaxMethods))
		{
			$route = $_GET;
			$route['islem'] = (isset($detayParams[$calledMethod]) ? $detayParams[$calledMethod] : '');
			if (!PageACL::Get()->Islem($route))
				die("Bu işlemi yapmak için yetkiniz bulunmamaktadır");
		}
	}

}