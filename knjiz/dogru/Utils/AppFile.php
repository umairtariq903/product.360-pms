<?php
if(! class_exists('AppFileBase'))
	return;
/**
 * @property string $Uzanti
 * @property bool $Exists
 * @property int $MTime
 * @property string $Link
 */
class AppFile extends AppFileBase
{
	public static $FILE_DIR = 'apli_dat/';
	public static $TEMP_DIR = 'apli_dat/prv/';
	public static $MANUAL_DIR='apli_dat/manual/';

	public $Changed = false;
	public $BoyutStr = '';
	/* Sonradan hesaplanan alanlar */
	protected $Uzanti = null;
	protected $Exists = null;

	public function Init($row)
	{
		parent::Init($row);
		if($this->Yol)
			$this->Yol = self::$FILE_DIR . $this->Yol;
		if($this->Boyut > 0)
			$this->BoyutStr = GetShortSize($this->Boyut);
		return $this;
	}

	public static function InitFromObj($obj)
	{
		$file = new self();
		return ObjectLib::SetFromObj($file, $obj);
	}

	public function Tasi()
	{
		/* @var $old AppFile */
		$old = null;
		if ($this->Id > 0)
			$old = AppFileDb::Get()->GetById($this->Id);
		if (! $old)
		{
			$yol = str_replace(AppFile::$FILE_DIR, '', $this->Yol);
			$old = AppFileDb::Get()->GetFirst(array('Yol' => Condition::EQ($yol)));
			if ($old)
				$this->Id = $old->Id;
		}
		$oldYol = $old ? $old->GetTamYol() : '';
		$yeniYol= $this->GetTamYol();
		if ($old && $oldYol == $yeniYol)
			return $this;
		if ($old && $oldYol != $yeniYol)
			DosyaSistem::Sil($oldYol);
		if ($this->Yol && $this->IsTemp())
			$this->SetYol($yeniYol);
		$this->Changed = true;
		return $this;
	}

	public function DecodeId()
	{
		$fileId = DgrCode::Decode($this->Id);
		if ($fileId != $this->Id)
			$this->Id = $fileId;
		else
			$this->Id = 0;
		return $this->Id;
	}

	public function SetYol($file)
	{
		$flExt = DosyaSistem::GetExt($file);
		$random = function() use($flExt) {
			return '_' . substr(md5(microtime(true) . rand(1000, 9999)), 0, 8)
				. '.' . $flExt;
		};
		$temp = $random();
		if(preg_match('/^http:/i', $file))
		{
			$yeniAd = AppFile::$TEMP_DIR . 'file' . $temp;
			if(! copy($file, $yeniAd))
				return;
			$file = $yeniAd;
		}
		if(!file_exists($file))
			return;
		$date = filemtime($file);
		$path = date('Y', $date) . '/' . date('m', $date) . '/';
		$yeniYol = $path . str_replace('.', '_', $this->Origin) . $temp;
		$yeniTamYol = $this->GetTamYol($yeniYol);
		while (file_exists($yeniTamYol))
		{
			$yeniYol = $path . str_replace('.', '_', $this->Origin) . $random();
			$yeniTamYol = $this->GetTamYol($yeniYol);
		}
		DosyaSistem::Tasi($file, $yeniTamYol);
		$this->Yol = $yeniYol;
		$this->OnChanged();
		$this->Changed = true;
	}

	public function IsTemp()
	{
		return strstr($this->GetTamYol(), self::$TEMP_DIR);
	}

	public function GetTamYol($file = '')
	{
		$pre = self::$FILE_DIR;
		$file = RelativePath($file ? $file : $this->Yol);
		if (!$file)
			return $file;
		if (! strstr($file, $pre))
			$file = $pre . $file;
		return  App::$Klasor . $file;
	}

	public function OnChanged()
	{
		$tamYol = $this->GetTamYol();
		if (is_file($tamYol))
		{
			$this->Tarih = date('d-m-Y H:i', filemtime($tamYol));
			$this->Boyut = filesize($tamYol);
		}
		return $this;
	}

