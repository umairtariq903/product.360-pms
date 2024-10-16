<?php
class PagesFile
{
	public $List = array();
	protected $FileName = '';

	public function __construct()
	{
		if(file_exists($this->FileName))
		{
			$xml = simplexml_load_file($this->FileName);
			XML_Serialize::Unserialize($this, $xml);
		}
	}

	/**
	 * @param PageInfo $page
	 */
	public function Change($page, $oldName)
	{
		file_put_contents($this->FileName, XML_Serialize::SerializeObj($this));
	}

	public static function Get()
	{
		return new static();
	}
}

class MenuPages extends PagesFile
{
	public function __construct()
	{
		$this->FileName = App::$Klasor . 'ui/pages/Menu.xml';
		parent::__construct();
	}

	/**
	 *
	 * @param PageInfo $currPage
	 */
	private function GetChildren($currPage)
	{
		$children = array();
		foreach($currPage->Children as $childPage)
		{
			$nesne = new stdClass();
			$nesne->MenuName = $childPage->MenuName;
			$nesne->Acts = $childPage->GetActs();
			$nesne->Children = $this->GetChildren($childPage);
			$children[] = $nesne;
		}
		return $children;
	}

	/**
	 * @param PageInfo $page
	 */
	public function Change($page, $oldName = '')
	{
		$pageCtrl = PageController::$_CurrentInstance;
		/* @var $pageCtrl DevPageTree */
		$allPages = $pageCtrl->GetAllPages();
		$this->List = $this->GetChildren($allPages);
		file_put_contents($this->FileName, XML_Serialize::SerializeObj($this));
	}
}

class JsPages extends PagesFile
{
	public function __construct()
	{
		$this->FileName = App::$Klasor . '_dev/Pages.xml';
		parent::__construct();
	}

	/**
	 * @param PageInfo $page
	 */
	public function Change($page, $oldName)
	{
		if(isset($this->List[$oldName]))
			unset($this->List[$oldName]);
		if($page)
		{
			$nesne = new stdClass();
			$nesne->N = $page->JsPageName;
			$nesne->U = implode('.', $page->GetActs());
			$nesne->T = $page->LinkType;
			$nesne->W = $page->PageWidth;
			$nesne->H = $page->PageHeight;
			$this->List[$page->JsPageName] = Kodlama::JSON($nesne);
			ksort($this->List);
		}
		file_put_contents($this->FileName, XML_Serialize::SerializeObj($this));
		$this->CreateFile();
	}

	public function CreateFile()
	{
		$pages = array();
		foreach($this->List as $name => $value)
			$pages[] = "PAGE_$name = '$value';";
		file_put_contents(App::$Klasor . '/js/Pages.js', implode("\n", $pages));
	}
}
