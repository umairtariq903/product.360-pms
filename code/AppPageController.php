<?php
class AppPageController extends PageController
{

    public static function Get()
    {
        return new AppPageController();
    }
    public static function ReLocate($subUrl = '', $permanent = false)
    {
        header("location:$GLOBALS[SITE_URL]$subUrl", $permanent, $permanent ? 301 : 302);
        App::End();
        return false;
    }
	public function SetDefaultResources()
	{
		global $TEMA, $BASE_TEMA, $BASE_TEMA_TYPE, $PAGE_PLUGINS, $SITE_URL;
		$isYonetimPage = IfNull($_GET, 'act') == 'admin';
		parent::SetDefaultResources();
		$this->AddResource('css/index.css');
        $this->AddResource('js/common.js');
		$this->AddJsVar('USE_BS_UI', Config('app.USE_BS_UI'));
		$this->AddJsVar('LANGUAGE', "en");
        if(Config('app.USE_BS_UI'))
            $this->AddResource(JS_BOOTSTRAP);

		if (@$_GET['mode'] != 'clear')
		{
			if ($BASE_TEMA)
			{
				$this->GetPageTree();
				$bdir = "themes/$BASE_TEMA";
				$this->AddResource("$bdir/index.js", true);
				$this->AddResource("$bdir/index.css", true);
				$BASE_TEMA_TYPE = $BASE_TEMA_TYPE ? $BASE_TEMA_TYPE : 'default';
				$file = "$bdir/menus/$BASE_TEMA_TYPE";
				$this->AddResource("$file.js", true);
				$this->AddResource("$file.css", true);
			}
			$this->AddResource("themes/$TEMA/css/index.css");
		}

		// Temaya özel kaynakların yüklenmesi
		if(is_array(Config('app.TEMA_EKLENTILER')))
			foreach(Config('app.TEMA_EKLENTILER') as $res)
			{
				/*if ($isYonetimPage)
					$res = str_replace ('layout3', 'layout', $res);*/
				$this->AddResource("dodatak/$res");
			}

        // Temaya özel kaynakların yüklenmesi
        if (is_array($PAGE_PLUGINS))
            foreach($PAGE_PLUGINS as $res)
            {
                $this->AddResource("dodatak/$res");
            }

        $this->AddJsVar('SITE_URL', $GLOBALS['SITE_URL']);
        $this->AddJsVar('APP_KOD', App::$Kod);


	}

	public function LoadPageViewModel()
	{
        global $SITE_ADI;
		$dt = parent::LoadPageViewModel();
        if ($this->Title != "")
            $this->Title .= " | " . $SITE_ADI;
        else
            $this->Title = $SITE_ADI;
		return $dt;
	}

	public function GetPageTree()
	{
		$list = MenuPages::Get()->List;
		$sayfalar = array('guest' => array('' => array('MenuName' => 'Anasayfa', 'ChildList' => array())));
		$sayfalar['caption']['guest'] = '';
		if ($list)
		{
			foreach($list as $node)
			{
				$act = $node->Acts[0];
				foreach($node->Children as $child)
					if ($child->MenuName)
					{
						if (! isset($sayfalar[$act]))
						{
							$sayfalar[$act] = array();
							$sayfalar['caption'][$act] = $node->MenuName;
						}
						$sayfalar[$act][$child->Acts[1]] = array('MenuName' => $child->MenuName, 'ChildList' => array());
							$childAct = $child->Acts[1];
							foreach($child->Children as $tochild)
								if ($tochild->MenuName)
								{
									if (! is_array($sayfalar[$act][$childAct]))
									{
										$sayfalar[$act][$childAct] = array();
										$sayfalar['caption'][$act] = array('MenuName' => $child->MenuName, 'ChildList' => array());
										$sayfalar['caption'][$act][$childAct] = $child->MenuName;
									}
									$sayfalar[$act][$childAct]['ChildList'][$tochild->Acts[2]] = $tochild->MenuName;
								}
					}
			}
		}
		$this->AddJsVar('PageUrlTree', $sayfalar);
	}

    public static function getErrorPage()
    {
        $act = IfNull($_GET, 'act');
        $act2 = IfNull($_GET, 'act2');
        if (KisiIsAdmin() || $act == "ajax" || $act == "developer" || $act == "db_model" || $act == "cisc" || $act == "parola_sifirla")
            return null;
        if ($act == "admin" && ! KisiIsAdmin())
            return self::Get()->ShowYetkiPageError();
        if ($act == "kisi" && ! KisiIsKisi())
            return self::Get()->ShowYetkiPageError();

        return null;
    }
    public function ShowPageError($rlocate = '')
    {
        $this->rlocate = $rlocate;
        return $this->SetTemplateUri('error_page.tpl', false);
    }

    public function ShowYetkiPageError($rlocate = '')
    {
        $this->rlocate = $rlocate;
        return $this->SetTemplateUri('error_yetki.tpl', false);
    }

    public function ShowAdminPageError($rlocate = '')
    {
        $this->rlocate = $rlocate;
        return $this->SetTemplateUri('error_admin.tpl', false);
    }

    public function ShowKullaniciError($rlocate = '')
    {
        $this->rlocate = $rlocate;
        return $this->SetTemplateUri('error_kullanici.tpl', false);
    }
}

PageController::$AppPageContrClass = 'AppPageController';