	public function UpdateOrigin($model, $field, $id)
	{
		$this->Origin = "$model.$field.$id";
		$origin = str_replace('.', '_', $this->Origin);
		$changed = false;
		if(! preg_match("/$origin/", $this->Yol))
		{
			$this->SetYol ($this->GetTamYol());
			$changed = true;
		}
		if ($this->__get('Exists') && ($this->Id > 0 || $changed))
			$this->Save_WoC();
	}

    public function KopyaVer()
    {
        $new = new AppFile();
        $new->Ad = $this->Ad;
        $new->Yol = '';
        if($this->Ad)
        {
            $tmp = self::$TEMP_DIR . 'temp' . (rand(1000, 9999)) . '.' . $this->__get('Uzanti');
            DosyaSistem::Kopyala($this->GetTamYol(), FullPath($tmp));
            $new->Yol = $tmp;
        }
        return $new;
    }

    public function KopyaVerIsimli()
    {
        $new = new AppFile();
        $new->Ad = $this->Ad;
        $new->Yol = '';
        if($this->Ad)
        {
            $tmp = self::$TEMP_DIR . $this->Ad . '.' . $this->__get('Uzanti');
            DosyaSistem::Kopyala($this->GetTamYol(), FullPath($tmp));
            $new->Yol = $tmp;
        }
        return $new;
    }

	/**
	 * @param AppFile[] $newFiles
	 * @param string $model
	 * @param string $field
	 * @param int $id
	 */
	public static function UpdateFiles($newFiles, $model, $field, $id)
	{
		$origin = "$model.$field.$id";
		$newFiles = str_replace("'", '', $newFiles);
		$cond = Condition::FindInSet($newFiles);
		$files = AppFileDb::Get()->GetList(array('Id' => $cond));
		foreach($files as $f)
			$f->UpdateOrigin($model, $field, $id);
		$params = AppFile::AsParams();
		$params->Origin = "='$origin'";
		$params->Id = Condition::Field(OPRT::NOT_FIND_INSET, $newFiles);
		$oldFiles = AppFileDb::Get()->GetList($params);
		foreach($oldFiles as $file)
			$file->Delete();
	}

	public function __get($name)
	{
		if ($name == 'Exists')
			return $this->Yol != '' && file_exists($this->GetTamYol());
		else if ($name == 'Uzanti' || $name == 'Extension')
			return DosyaSistem::GetExt($this->Yol);
		else if ($name == 'MTime')
			return $this->MTime = file_exists($this->GetTamYol()) ? filemtime($this->GetTamYol()) : 0;
		else if ($name == 'Link')
		{
			$url = RelativePath($this->GetTamYol());
//			if (Config('app.FILE_DOWNLOAD_URL_REWRITE'))
//				$url = str_replace('apli_dat/', 'files/', $url) . ";$this->Ad";
			return $url;
		}
		return parent::__get($name);
	}

	public function __toString()
	{
		return $this->GetTamYol();
	}

	public static function DosyadanTopluTasi($modelName, $field, $klasor)
	{
		$db = call_user_func(array($modelName, 'Get'));
		/*@var $db ModelDb*/
		$map = $db->GetModelMap();
		$table = $map->Name;
		$dbFieldName = $map->DbFields[$field]->Name;
		$list = $db->GetList(array($field => '!=""'));
		foreach($list as $item)
		{
			$old = Dosya::Cikart($item->GetValue($field));
			$old->Klasor = $klasor;
			$new = $item->{$field};
			/*@var $new AppFile*/
			if($new->Yol)
				continue;
			if($old->Exists)
			{
				$new->UpdateOrigin($db->GetModelName(), $field, $item->Id);
				$new->SetYol($old->GoreceliYol);
				$new->Ad = $old->Ad;
				$item->Save_WoC();
			}
			else
				DB::Update($table, "$dbFieldName=''", "id=$item->Id");
		}
	}

