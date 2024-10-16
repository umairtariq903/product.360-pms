<?php
require_once 'PageRouter.php';
require_once 'IPageController.php';
require_once 'PageInfoFile.php';

class PageController implements IPageController
{
	public static $CharSet = 'utf-8';

	public $ConvertQuots = true;

	public static $UsePageControllerPath = true;
	/**
	 * @var string Sayfanın başlığı
	 */
	public $Title = NULL;

	public $pageIcon = '';

	/**
	 * Sayfada gösterilecek/edit edilecek asıl veri
	 * @var ModelBase|Anything
	 */
	public $Data = NULL;

	/**
	 * Sayfaya eklenecek JS dosyalarının URL'leri
	 * @var string[]
	 */
	private $JsList = array();
	/**
	 *
	 * @var Sayfaya eklenecek CSS dosyalarının URL'leri
	 */
	private $CssList= array();
	/**
	 *
	 * @var array[]
	 */
	private $JsVars = array();
	/**
	 * @var string[]
	 */
	private $JsOnloadFunctions = array();

	/** @var PageController */
	public static $_CurrentInstance = NULL;

	/**
	 * @var string Bu sayfaya ait View/Template dosyasının tam yolu
	 */
	public $Template = null;

	/**
	 *
	 * @var string Template'in aranacağı kök klasör
	 */
	public $PageControllerUrl = '';

	/**
	 *
	 * @var string[] Bu controller'a ulaşırken kullanılan action elemanları
	 */
	private $_ActionRoute = array();

	/**
	 * Ajax işlemleri sırasında, yetki kontrolünü
	 * yapacak metodun/fonksiyonun adı.
	 * Verilecek fonksiyon iki parametre almalıdır:
	 *  * Method Names (array)
	 *  * PageController
	 *
	 * @var string|array
	 */
	public static $AjaxYetkiKontrolFonk = array('PageACL', 'CheckIslemYetki');

	/**
	 * Sayfanın kullanacağı varsayılan stil dosyası
	 * @var string
	 */
	public static $Style = 'default';

	/**
	 *
	 * @var DataTable[]
	 */
	public $DataTables = array();
	/**
	 *
	 * @var DbModelForm
	 */
	public $DbModelForm = null;
	public $OriginalFileUrl = '';

	/**
	 * Liste durum kaydetmelerde hangi parameterelere bakılacak
	 * @var array
	 */
	public $StateParams = array('act', 'act2', 'act3', 'act4', 'mode', 'customTab');
	public $CustomStateParams = array();

	public static $AppPageContrClass = 'PageController';

	public $StopExecution = false;

	public $LoadPageResources = true;

	public $ResponsiveView = false;

	/**
	 * @var string[]
	 */
	public $Keywords = array();

	public $Description = '';

	/**
	 * Smarty bu sayfayı kaç saniye cache den alacağını belirler
	 * @var int
	 */
	public $CacheLifeTime = 0;

	// Sayfaya erişim linki (SEO için)
	public $CanonicalUrl = '';

	public $UseRewriteAct = false;

	public $UrlRewritten = '';

	/**
	 *
	 * @param type $tpl
	 * @param type $dir
	 * @param type $acts
	 */
	public function __construct($tpl = '', $dir = '', $acts = array())
	{
		if(self::$UsePageControllerPath)
		{
			$ref = new ReflectionClass(get_class($this));
			$dir = dirname($ref->getFileName());
			$tpl = basename($ref->getFileName(), '.php') . '.tpl';
		}
		// getFileName slash'ları ters verebildiği için
		// FullPath fonksiyonundan geçiriyoruz
		PageController::$_CurrentInstance = $this;
		$this->PageControllerUrl = FullPath($dir);
		$this->_ActionRoute = $acts;
		$this->SetTemplateUri($tpl);
		require_once 'JsConstants.php';
		$isAjax = @$_GET['ajax'] == '1' || @$_GET['grid'] == '1';
		if (!$isAjax)
			$this->SetDefaultResources();
		if ($this->StopExecution)
			return;
		$this->LoadPageViewModel();
		if (!$isAjax && $this->LoadPageResources)
			$this->LoadCustomResource();
		$currModelName = '';
		if ($this->DbModelForm || count($this->DataTables) > 0)
		{
			$form = $this->DbModelForm;
			if (!$form && count($this->DataTables) > 0)
				$form = reset($this->DataTables);
			$currModelName = is_a($form->ModelDb, 'ModelDb') ? $form->ModelDb->GetModelMap()->Name : '';
			$this->AddResource(JS_DBMODEL_FORM);
			$this->AddJsVar('DbModelName', $currModelName);
		}
		$this->LoadDevTool($currModelName);

		// Rewrite durumlarında, adres satırı parametrelerini JS'e aktarılmalı
		// çünkü window.location.href düzgün hesaplanamıyor
		$qs = $_SERVER['QUERY_STRING'];
		$uri= $_SERVER['REQUEST_URI'];
		if ($uri && $qs && !stristr($uri, $qs))
		{
			$url = array();
			foreach($_GET as $name => $value)
				$url[] = "$name=$value";
			$this->AddJsVar('ORIGINAL_URL', '?' . implode('&', $url));
		}
	}

	/**
	 * Aktif PageController nesnesini döndürür
	 * @return static
	 */
	public static function GetCurrentInstance()
	{
		return self::$_CurrentInstance;
	}

