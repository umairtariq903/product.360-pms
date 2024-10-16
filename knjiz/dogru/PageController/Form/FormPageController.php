<?php
require_once dirname(__FILE__) . '/Control.php';
require_once dirname(__FILE__) . '/ControlCollection.php';
class FormPageController extends PageController
{
	/**
	 * @var ControlCollection
	 */
	private $Controls = null;

	public function __construct($tpl, $dir, $acts)
	{
		parent::__construct($tpl, $dir, $acts);
		$this->Controls = ControlCollection::FromDeclarations($this);
		foreach($this->Controls as $control)
			$this->{$control->Id} = $control;
	}

	public function HandleAjaxRequest()
	{
		$method = $_POST['cpaint_function'];
		if (in_array($method, $this->GetAllowedAjaxMethods()))
			return parent::HandleAjaxRequest();

		// Henüz yüklenmemişse, cpaint'i yükle
		if (! class_exists('cpaint'))
			require_once KNJIZ_DIR . '/others/cpaint/cpaint2.inc.php';
		$jSon = $_POST['cpaint_argument'][0];
		$controls= json_decode($jSon);
		Kodlama::KarakterKodlamaDuzelt($controls);
		foreach($this->Controls as $Id => $control)
			$control = ObjectLib::SetFromObj($control, $controls->{$Id});
		$return = call_user_func(array($this, $method));
		die(Kodlama::JSON($this->Controls));
	}

	/**
	 *
	 * @param string $template
	 * @param SmartyBC $smarty
	 * @return string
	 */
	public function SmartyPreFilter($template,SmartyBC $smarty)
	{
		$curr	= PageController::$_CurrentInstance->GetTemplateUri();
		if (realpath($smarty->_current_file) != realpath($curr))
			return $template;
		$tpl	= dirname(__FILE__) . '/controls.tpl';
		if (! preg_match("#$tpl#i"))
			$template .= "\r\n{include file='$tpl'}";
		return $template;
	}

	public function Render()
	{
		$smarty = SmartyWrap::Load();
		$smarty->assign('FormPostBack', 1);
		$smarty->assign('FormControls', $this->Controls);
		$smarty->registerFilter('pre', array($this, 'SmartyPreFilter'));
		parent::Render();
	}

	public function __get($name)
	{
		if ($name == 'Controls')
			return $this->Controls;
		else if (array_key_exists ($name, $this->Controls))
			return $this->Controls[$name];
		else
			return null;
	}
}

