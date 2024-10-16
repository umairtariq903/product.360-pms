<?php
class PhpError
{
	public $Id = 0;
	public $ErrNo = 0;
	public $ErrStr = '';
	public $ErrFile = '';
	public $ErrLine = 0;
	public $SesionId = '';
	public $ErrTime = '';
	public $BackTree = array();

	public function Show()
	{
		$full = FullPath($this->ErrFile);
		$file = RelativePath($this->ErrFile);
		$hata = "<b>My ERROR</b> [$this->ErrNo] $this->ErrStr<br />\n";
        $hata .= "
			<a target='_blank' href=\"ertp://nbns?prj=" . App::$Kod . "&file=$file:$this->ErrLine\">
			<img src=\"". GetImgUrl('dogru/Debug/images/netbeans.png') . "\" width=16 align=absmiddle></a>
			<a href=\"index.php?act=cisc&act2=show_code&mode=clear&code=$file:$this->ErrLine\" target=_blank>
				$full:$this->ErrLine
			</a>";
		return $hata;
	}

	public function GetTypeStr()
	{
		$dizi = array(
			E_USER_ERROR => 'ERROR',
			E_USER_NOTICE => 'NOTICE',
			E_USER_WARNING => 'WARNING'
		);
		if(isset($dizi[$this->ErrNo]))
			return $dizi[$this->ErrNo];
		else
			return "($this->ErrNo)";
	}
}