	public static function IsUTF8()
	{
		return strtolower(self::$CharSet) == 'utf-8';
	}

	public function GetStateParams()
	{
		return array_merge($this->StateParams, $this->CustomStateParams);
	}

	public function GetStateURL()
	{
		$acts = $this->GetStateParams();
		$url = array();
		foreach($acts as $act)
			if(isset($_GET[$act]))
				$url[] = "$act=$_GET[$act]";
		return implode('&', $url);
	}

	public function GetState()
	{
		$key = @$_SESSION['UserUniqueKey'];
		if($key)
		{
			$key .= addslashes('?' . $this->GetStateURL());
			$query = "SELECT deger FROM user_storage WHERE id='$key'";
			$state = DB::FetchScalar($query);
			if ($state)
				return json_decode ($state);
		}
		return new stdClass();
	}

	public function IsColPinned($colName, $defaultVal = 0)
	{
		$pinned = $this->GetState()->pinnedCols;
		if (is_array($pinned))
			return in_array($colName, $pinned) ? 1 : 0;
		return $defaultVal;
	}

	public function LoadedModel($obj)
	{

	}
	/**
	 * @param ColumnTemplate $obj
	 */
	public static function ColumnPropertyRenderer($obj)
	{
	}

	/**
	 *
	 * @return \DataTableModelDb|DbModelForm
	 */
	public function LoadPageViewModel()
	{
		$className = get_class($this);
		$ref = new ReflectionClass($className);
		$fileName = str_replace('.php', '.xml', $ref->getFileName());
		$dt = null;
		if (isFile($fileName))
		{
			$this->PageInfo = $pageInfo = new PageInfo();
			$xml = simplexml_load_file($fileName);
			XML_Serialize::Unserialize ($pageInfo, $xml);
			$this->PreBuildViewModel($pageInfo);
			if ($pageInfo->PageTitle && !$this->Title)
				$this->Title = $pageInfo->PageTitle;
			$pageProps = array();
			$tpl = '';
			DataTableBase::$ColumnPropertyRenderer = array(get_class($this), 'ColumnPropertyRenderer');
			switch ($pageInfo->Type)
			{
				case PageInfo::TYPE_LIST:
					$this->AddResource(JS_DBMODEL_LIST);
                    if(Config('app.USE_BS_UI'))
					    $tpl = 'bases/arama_page_bs.tpl';
                    else
					    $tpl = 'bases/arama_page.tpl';
					$dt = new DataTableModelDb($this);
					$model = $pageInfo->ModelBase;
					$dt->Build($model, $pageInfo->Columns);
					if ($dt->CbRenderRow == NULL)
						$dt->CbRenderRow = array($this, 'DataRenderRow');
					if ($dt->CbProcessParam == NULL)
						$dt->CbProcessParam = array($this, 'DataProcessParam');
					if($pageInfo->RowAttributes)
					{
						$attr = explode(',', $pageInfo->RowAttributes);
						$dt->RowAttributes = array_combine($attr, $attr);
					}
					if(is_object($model))
						/* @var $model ModelDb */
						$model = $model->GetModelName();
					$this->DataTables[$dt->GetDb()->GetModelName()] = $dt;
					$pageProps = $pageInfo->DbListProps;
					$dtProps = $dt->DataGrid;
					$acts = NULL;
					foreach($pageInfo->DbListActions as $act)
					{
						if(! $acts)
						{
							$acts = $dt->AddColumnActions();
							$acts->Buttons[] = new BtnIslemList();
						}
						$btn = new BtnIslem();
						ObjectLib::SetFromObj($btn, $act);
						$auth = @$act->auth;
						if(! $auth)
							$auth = '';
						$btn->SetYetkiKontrol($auth);
						$acts->Buttons[] = $btn;
					}
					if($pageInfo->JsDetailPage)
						$this->AddJsVar('DetailPage', $pageInfo->JsDetailPage);
					$dt->CbProcessParam = array($this, 'DataProcessParam');
					break;
				case PageInfo::TYPE_VIEW:
				case PageInfo::TYPE_FORM:
					$dt = DbModelForm::Get($this, $pageInfo->ModelBase, $pageInfo->Columns);
					if($pageInfo->Type == PageInfo::TYPE_FORM)
						$tpl = 'bases/edit_form.tpl';
					else
					{
						foreach($dt->Columns as $col)
							$col->Readonly = true;
						$tpl = 'bases/view_page.tpl';
					}
					$this->Data = $dt->GetObj();
					$this->LoadedModel($this->Data);
					$pageProps = $pageInfo->DbFormProps;
					$dtProps = $dt;
					break;
			}
			foreach($pageProps as $key => $value)
				$dtProps->{$key} = is_bool($dtProps->{$key}) ? ($value == 1) : $value;
			if ($pageInfo->Type == PageInfo::TYPE_LIST && $dtProps->AutomaticStaticGrid == 1)
				$dt->EnableSEOGrid();
			if(isset($dtProps) && $dtProps->PageSize <= 0)
				$dtProps->PageSize = 10;
			$libs = explode(',', $pageInfo->PhpLibs);
			foreach($libs as $lib)
				if($lib && defined($lib))
				{
					$lib = constant($lib);
					if(! LibLoader::IsLoaded($lib))
						LibLoader::Load($lib);
				}
			$libs = explode(',', $pageInfo->JsLibs);
			foreach($libs as $lib)
			{
				$lib = defined($lib) ? constant($lib) : $lib;
				if ($lib)
					$this->AddResource($lib);
			}
			if(! isFile($this->Template) && $tpl)
				$this->Template = $tpl;
		}
		return $dt;
	}

