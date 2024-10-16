<?php

class PageInfo
{
	const TYPE_UNKNOWN	= -1;
	const TYPE_EMPTY	= 0;
	const TYPE_CUSTOM	= 1;
	const TYPE_LIST		= 2;
	const TYPE_FORM		= 3;
	const TYPE_LINK		= 4;
	const TYPE_VIEW		= 5;

	public static $Types = array(
		self::TYPE_UNKNOWN	=> "Bilinmeyen",
		self::TYPE_EMPTY	=> "Temel klasör (PageController yok)",
		self::TYPE_CUSTOM	=> "Özel PageController",
		self::TYPE_LIST		=> "Listeleme amaçlı PageController",
		self::TYPE_FORM		=> "Kayıt düzenleme amaçlı PageController",
		self::TYPE_LINK		=> "Bir başka sayfaya yönlendirilmiş.",
		self::TYPE_VIEW		=> "Kayit görüntüleme amaçlı PageControler"
	);

	public $Version = 0;
	public $Name = '';
	public $MenuName = '';
	public $FullPath = '';
	public $PageTitle = '';
	public $Type = -1;
	public $ModelBase = '';
	public $Columns = array();
	public $PhpLibs = '';
	public $JsLibs = '';
	public $LinkType = 0;
	public $PageWidth = 0;
	public $PageHeight = 0;
	public $JsPageName = '';
	public $JsDetailPage = '';
	public $DbListProps = array();
	public $DbFormProps = array();
	public $RowAttributes = '';
	/**
	 * @var BtnIslem[]
	 */
	public $DbListActions = array();

	public static function GetArrayProp($type)
	{
		$regs = array();
		if (is_string($type) && preg_match("/^\#\[(.*)\]/", $type, $regs))
			return explode(",", $regs[1]);
		return $type;
	}

	/**
	 *
	 * @param type $Name
	 * @param type $FullPath
	 * @param type $Children
	 * @return PageTreeNode
	 */
	public static function InitNew($Name, $FullPath)
	{
		$fileName = "$FullPath/$Name.xml";
		$node = new PageInfo();
		if (isFile($fileName))
		{
			$xml = simplexml_load_file($fileName);
			XML_Serialize::Unserialize ($node, $xml);
			if ($node->Version == 0)
				foreach($node->Columns as &$col)
				{
					$parts = explode(';', $col);
					$col = new stdClass();
					foreach($parts as $p)
					{
						if (!$p)
							continue;
						$props = explode(':', $p);
						if (count($props) == 1 && !isset($col->Name))
							$col->Name = $props[0];
						else if (count($props) == 1)
							$col->{$props[0]} = 1;
						else
							$col->{$props[0]} = self::GetArrayProp($props[1]);
					}
				}
		}
		$node->Name = $Name;
		$node->FullPath = RelativePath($FullPath);
		if(! $node->JsPageName)
		{
			$jname = '';
			foreach($node->GetActs() as $d)
				$jname .= StringLib::UcFirst($d);
			$node->JsPageName = $jname;
		}
		$node->GetType();
		return $node;
	}

	public function GetActs()
	{
		$dirs = explode('/', $this->FullPath);
		$acts = array();
		$pdir = '';
		foreach($dirs as $d)
		{
			if($d && ($acts || $pdir == 'pages'))
				$acts[] = $d;
			$pdir = $d;
		}
		return $acts;
	}

	public function GetType()
	{
		if ($this->Type >= 0)
			return $this->Type;
		$fileName = "$this->FullPath/$this->Name";
		if (!isFile("$fileName.php"))
			return $this->Type = self::TYPE_EMPTY;
		return $this->Type = self::TYPE_UNKNOWN;
	}

	public function GetJsFunctions()
	{
		$list = array();
		$file = "$this->FullPath/$this->Name.js";
		if(! isFile($file))
			return $list;
		$content = explode("\n", file_get_contents($file));
		$matches = array();
		$i=1;
		foreach($content as $line)
		{
			if(preg_match('/^\s*function\s+([a-z0-9_]+)/i', $line, $matches))
				$list[$i] = $matches[1];
			$i++;
		}
		return $list;
	}

	public function GetAppPath()
	{
		return FullPath($this->FullPath);
	}

	public function Save()
	{
		$fileName = "$this->FullPath/$this->Name.xml";
		$xml = XML_Serialize::SerializeObj($this);
		return file_put_contents($fileName, $xml) > 0 ? 1: 0;
	}
}
