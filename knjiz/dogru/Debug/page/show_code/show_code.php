<?php
class DebugShowCodePage extends PageController
{
	public function Index()
	{
		$file = explode(':', $_GET['code']);
		if(preg_match('/config/i', $file[0]))
			return $this->ShowError('Dosya bulunamadı');
		$this->Code = file_get_contents(FullPath($file[0]));
		$this->Line = $file[1] - 1;
	}
}
?>
