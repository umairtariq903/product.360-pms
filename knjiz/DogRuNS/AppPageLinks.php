<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DogRu;

class AppPageLinkItem
{
	public static $DefaultIcon = '';
	public $id = '';
	public $orderId = '';
	public $url = '';
	public $title = '';
	public $icon = '';
	public $parent_id = '';
	public $hide = 0;

	public static function init($id, $url, $title, $icon, $parent_id)
	{
		if (!$icon)
			$icon = self::$DefaultIcon;
		$page = new AppPageLinkItem();
		$page->id = $id;
		$page->orderId = $id;
		$page->url = $url;
		$page->title = $title;
		$page->icon = $icon;
		$page->parent_id = $parent_id;
		return $page;
	}

	public function getAct($level)
	{
		$acts = explode('.', $this->url);
		return IfNull($acts, $level);
	}

	public function __get($name)
	{
		if ($name == 'act2')
			return $this->getAct(1);
	}

	public function getFullUrl()
	{
		if (preg_match('/^http/', $this->url))
			return $this->url;
		$acts = explode('.', $this->url);
		if ($acts[0])
			$acts[0] = "act=$acts[0]";
		for ($i=1; $i<count($acts); $i++)
			$acts[$i] = "act". ($i+1) ."=$acts[$i]";
		return '?' . implode('&', $acts);
	}
}

class AppPageLinks
{
	/**
	 * @var AppPageLinkItem[]
	 */
	private $list = array();

	/**
	 * @return AppPageLinks
	 */
	public static function get()
	{
		static $pl = NULL;
		return $pl ? $pl : ($pl = new AppPageLinks);
	}

	public static function getParentId($id)
	{
		$pid = '';
		$pos = strpos($id, '0');
		$len = strlen($id);
		if ($pos === false)
			$pos = $len;
		if ($pos > 1)
			$pid = substr($id, 0, $pos - 1);
		if ($pos > 2)
			$pid = str_pad($pid, $len, '0');
		return $pid == 'P' ? '' : $pid;
	}

	/**
	 * @return AppPageLinks
	 */
	public function setDefaultIcon($icon)
	{
		AppPageLinkItem::$DefaultIcon = $icon;
		return $this;
	}

	/**
	 * @return AppPageLinks
	 */
	public function add($id, $title, $url = '', $icon = '')
	{
		$page = AppPageLinkItem::init($id, $url, $title, $icon, self::getParentId($id));
		$this->list[$page->orderId] = $page;
		return $this;
	}

	/**
	 * @return AppPageLinkItem
	 */
	public function addNext($beforeId, $title, $url = '', $icon = '')
	{
		$beforePage = $this->getById($beforeId);
		if (! $beforePage)
			ThrowException ("Önceki sayfa linki bulunamadı. ($beforeId)");
		// Sonraki Idler al
		ksort($this->list);
		$orderedIds = array();
		foreach($this->list as $id => $pg)
			if ($id > $beforePage->orderId && $beforePage->parent_id == $pg->parent_id)
				$orderedIds[] = $pg->id;

		$pid = $beforePage->parent_id;
		$page = $this->addByTitle($pid, $title, $url, $icon);
		array_unshift($orderedIds, $page->id);
		self::reOrder($orderedIds);
		return $page;
	}

	public function getIdByTitle($parentId, $title)
	{
		$itm = \ArrayLib::SearchObj($this->list, "parent_id=$parentId,title=$title");
		if ($itm)
			return $itm->id;
		return '';
	}

	public function getNextChildId($pid, $field = 'id')
	{
		$newId = $pid;
		// En büyük child ı bul
		foreach($this->list as $itm)
			if (self::getParentId($itm->{$field}) == $pid && $newId < $itm->{$field})
				$newId = $itm->{$field};
		// 0 dan önceki son karakteri al
		$i = strpos($newId, '0') - 1;
		// eğer 0 yoksa en son karakteri al
		if ($i < 0)
			$i = strlen($newId) - 1;
		// Eğer daha önce hiç child yoksa ve sonda 0 olan yer varsa ilk 0 olduğu
		// yeri hesapla
		if ($newId == $pid && $i < strlen($newId))
			$i++;
		// hesaplanan yerin değerini bir arttır
		$newId[$i] = chr(ord($newId[$i]) + 1);
		if ($newId[$i] == ':')
			$newId[$i] = 'A';
		return $newId;
	}

	/**
	 * @return AppPageLinkItem
	 */
	public  function addByTitle($parentId, $title, $url = '', $parentTitle = '')
	{
		$pid = $parentId;
		if ($parentTitle)
		{
			$pid = $this->getIdByTitle($parentId, $parentTitle);
			if (!$pid)
				$pid = $this->addByTitle($parentId, $parentTitle)->id;
		}
		$newId = $this->getNextChildId($pid);
		$page = AppPageLinkItem::init($newId, $url, $title, '', $pid);
		$parent = $this->getById($pid);
		$page->orderId = $this->getNextChildId($parent->orderId, 'orderId');
		$this->list[$page->orderId] = $page;
		return $page;
	}

