<?php
class XML_Serialize
{
	/**
	 * Nesneleri serileştirirken default alanların yazılmaması sağlanır.
	 */
	public $Compress = false;

	private static $IgnrChars = array("&", "\n", ">", "<");
	private static $AltrChars = array("&amp;", "&#xA;", "&gt;", "&lt;");

	public function Serialize($name, $item)
	{
		$content = "<?xml version=\"1.0\" encoding=\"utf-8\" standalone=\"yes\"?>";
		$content.= $this->SerializeItem($name, $item);
		if (App::IsUTF8())
			$content = Kodlama::UTF8($content);
		return $content;
	}

	private function SerializeItem($name, $item, $tabs = "")
	{
		if(is_object($item))
		{
			$sonuc = "\n$tabs<$name type=\"" . get_class($item) . "\">";
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
			$sonuc = "\n$tabs<$name type=\"array\">";
			foreach($item as $key => $value){
				$key = "i_$key";
				$sonuc .= $this->SerializeItem($key, $value, "$tabs\t");
			}
			$sonuc .= "\n$tabs</$name>";
		}
		else
			$sonuc = "\n$tabs<$name>" . str_replace(self::$IgnrChars, self::$AltrChars, $item) . "</$name>";
		return $sonuc;
	}

	public static function Unserialize(&$item, SimpleXMLElement $xml)
	{
		foreach($xml as $key => $value)
		{
			if($value['type'] == '')
			{
				$val = str_replace(self::$AltrChars, self::$IgnrChars, $value);
				$val = str_replace('|#|', "\n", $val);
			}
			else if($value['type'] == 'array')
			{
				$val = array();
				self::Unserialize($val, $value);
			}
			else
			{
				$class = (string) $value['type'];
				$val = new $class;
				self::Unserialize($val, $value);
			}
			if(is_array($item))
			{
				$key = substr($key, 2);
				$item[$key] = $val;
			}
			else
				$item->{$key} = $val;
		}
	}

	public static function SerializeObj($item, $baslik = "root")
	{
		$xml = new XML_Serialize();
		return $xml->Serialize($baslik, $item);
	}
}

