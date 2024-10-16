<?php
class ImageEdit
{
	var $extension;

	public function __construct($extension)
	{
		$this->extension = strtoupper($extension);
	}

	function generateThumbVersion($fileUrl, $newFileUrl, $newWidth = 100)
	{
		// Yeni boyutlardan yüksekliği hesaplıyoruz
		list($oldWidth, $oldHeight, $dummy, $dummy) = getimagesize($fileUrl);
		// Boyutlar büyük değilse olduğu gibi kaydet
		if ($oldWidth <= $newWidth)
		{
			if ($fileUrl != $newFileUrl && file_exists($fileUrl))
				copy($fileUrl, $newFileUrl);
			return true;
		}

		// Oranı bul ve yüksekliğe uygula
		$perc 		= $newWidth / (double)$oldWidth;
		$newHeight	= $oldHeight * $perc;

		if ($this->extension == 'JPG' || $this->extension == 'JPEG')
			$this->jpgThumbVersion($fileUrl, $newFileUrl, $oldWidth, $oldHeight, $newWidth, $newHeight);
		else if ($this->extension == 'GIF')
			$this->gifThumbVersion($fileUrl, $newFileUrl, $oldWidth, $oldHeight, $newWidth, $newHeight);
		else if ($this->extension == 'PNG')
			$this->pngThumbVersion($fileUrl, $newFileUrl, $oldWidth, $oldHeight, $newWidth, $newHeight);
	}

	function jpgThumbVersion($fileUrl, $newFileUrl, $oldWidth, $oldHeight, $newWidth, $newHeight)
	{
		$image_p = imagecreatetruecolor($newWidth, $newHeight);
		$image = imagecreatefromjpeg($fileUrl);

		imagecopyresampled($image_p, $image, 0, 0, 0, 0,
			$newWidth, $newHeight,
			$oldWidth, $oldHeight);

		imagejpeg($image_p, $newFileUrl);
		imagedestroy($image_p);
	}

	function gifThumbVersion($fileUrl, $newFileUrl, $oldWidth, $oldHeight, $newWidth, $newHeight)
	{
		$image_p = imagecreatetruecolor($newWidth, $newHeight);
		$image = imagecreatefromgif($fileUrl);

		imagecopyresampled($image_p, $image, 0, 0, 0, 0,
			$newWidth, $newHeight,
			$oldWidth, $oldHeight);

		imagegif($image_p, $newFileUrl);
		imagedestroy($image_p);
	}

	function pngThumbVersion($fileUrl, $newFileUrl, $oldWidth, $oldHeight, $newWidth, $newHeight)
	{
		$image_p = imagecreatetruecolor($newWidth, $newHeight);
		$image = imagecreatefrompng($fileUrl);

		imagecopyresampled($image_p, $image, 0, 0, 0, 0,
			$newWidth, $newHeight,
			$oldWidth, $oldHeight);

		imagepng($image_p, $newFileUrl);
		imagedestroy($image_p);
	}

	public static function webThumbPng($url, $file)
	{
		$Browser = new COM('InternetExplorer.Application');
		$Browserhandle = $Browser->HWND;
		$Browser->Visible = true;
		$Browser->Fullscreen = true;
		$Browser->Navigate($url);

		while($Browser->Busy)
		  com_message_pump(1000);

		$img = imagegrabwindow($Browserhandle, 0);
		$Browser->Quit();
		imagepng($img, $file);
	}

}// class