	/**
	 * @return AppPageLinkItem[]
	 */
	public function getList()
	{
		$list = array();
		$regs = array();
		ksort($this->list);
		foreach($this->list as $key => $pg)
		{
			if ($pg->hide)
				continue;
			if (preg_match('/#(.*)/', $pg->title, $regs))
			{
				$title = Trans($regs[1]);
				if ($title)
					$pg->title = $title;
			}
			$list[$key] = $pg;
		}
		return $list;
	}

	public function echoTreeViewList()
	{
		echo "<pre>";
		foreach($this->getList() as $pg)
		{
			$tabCnt = strpos($pg->orderId, '0');
			if ($tabCnt === FALSE)
				$tabCnt = strlen ($pg->orderId);
			$tabCnt -= 2;
			echo str_pad("", $tabCnt*4, " ") . "$pg->id => $pg->title ($pg->url, $pg->orderId)\n";
		}
		echo "</pre>";
	}

	/**
	 * id si verilen sayfayı ve alt sayfalarını siler
	 * @param string $id
	 */
	public function del($id)
	{
		$ids = explode(',', $id);
		if (count($ids) > 1)
		{
			foreach($ids as $i)
				self::del($i);
			return;
		}
		$pattern = preg_replace('/[0]+$/', '(.+)', $id);
		foreach($this->list as $key => $p)
			if (preg_match("/^$pattern/", $p->id))
				unset($this->list[$key]);
	}

	/**
	 * @return AppPageLinkItem[]
	 */
	public function getByActLevel($act, $level = 0)
	{
		$array = array();
		foreach($this->list as $key => $p)
		{
			$acts = explode('.', $p->url);
			if (@$acts[$level] == $act)
				$array[] = $this->list[$key];
		}
		return $array;
	}

	public function delByActLevel($act, $level = 0)
	{
		foreach($this->list as $key => $p)
		{
			$url = explode('&', $p->url);
			$url = $url[0];
			$acts = explode('.', $url);
			if (@$acts[$level] == $act)
				unset($this->list[$key]);
		}
	}

	public function checkModules()
	{
		// Aktif olmayan modüller
		foreach(\DogRu\AppModules::GetModules() as $key => $modul)
			if (! $modul->IsAktif())
			{
				$this->delByActLevel ($key, 1);
				$this->delByActLevel ($key, 2);
			}
	}

	public function checkAllLeaf($showLeaves)
	{
		foreach($this->list as $pg)
		{
			if (! $pg->url)
				continue;
			$parent = $this->getById($pg->parent_id);
			if (!$parent || !$parent->url)
				continue;
			if ($showLeaves)
				$parent->url = '';
			else
				$pg->hide = 1;
		}
	}

	/**
	 * Verilen idleri sıralaması düzenler
	 * @param string $ids
	 */
	public static function reOrder($ids)
	{
		$pl = self::get();
		if (is_string($ids))
			$ids = explode(',', $ids);
		// değiştirme listesini oluştur
		$sortedIds = array();
		foreach($ids as $id)
			$sortedIds[] = $pl->getById($id)->orderId;
		sort($sortedIds, SORT_STRING);
		$newIds = array();
		for($i=0; $i<count($ids); $i++)
			$newIds[$ids[$i]] = $sortedIds[$i];
		$list = $pl->list;
		$rLeng = -1;
		foreach($newIds as $old => $new)
		{
			$pattern = preg_replace('/[0]+$/', '(.+)', $old);
			foreach($list as $page)
			{
				$regs = array();
				if (preg_match("/^$pattern/", $page->id, $regs))
				{
					$r = IfNull($regs, 1, $new);
					if ($rLeng < 0)
						$rLeng = strlen ($r);
					else if ($rLeng != strlen($r))
						ThrowException('değiştirilmek istenen AppPageLinks elemanları aynı uzunlukta değil');
					$page->orderId = substr($new, 0, -strlen($r)) . $r;
				}
			}
		}
		$pl->list = array();
		foreach($list as $page)
			$pl->list[$page->orderId] = $page;
		ksort($pl->list);
	}

	public function moveBefore($id, $afterId)
	{
		$start = $this->getById($id);
		$end = $this->getById($afterId);
		if (!$start || !$end || $start->parent_id != $end->parent_id)
			return;
		$order = array();
		foreach($this->list as $pg)
		{
			if ($pg->parent_id != $start->parent_id)
				continue;
			if ($pg->id == $afterId)
				$order = array($id, $afterId);
			elseif ($pg->id == $id)
				break;
			elseif ($order)
				$order[] = $pg->id;
		}
		self::reOrder($order);
	}

	/**
	 * @return AppPageLinkItem
	 */
	public function getById($id)
	{
		if (!$id)
			return null;
		return \ArrayLib::SearchObj($this->list, array('id' => $id));
	}
}