	/**
	 * DbList veya DbForm için xml parse edildikten sonra ViewModel build edilmeden
	 * önce yapılacak işlemler
	 * @param PageInfo $pageInfo
	 * @return PageInfo
	 */
	public function PreBuildViewModel(PageInfo $pageInfo)
	{
		return $pageInfo;
	}

	/**
	 * Her sayfanın en üstüne eklenecek varsayılan
	 * JS/CSS gibi harici kaynakların hangileri
	 * olduğunu dizi olarak döndürür
	 *
	 * @return string[]
	 */
	public function SetDefaultResources()
	{
		$this->AddResource(JS_DGR_COMMON);
		$this->AddResource(JS_JQUERY);
		$this->AddResource(JS_MASKED_INPUT);
		$this->AddResource(JS_SCROLLTO);
		$this->AddResource(JS_JQUERY_UI);
		$this->AddResource(JS_DRAGGABLE);
		$this->AddResource(JS_CPAINT);
		$this->AddResource(JS_FONT_AWESOME);
		$this->AddResource(JS_JQUERY_CHOSEN);
		$this->AddResource('js/Pages.js', TRUE);
		$this->AddResource("dogru/PageController/styles/" . self::$Style . "/index.css");
		$this->AddJsVar('UseRewriteAct', $this->UseRewriteAct);
	}

	public function LoadCustomResource()
	{
		$path = $this->PageControllerUrl;
		if(DosyaSistem::IsInDir($path, KNJIZ_DIR))
			$relative = RelativePath($path, KNJIZ_DIR);
		else
			$relative = RelativePath($path, App::$Klasor);
		$parts = explode('/', $relative);
		$jsFile = end($parts) . '.js';
		$cssFile= end($parts) . '.css';
		if (is_file($path . '/' . $jsFile))
			$this->AddResource($relative . '/' . $jsFile);
		if (is_file($path . '/' . $cssFile))
			$this->AddResource($relative . '/' . $cssFile);
	}

