<?php
class DebugDetayPage extends PageController
{
	public function Index()
	{
		if(@$_GET['t'] == 'err')
		{
			$errs = PhpErrorList::Get()->List;
			foreach($errs as $e)
				if ($e->Id == $_GET['id'])
				{
					$this->err = $e;
					break;
				}
			if(! $this->err)
				return $this->ShowError('Ayrıntı bilgisine ulaşılamadı. Lütfen sayfayı yenileyin');
			$this->SetTemplateUri('error.tpl');
		}else{
			$dbg = Debug::GetInstance();
			foreach($dbg->Logs as $log)
				if ($log->Id == $_GET['id'])
				{
					$this->detay = $log;
					break;
				}
			if(! $this->detay)
				return $this->ShowError('Ayrıntı bilgisine ulaşılamadı. Lütfen sayfayı yenileyin');
			if(IsSerialized($this->detay->LongDesc))
				$this->params = mb_unserialize ($this->detay->LongDesc);
		}
	}
}
?>