	public function GetAsImg($width = '', $defaultImg = '')
	{
		$img = new FormInputImageContainer();
		$img->Deletable = false;
		if ($width && $width > 0)
			$img->DefaultImgWidth = $width;
		if ($defaultImg)
			$img->DefaultImgUrl = $defaultImg;
		$pgCtrl = PageController::$_CurrentInstance;
		if ($pgCtrl)
			foreach($img->JsRequirements as $js)
				$pgCtrl->AddResource($js);
		return $img->val($this);
	}

	public function ToStdObj($fields = null, $regExp = false)
	{
		if ($fields == null)
			$fields = 'Id,Ad,Yol,Kategori,Aktif,Aciklama,Boyut,Tarih';
		$obj = parent::ToStdObj($fields, $regExp);
		$obj->Id = DgrCode::Encode($obj->Id);

		return $obj;
	}

	public function ToLinkHtml()
	{
		if (! $this->__get('Exists'))
			return 'Dosya yüklenmedi';

		if (@$_GET['pdfuret'] == '1')
			return "<a href=\"$this->Yol\" target=\"_blank\">$this->Ad</a>";

		$formItem = new FormInputFileContainer();
		$formItem->SetReadOnly(true);
		$formItem->val($this);

		return "$formItem";
	}

	/**
	 *  Verilen resmi, genişliği baz alınacak şekilde küçültür.
	 * @param int $genislik
	 * @return $this
	 */
	public function Kucult($genislik = 300)
	{
		if (! preg_match("/\.(jpe?g|g[iİıI]f|png)$/i", $this->Yol) ||
			! $this->__get('Exists'))
			return $this;
		$path = $this->GetTamYol();
		list($oldWidth) = getimagesize($path);
		if($oldWidth <= $genislik)
			return $this;
		$img = new ImageEdit($this->__get('Uzanti'));
		$img->generateThumbVersion($path, $path, $genislik);
		return $this;
	}
}

class AppFileDb extends AppFileDbBase
{
	public function Delete(ModelBase $obj, $control = ModelDb::DELETE_WITH_CONTROL)
	{
		/* @var $obj AppFile */
		DosyaSistem::Sil($obj->GetTamYol());
		return parent::Delete($obj, $control);
	}

	public function Save(ModelBase $obj, $control = ModelDb::SAVE_WITH_CONTROL )
	{
		/* @var $obj AppFile */
		$oldYol = $obj->Yol;
		$obj->Yol = str_replace(AppFile::$FILE_DIR, '', $obj->Yol);
		if ($obj->IsTemp())
			ThrowException ("Dosya yükleme sırasında bir hata oluştuğundan işlem yapılamadı, ilgili dosyayı silip yükleyerek tekrar deneyiniz");
		$sonuc = parent::Save($obj, $control);
		$obj->Yol = $oldYol;
		return $sonuc;
	}

	private static function stsGetCountAndSize($group = 4)
	{
		$cb = function($row){
			$row['Adet'] = Number::Format($row['Adet']);
			$row['Boyut'] = GetShortSize($row['Boyut'], 'GB');
			return $row;
		};

		$query = "
			SELECT LEFT(yol, $group) AS Donem, COUNT(*) AS Adet, SUM(boyut) AS Boyut
			FROM app_file
			WHERE boyut > 0
			GROUP BY Donem DESC";
		if ($group > 0)
			return DB::FetchAssoc($query, $cb);
		else
			return DB::FetchSingle($query, $cb);
	}

	public static function stsGetOzet()
	{
		return self::stsGetCountAndSize(0);
	}

	public static function stsGetYears()
	{
		return self::stsGetCountAndSize(4);
	}

	public static function stsGetTerms()
	{
		return self::stsGetCountAndSize(7);
	}

	public static function checkAllFiles()
	{
		set_time_limit(0);
		Transaction::Commit();
		DB::Update('app_file', 'boyut=0', '1');
		AppFileDb::Get()->WalkList(array(), function(AppFile $file){
			if (!$file->Exists)
				return;
			$file->OnChanged();
			$file->Save_WoC();
		});
		return array(
			'dosya_yok' => DB::FetchScalar('SELECT COUNT(*) FROM app_file WHERE boyut=0')
		);
	}
}