	private function LoadDevTool($currModelName)
	{
		if ((@$_GET['cisc'] == 'dev' || @$_SESSION['cisc'] == 'dev' || Debug::$IsAktif) &&
			!isset($_POST['act']) && @$_GET['ajax'] != '1' && @$_GET['mode'] != 'ajax' &&
			!isDeveloperPage() &&
			!isset($_GET['notPage']) && @$_GET['grid'] != '1' &&
			!preg_match('#/pravi/#i', $_SERVER['REQUEST_URI']))
		{
			$_SESSION['OldUrl'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$_SESSION['OldPath'] = RelativePath($this->PageControllerUrl);
			$_SESSION['OldUrlParams'] = $_GET;
			$_SESSION['cisc'] = 'dev';
			if ($currModelName)
				$_SESSION['CurrModelName'] = $currModelName;
		}

		if (isset($_GET['cisc']) && !$_GET['cisc'])
		{
			unset($_SESSION['cisc']);
			unset($_SESSION['OldUrl']);
			unset($_SESSION['OldPath']);
			unset($_SESSION['OldUrlParams']);
		}

		if (isset($_SESSION['OldUrl']) &&
			!in_array(@$_GET['act'], array('cisc')))
		{
			$this->AddJsVar('OldUrl', $_SESSION['OldUrl']);
			$this->AddJsVar('OldPath', $_SESSION['OldPath']);
			$this->AddJsVar('DbModelName', @$_SESSION['CurrModelName']);
			$this->AddResource (JS_DEVELOPER);
		}
	}

	private function Minify($file, $target = '')
	{
		if (! $target)
			$target = preg_replace('/\.([^.]+)$/', '.min.$1', $file);
		if (preg_match('/\.css$/', $file))
		{
			$minifier = new MatthiasMullie\Minify\CSS($file);
			$minifier->setImportExtensions(array());
		}
		else
			$minifier = new MatthiasMullie\Minify\JS($file);
		$contents = $minifier->minify();
		file_put_contents($target, $contents);
	}

	private function MinifyResources($list, $type, $dir = '')
	{
		$isCss = $type == 'css';
		$CssUrl = function ($content, $preUrl){
			return preg_replace_callback("/url\s*\(\s*('|\")?([^('\")]*)('|\")?\s*\)/i"
				, function($m) use($preUrl) {
					if (preg_match("/data:/", $m[2]))
						return "url($m[2])";
					return "url($preUrl/$m[2])";
				}, $content);
		};
		$resources = array();
		$names = '';
		$fileUrls = array();
		foreach($list as $file)
		{
			if (! preg_match("/\.$type$/i", $file))
				$file .= ".$type";
			$url = GetResourceUrl($file, $type, $dir, FALSE);
			$fileUrls[$file] = dirname($url);
			$url = preg_replace('!^pravi/!', KNJIZ_DIR, $url);
			if (! isFile($url))
				continue;
			$names .= $url;
			$resources[$file] = $url;
		}
		$age = 0;
		if (LibLoader::IsLoaded(LIB_AUTOLOADER))
			$age = AutoLoader::$classesFileAge;
		$names .= "?v=$age";
		$minifyFile = 'prv/izv_' . md5($names) . ".$type";
		if (! isFile($minifyFile))
		{
			$contents = array();
			srand(microtime(true) + ord(session_id()));
			$rand1 = rand(1e5, 1e10);
			foreach ($resources as $key => $file)
			{
				$rand2 = rand(1e10, 1e10 + 1e3);
				$tmpFile = "prv/izv_tmp_$rand1"."_$rand2.$type";
				// $contents[] = "/**********[ $file ]*************/";
				$cont = file_get_contents($file);
				if ($isCss)
				{
					$preUrl = "../$fileUrls[$key]";
					$cont = $CssUrl($cont, $preUrl);
				}
				if (! preg_match('/\.min\./', $file))
				{
					file_put_contents($tmpFile, $cont);
					$this->Minify($tmpFile, $tmpFile);
					$cont = file_get_contents($tmpFile);
				}
				if (!$isCss)
					$cont .= ';';
				$contents[] = $cont;
				@unlink($tmpFile);
			}
			file_put_contents($minifyFile, implode("\n", $contents));
		}
		$minifyFile .= "?v=$age";
		if (! $isCss)
			return "<script type=\"text/javascript\" src=\"$minifyFile\"></script>";
		else
			return "<link href=\"$minifyFile\" rel=\"stylesheet\" type=\"text/css\" />";
	}

	public function LoadResources($type = 'all')
	{
		$inc = array();
		$rPath = RelativePath($this->PageControllerUrl);
		if ($type == 'all' || $type == 'css')
			if (! Debug::$IsAktif)
				$inc[] = $this->MinifyResources($this->CssList, 'css', $rPath);
			else
				foreach($this->CssList as $file)
					$inc[] = AddCSS($file);
		if ($type == 'all' || $type == 'js')
		{
			$relative = str_ireplace(App::$Klasor, '', KNJIZ_DIR);
			$libUrl = $relative == KNJIZ_DIR ? 'pravi/' : $relative;
			$inc[] = "<script>var KNJIZ_URL='$libUrl';</script>\n";
			if (Debug::$IsAktif)
				foreach($this->JsList as $file)
					$inc[] = AddJS($file, 'js', $rPath);
			else
				$inc[] = $this->MinifyResources($this->JsList, 'js', $rPath);
			if (LibLoader::IsLoaded(LIB_TEMA) && Debug::$IsAktif)
			{
				$this->AddJsVar('AllThemes', Tema::GetAllThemes());
				$this->AddJsVar('CurrTheme', Tema::$Ad);
			}
            if (Debug::$IsAktif)
			    $this->AddJsVar('CurrDbName', DB::Get()->DbName);
			$this->AddJsVar('DECIMAL_SEPARATOR', Number::$DECIMAL_SEPARATOR);
			$this->AddJsVar('THOUSAND_SEPARATOR', Number::$THOUSAND_SEPARATOR);
			if ($this->pageIcon)
				$this->AddJsVar('PAGE_ICON', "fa $this->pageIcon");

			$inc[] = "<script>";
			foreach($this->JsVars as $name => $array)
			{
				$value = $array['val'];
				if (is_array($value) || is_object($value))
					$inc[] = Kodlama::JsonVar($name, $value, $array['html']);
				else
					$inc[] = "$name='" . addslashes($value) . "';";
			}
			$inc[] ="</script>";
			if (count($this->JsOnloadFunctions) > 0)
			{
				$scripts = array();
				foreach($this->JsOnloadFunctions as $code)
					$scripts []= "$code;";
				$scripts = implode("\n", $scripts);
				$inc[] = "<script>\n$(function(){ $scripts \n})</script>\n";
			}
		}

		return implode("\n", $inc);
	}

	public function AddJsVar($name, $value, $isHtml = false)
	{
		$this->JsVars[$name] = array('html' => $isHtml,	'val'  => $value);
	}

	public function AddJsOnloadFunc($jsCode)
	{
		$this->JsOnloadFunctions[] = $jsCode;
	}

	public function Index()
	{

	}

	public function RequestComplate($type)
	{

	}

	public function FormPosted()
	{
		if (@$_GET['grid'] == '1')
			$this->GetGridData(@$_POST['table_id']);
	}

	public function GetGridData($tableId)
	{
		if (! array_key_exists($tableId, $this->DataTables))
			return '{ }';
		$dt = $this->DataTables[$tableId];
		$dt->Page = $this;
		if ($dt->CbRenderRow == NULL)
			$dt->CbRenderRow = array($this, 'DataRenderRow');
		if ($dt->CbRenderRowMenu == NULL)
			$dt->CbRenderRowMenu = array($this, 'DataRenderRowMenu');
		if ($dt->CbProcessParam == NULL)
			$dt->CbProcessParam = array($this, 'DataProcessParam');
		if (in_array(@$_GET['export'], array('xls', 'pdf', 'doc')))
			return $dt->DataGrid->GetExternalFile($_POST, $_GET['export']);
		App::End($dt->DataGrid->GetJSON($_POST));
	}

	public function GetActionRoute()
	{
		return $this->_ActionRoute;
	}

	public function HandleAjaxRequest()
	{
		// Henüz yüklenmemişse, cpaint'i yükle
		if (! class_exists('cpaint'))
			require_once KNJIZ_DIR . '/others/cpaint/cpaint2.inc.php';

		// Metotları register et
		$GLOBALS['cp'] = $cp = new cpaint();
		$methodNames = array();
		foreach($this->GetAllowedAjaxMethods() as $method)
		{
			$class = get_class($this);
			$methodName = $method;
			$regs = array();
			if (is_array($method))
			{
				if (count($method) == 1)
					$methodName = $method[0];
				else
				{
					$class = $method[0];
					$methodName = $method[1];
				}
			}
			else if (is_string($method) && preg_match("/([a-z0-9_]*)(\.|[:]{2})([a-z0-9_]*)/i", $method, $regs))
			{
				$class = $regs[1];
				$methodName = $regs[3];
			}
			$methodNames[] = $methodName;
			$cp->register(array($class, $methodName));
		}

		// Yetkilendirmeye tabi tutulması gereken metotlardan
		// birisi çağrılmışsa, giriş yapan kullanıcının yetkisine bakarak,
		// yetki aşımı durumunda hata mesajı vererek işlemi durdurmamız gerekiyor
		if (self::$AjaxYetkiKontrolFonk
			&& !in_array(@$_POST['cpaint_function'], array('TableState_Save', 'TableState_Clear')))
			call_user_func(self::$AjaxYetkiKontrolFonk, $methodNames, $this);

		// Yetki kontrolü ve metot tanımları tamamlandı
		// gerekli çağrının handle edilmesi işlemini Cpaint'e bırakıyoruz
		$cp->start(PageController::$CharSet, true);
		$cp->return_data();
		App::End();
	}

	public function Init()
	{
		if(@$_GET['grid'] == 1 && isset($_POST['data']))
		{
			$data = $_POST['data'];
			if (! PageController::IsUTF8())
				$data = Kodlama::UTF8($data);
			$data = json_decode($data, true);
			$_POST = array();
			foreach($data as $item)
				$_POST[$item['name']] = $item['value'];
		}
	}

	public function GetPageFileFetchTpl($fileName, $relativeUrl = true)
	{
		$file = $this->GetPageFileUrl($fileName, $relativeUrl);
		$smarty = SmartyWrap::Load();
		$smarty->assign('Page', PageController::$_CurrentInstance);
		return $smarty->fetch($file);
	}

	public function GetPageFileUrl($fileName, $relativeUrl = true)
	{
		$alt = $this->GetThemeTemplateUrl($fileName, $directOrder = 1);
		$url = $relativeUrl ? RelativePath("$this->PageControllerUrl/$fileName") : FullPath("ui/pages/$fileName");

		$this->OriginalFileUrl = $url;

		return $alt == '' ? $url : $alt;
	}

	public function GetModulTemplateUrl($modul, $template)
	{
		return App::$Klasor . "modules/$modul/ui/templates/$template";
	}

	public function GetTemplateUri()
	{
		return $this->Template;
	}

	public function SetTemplateUri($templateUrl, $relativePath = true)
	{
		$t = $templateUrl;

		if (substr($templateUrl, -4) != '.tpl')
			$templateUrl .= ".tpl";

		if (! stristr($templateUrl, App::$Klasor))
		{
			if ($relativePath)
				$templateUrl = "$this->PageControllerUrl/$templateUrl" ;
			else
				$templateUrl = App::$Klasor . "ui/pages/$templateUrl";
		}

		if (stristr($templateUrl, App::$Klasor))
			$alt = $this->GetThemeTemplateUrl($t);
		else
			$alt = '';
		$libFile = KNJIZ_DIR . "dogru/PageController/$t";
		if(! isFile($templateUrl) && isFile($libFile))
			$templateUrl = $libFile;

		$this->Template = ($alt != '' ? $alt : $templateUrl);
		return $this;
	}

	public function GetThemeTemplateUrl($templateUrl, $ignoreAlternatives = 0)
	{
		if(! LibLoader::IsLoaded(LIB_TEMA))
			return '';
		// Gönderilen şablon da önerilen bir klasör yapısı var mı?
		$parts = explode('/', $templateUrl);
		$givenPath = '';
		if ($parts > 1)
		{
			$templateUrl = end($parts);
			unset($parts[ count($parts) - 1]);
			$givenPath   = implode('/', $parts);
			if (substr($givenPath, -1) != '/')
				$givenPath .= '/';
		}

		$templateUrl = str_replace(".tpl", "", $templateUrl);

		// TEMA Klasöründe bir alternatif varsa
		// onu yüklemeye çalışalım
		$route = implode('_', $this->_ActionRoute);

		// Alternatif 1:
		// * Gönderilen dosyanın kendisi
		$alternatives = array($templateUrl);

		if (! $ignoreAlternatives)
		{
			// Alternatif 2:
			// * act1_act2_act3.tpl
			$alt = $route;
			$alternatives[] = $alt;

			// Alternatif 3:
			// * act1_act2_act3_$templateUrl.tpl
			if ($templateUrl != end($this->_ActionRoute))
				$alt .= '_' . $templateUrl;
			$alternatives[] = $alt;

		}
		// Alternatif 4:
		// * act1_act2_templateUrl.tpl
		$path = $this->_ActionRoute;
		if(is_array($path) && count($path) > 0){
			unset($path[ count($path) - 1]);
			$alt = implode('_', $path) . '_' . $templateUrl;
			$alternatives[] = $alt;
		}

		// Alternatifleri ters sırada kontrol et
		$unique_alts = array_unique($alternatives);
		$reverse_alts = array_reverse($unique_alts);
		foreach($reverse_alts as $alt)
		{
			//TODO-DGR
			//tema klasorundeki guest_default sayfasını çağırırken başına ekstradan / işareti eklediği için kaldırıldı
//			if ($givenPath != '')
//			{
//				$fullPath = Tema::FileFullPath($givenPath . $alt . '.tpl');
//				if (isFile($fullPath))
//					return $fullPath;
//			}

			$fullPath = Tema::FileFullPath($alt . '.tpl');
			if (isFile($fullPath))
				return $fullPath;
		}

		return '';
	}

	/**
	 * Belli bir şablona uyan tüm public metotlar Ajax tarafından çağrılabilir
	 * @return string[]
	 */
	public function GetAllowedAjaxMethods()
	{
		// Şablon
		$pattern = '/^[^_].*/';

		// Interface methodları çağrılamaz
		$intMethods = get_class_methods('IPageController');

		// Metotlar
		$reflector = new ReflectionClass($this);
		$allMethods = $reflector->getMethods(ReflectionMethod::IS_PUBLIC);
		$availableMethods = array();
		foreach($allMethods as $method)
		{
			/* @var $method ReflectionMethod */
			if ($method->isStatic() &&
					preg_match($pattern, $method->name) &&
					! in_array($method->name, array_values($intMethods)))
			$availableMethods[] = array(get_class ($this), $method->name);
		}

		return $availableMethods;
	}

	/**
	 * İşlem yetkisi gerektiren Ajax metodlarının bir listesini döndürür
	 * Bu listede döndürülmeyen ajax metotları yetkilendirme kontrolüne
	 * tabi tutulmaz
	 * @return string[]
	 */
	public function GetAuthorizedAjaxMethods()
	{
		return 'all';
	}

	public static function TableState_Save($obj)
	{
		$key = @$_SESSION['UserUniqueKey'];
		if(!$key)
			return;
		session_write_close();
		DB::$SEND_HEAVY_QUERY = FALSE;
		$key .= "?$obj->url";
		$obj = addslashes(Kodlama::JSON($obj));
		$query = "REPLACE INTO user_storage SET id='$key', deger='$obj'";
		return DB::Execute($query);
	}

	public static function TableState_Clear($url)
	{
		$key = @$_SESSION['UserUniqueKey'];
		if(!$key)
			return 1;
		session_write_close();
		return DB::Delete('user_storage', "id='$key?$url'");
	}

	public static function SaveQueryAs($data)
	{
		list($ad, $url, $params) = ObjToArray($data);
		$key = @$_SESSION['UserUniqueKey'];
		if (!$key)
			return 'Kaydetme özelliğini kullanmak için lütfen giriş yapınız';
		session_write_close();
		$ad = trim(addslashes(preg_replace("/[\?#\[\]\(\)]/", " ", $ad)));
		$url = addslashes($url);
		$params = addslashes(Kodlama::JSON($params));
		$key = "$key#NewTab#$ad?$url";
		return DB::Execute("REPLACE INTO user_storage SET id='$key', deger='$params'");
	}


	public function GetSavedQueries()
	{
		$acts = $this->StateParams;
		$url = array();
		foreach($acts as $act)
			if (@$_GET[$act] && substr($act, 0, 3) == 'act')
				$url[] = $act. '=' . $_GET[$act];
		$url = addslashes(implode('&', $url));
		$key = @$_SESSION['UserUniqueKey'];
		$searchTerm = "$key#NewTab#%?$url";
		$rows = DB::FetchArray("SELECT id, deger FROM user_storage WHERE id LIKE '$searchTerm'");
		$pattern = str_replace("%", "(.*)", $searchTerm);
		$regs = array();
		for($i=0; $i<count($rows); $i++)
		{
			$row = $rows[$i];
			if (! preg_match("/$pattern/i", $row['id'], $regs))
				continue;
			$rows[$i] = (object)array(
				'Ad'	=> substr($regs[1], 0, -1),
				'Key'	=> str_replace("$key#NewTab#", '', $row['id']),
				'Params'=> Kodlama::JSONTryParse($row['deger']));
		}
		return $rows;
	}

	public static function DeleteSavedQuery($pageKey)
	{
		$key = @$_SESSION['UserUniqueKey'];
		$searchTerm = addslashes("$key#NewTab#$pageKey");
		return DB::Execute("DELETE FROM user_storage WHERE id = '$searchTerm' ");
	}
	/**
	 * @param ModelBase $obj
	 */
	public static function DbModelForm_Save($obj)
	{
		$page = PageController::$_CurrentInstance;
		$form = $page->DbModelForm;
		if (!$form)
			return 'Form nesnesi bulunamadı';
		$model = $form->GetObj();
		$model->SetFromObj($obj);
		$sonuc = $page->DbModelFormValidate($model);
		if($sonuc != 1)
			return $sonuc;
		$sonuc = $form->Save($obj);
		if($sonuc != 1)
			return $sonuc;
		$page->DbModelFormAfterSave($model);
		$page->DbModelFormAfterSave2($model, $obj);
		return $sonuc;
	}

	protected function DbModelFormValidate($obj)
	{
		return 1;
	}

	protected function DbModelFormAfterSave($model)
	{
	}

	protected function DbModelFormAfterSave2($model, $obj)
	{
	}

	/**
	 * İşlem yetkisi GEREKTİRMEYEN Ajax metodlarının bir listesini döndürür
	 * @return string[]
	 */
	public function GetNoAuthorizedAjaxMethods()
	{
		return array();
	}

	public function ShowError($msg, $tpl = 'hata.tpl')
	{
		$this->StopExecution = true;
		// Ajax isteklerinde direk hatayı göster
		if (App::IsUTF8())
			$msg = Kodlama::UTF8 ($msg);
		if(@$_GET['ajax'] == 1)
			ThrowException($msg);

		$this->SetTemplateUri($tpl, false);
		$this->hata = $msg;
		return false;
	}

	public function ShowRecordNotFound(
		$message = 'Aranan kayıt bulunamadı veya kayıt yayından kaldırılmış olabilir',
		$redirectUrl = 'index.php',
		$redirectTitle= 'Ana sayfa',
		$error404 = true
		)
	{
		ThrowException($message);
	}

	public function Render()
	{
		$smarty = SmartyWrap::Load();
		$smarty->assign('Page', $this);
		if($this->DataTables)
		{
			$this->AddJsVar('StateParams', $this->GetStateParams());
			$this->AddJsVar('SavedQueries', $this->GetSavedQueries());
		}
		if (count($this->DataTables) > 0)
			$this->AddResource(JS_DBMODEL_LIST);
		foreach($this->DataTables as $dt)
		{
			$this->DtColumns = array();
			foreach ($dt->Columns as $v)
				$this->DtColumns[] = clone $v;
			$this->HideSearch = ! $dt->DataGrid->ShowAdvSearch;
			break;
		}
		if ($this->DbModelForm)
		{
			$this->DbModelForm->Page = $this;
			$obj = $this->DbModelForm->GetObj();
			$this->CustomSaveFunc = $this->DbModelForm->CustomSaveFunc;
			$this->AddJsVar('DbModel_CustomSaveFunc', $this->CustomSaveFunc);
			if ($this->DbModelForm->CustomLoadFunc)
			{
				$obj = ObjectLib::CloneObj($obj, 2);
				if ($this->ResourceAdded(JS_ERS_TABLE))
				{
					foreach($obj as $name => $value)
						if (is_a($value, 'ModelBaseArray'))
							foreach($value as $subName => $subValue)
								$value[$subName] = self::TriggerAppFile($subValue);

				}
				$this->AddJsVar('DbModelObj', $obj);
				if ($this->LoadPageResources)
					$this->AddJsOnloadFunc($this->DbModelForm->CustomLoadFunc . '(DbModelObj)');
			}
		}
		if($this->ResourceAdded(JS_ERS_TABLE))
		{
			$fileHtml = new FormInputFileContainer();
			$this->AddJsVar('TemplateAppFileHtml', $fileHtml->html());
			$imageHtml = new FormInputImageContainer();
			$this->AddJsVar('TemplateAppFileImageHtml', $imageHtml->html());
		}
		// PageController den üretilen sınıfın tüm _ ile başlamayan değişkenlerini
		// smarty e aktar.
		$vars = get_object_vars($this);
		foreach ($vars as $name => $value)
			if ($name[0] != '_' && $value != NULL)
				$smarty->assign($name, $value);
		foreach($this->DataTables as $id => $dt)
		{
			$dt->DataGrid->Render($this, $id);
			foreach($dt->Columns as $name => $col)
			{
				$target = ArrayLib::SearchObj($this->DtColumns, array('Name' => $name));
				if ($target)
					$target->Pinned = $col->Pinned;
			}
		}
		PageRouter::RenderDefault();
	}

	/**
	 * @param ModelBase $obj
	 */
	protected function TriggerAppFile($obj)
	{
		if (! is_a($obj, 'ModelBase'))
			return $obj;
		$map = $obj->GetModelMap();
		$obj2 = ObjectLib::Cast('stdClass', $obj);
		foreach($map->DbFields as $name => $field)
			if ($field->ModelName == 'AppFile')
				$obj2->{$name} = $obj->{$name};
		return $obj2;
	}

	/**
	 * @param stdClass|ModelBase $realData
	 * @param stdClass|ModelBase $rowData
	 * @param string[] $attributes
	 * @param DataTable $dataTable
	 */
	public function DataRenderRow($realData, $rowData, $attributes, $dataTable)
	{

	}

	/**
	 * @param stdClass|ModelBase $params
	 */
	public function DataProcessParam($params)
	{

	}

	public static function DataGridDelete($params)
	{
		list($id, $modelBase) = ObjToArray($params);
		$page = self::$_CurrentInstance;
		foreach($page->DataTables as $dt)
		{
			$db = $dt->ModelDb;
			if ($db && $db->GetModelName() == $modelBase)
			{
				$obj = $db->GetById($id);
				if (!$obj)
					ThrowException("$modelBase(#$id) kaydı bulunamadı");
				$val = $page->DataGridIsDeletable($obj);
				if ($val == 1)
					return $obj->Delete();
				ThrowException($val);
			}
		}
		ThrowException("$modelBase bulunamadı");
		return 'hata';
	}

	/**
	 * @param ModelBase $obj
	 */
	public function DataGridIsDeletable($obj)
	{
		return 1;
	}

	public static function FetchPageTemplate($fileName, $relativeUrl = true)
	{
		$page = PageController::$_CurrentInstance;
		if($page)
			return $page->GetPageFileFetchTpl($fileName, $relativeUrl);
		else
			return 'içerik bulunmadı';
	}

	/**
	 * Verilen JS/CSS kaynağını, bağımlılıklarına da bakarak
	 * sayfanın eklenecekler listesine ekler
	 * @param type $jsName
	 */
	public function AddResource($resName, $ifExist = false)
	{
		if($ifExist && !isFile($resName))
			return;
		$isJs = preg_match("/\.js$/i", $resName);
		if ($isJs)
			$array =& $this->JsList;
		else
			$array =& $this->CssList;
		if (in_array($resName, $array))
			return;
		if (array_key_exists($resName, JsDependency::$Files))
		{
			$deps = JsDependency::$Files[$resName];
			foreach($deps as $dep)
				$this->AddResource($dep);
		}
		if (!is_int($resName) && ! in_array($resName, $array))
		{
			if (Debug::$IsAktif && preg_match('/\.min\./', $resName))
			{
				$url = GetResourceUrl($resName, $isJs ? 'js' : 'css', '', FALSE);
				$url = preg_replace('!^pravi/!', KNJIZ_DIR, $url);
				$src = str_replace('.min.', '.', $url);
				if (isFile($src) && filemtime($src) > filemtime($url))
					$this->Minify($src, $url);
				if (isFile($src))
					$resName = str_replace('.min.', '.', $resName);
			}
            if (! in_array($resName, $array))
                $array[] = $resName;
		}
	}

	public function ResourceAdded($res)
	{
		// Eğer sabit değer hala dosyaya dönüştürülmediyse
		if (in_array($res, $this->JsList))
			return true;

		// Sabit dosyaya dönüştürüldüyse, dosyalara bakalım
		// tüm bağımlılıkların yüklenmiş olması gerekiyor
		if (is_int($res))
		{
			$files = JsDependency::$Files[$res];
			$files = array_filter($files, 'is_string');
			$diff = array_diff($files, $this->JsList);
			if (! $diff)
				return true;
		}

		return false;
	}

	/**
	 * @return ModelBase Sayfadaki aktif ModelBase Instance verir
	 */
	public static function GetModelBaseInst()
	{
		return PageController::$_CurrentInstance->Data;
	}

	public static function ChangeSqlViewFavorite($obj)
	{
		$fav = $obj->fav;
		$isAdd = $obj->isAdd;
		AppSqlViewListBase::ChangeUserFavorite($fav, $isAdd);
		return 1;
	}

	public function GetCanonicalUrl()
	{
		$isFullUrl = substr($this->CanonicalUrl, 0, 4) == 'http';
		if ($this->CanonicalUrl)
			return ($isFullUrl ? '' : $GLOBALS['SITE_URL']) . $this->CanonicalUrl;
		$protocol = isset($_SERVER['HTTPS']) ? "https" : "http";
		return "$protocol://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	}

	/**
	 * @param array $dataRow
	 * @param array $columns
	 * @param string $tpl
	 * @return string
	 */
	public static function ParseTemplate($dataRow, $columns, $className, $tpl)
	{
		static $names = array();
		if (! isset($names[$className]))
		{
			$regs = array();
			preg_match_all("/(#\w+)/i", $tpl, $regs);
			if (isset($regs[0]))
			{
				$names[$className] = $regs[0];
				array_unique($names[$className]);
			}

			// İsimleri uzunluklarına göre tersten sıralıyoruz
			// böylece #Name ve #NameSurname karışmıyor
			// (önce uzun olan değişken, #NameSurname değiştirilecek)
			usort($names[$className], function($var1, $var2){
				return strlen($var2) - strlen($var1);
			});
		}

		$is_obj = is_object($dataRow);
		if (! $is_obj)
			$columns = array_keys($columns);
		foreach($names[$className] as $name)
		{
			$varName = substr($name, 1);
			if ($is_obj)
				$tpl = str_replace($name, @$dataRow->{$varName}, $tpl);
			else
			{
				$index = array_search($varName, $columns);
				if ($index !== false)
					$tpl = str_replace($name, $dataRow[$index], $tpl);
			}
		}

		return $tpl;
	}

	/**
	 * @return array
	 */
	public function GetBSPagingLinks()
	{
		$dt = reset($this->DataTables);
		/* @var $dt DataTableModelDb */
		$paging = $dt->DataPageInfo;
		$getUrl = function($pageNo) use ($paging){
			/* @var $paging PagedData */
			if ($pageNo <= 0 || $pageNo > $paging->PageCount)
				return '';
			$var = 'iDisplayStart';
			$start = ($pageNo - 1) * $paging->PageSize;
			$pageCtrl = PageController::$_CurrentInstance;
			if ($pageCtrl->UrlRewritten)
				return "$pageCtrl->UrlRewritten/$start";
			else
				return PagedData::SayfaUrlVer($var) . "&$var=$start";
		};

		// 5 link için başlangıç sayfası
		$pageLinkCount = 10;
		$pageStart = intval(($paging->PageNo -1) / $pageLinkCount) * $pageLinkCount;
		$pageLinks = array();
		$pageLinks['&laquo;'] = $getUrl($pageStart);
		for($i=$pageStart+1; $i <= $pageStart + $pageLinkCount; $i++)
		{
			if ($i > $paging->PageCount)
				continue;
			if ($i == $paging->PageNo)
				$pageLinks[$i] = '';
			else
				$pageLinks[$i] = $getUrl($i);
		}

		$pageLinks['&raquo;'] = $getUrl($i);

		return $pageLinks;
	}

	public function IsStaticGrid()
	{
		if (count($this->DataTables) == 0)
			return 0;
		$dt = reset($this->DataTables);
		/* @var $dt DataTableModelDb */
		return $dt->StaticGrid;
	}
}
