<?php
class SitemapUrl
{
	public $loc;
	public $priority;
	public $lastmod;
	public $changefreq;
}
class Sitemap
{
	/**
	 * Nesneleri serileştirirken default alanların yazılmaması sağlanır.
	 */
	public $Compress = false;

	private static $IgnrChars = array("&", "\n", ">", "<");
	private static $AltrChars = array("&amp;", "&#xA;", "&gt;", "&lt;");

	public static function Generate($urlList)
	{
		$sitemap = new Sitemap();

		$content = "<?xml version=\"1.0\" encoding=\"utf-8\" standalone=\"yes\"?>";
		$content.= "\n".'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">';
		$content.= $sitemap->SerializeItem("urlset", $urlList);
		$content.= "\n</urlset>";
		if (App::IsUTF8())
			$content = Kodlama::UTF8($content);

		return $content;
	}

	private function SerializeItem($name, $item, $tabs = "")
	{
		if(is_object($item))
		{
			$sonuc = "\n$tabs<$name>";
			if(isset($item->_SleepFields))
				$props = $item->_SleepFields;
			else
				$props = array_keys(get_object_vars($item));
			$obj = null;
			foreach($props as $p)
			{
				$val = $item->{$p};
				if($this->Compress)
				{
					if(! $obj)
						$obj = ObjectLib::InitNew(get_class($item));
					$default = @$obj->{$p};
					if($default == $val && !is_a($obj, 'stdClass'))
						continue;
				}
				$sonuc .= $this->SerializeItem($p, $item->{$p}, "$tabs\t");
			}
			$sonuc .= "\n$tabs</$name>";
		}
		elseif(is_array($item))
		{
			$sonuc = "";
			foreach($item as $key => $value){
				$key = "url";
				$sonuc .= $this->SerializeItem($key, $value, "$tabs\t");
			}
		}
		else
			$sonuc = "\n$tabs<$name>" . str_replace(self::$IgnrChars, self::$AltrChars, $item) . "</$name>";
		return $sonuc;
	}
}