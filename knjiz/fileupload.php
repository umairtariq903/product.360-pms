<?php
function FileUpload($name, $tmp, $err)
{
	global $AppTempDir;
	// Yüklenen dosyanın bilgileri
	//Kodlama::KarakterKodlamaDuzelt($_FILES['FL_Upload']['name']);
//	$name = $_FILES['FL_Upload']['name'];
//	$tmp  = $_FILES['FL_Upload']['tmp_name'];
//	$err  = $_FILES['FL_Upload']['error'];

	if($err > 0)
		return 'Dosya yuklenemedi. Maksimum dosya yukleme boyutu = '.ini_get ('upload_max_filesize');
	// Yüklenen dosyayı temp klasörüne taşı
	$tmpDir = $AppTempDir ? $AppTempDir : AppFile::$TEMP_DIR;
	$tmpFolder = FullPath($tmpDir);
	$parts = explode('.', $name);
	$ext = strtolower(end($parts));
	$tmpFiles = DosyaSistem::getDirContents($tmpFolder, '.*', true);
	$sayac = 0;
	foreach($tmpFiles as $tmpFile)
	{
		if($sayac >= 100)
			break;
		$tarih = date("d-m-Y", filectime($tmpFile));
		if($tarih != '' && Tarih::FarkVer($tarih, Tarih::Bugun()) >= 1)
		{
			@unlink($tmpFile);
			$sayac++;
		}
	}
	if (preg_match("/(php|php4|php3|js)/", $ext))
		return 'Kötü dosya türü';

	$newName = substr(time(), -5) . '-' . substr(md5(microtime()), 0, 10) . '.' . $ext;
	if (DosyaSistem::Tasi($tmp, $tmpFolder . $newName))
	{
		$newUrl = $tmpDir . $newName;
		if (preg_match("/\.(jpe?g|gif|png)$/i", $newUrl) && isset($_POST['cropData']))
		{
			$data = json_decode($_POST['cropData']);
			$sonuc = Crop($newUrl, $newUrl, $data);
			if ($sonuc != 1)
				return $sonuc;
			// Dosya PNG oldu
			$pngUrl = preg_replace("/\.([a-z]+)/i", ".png", $newUrl);
			rename($newUrl, $pngUrl);
			$newUrl = $pngUrl;
		}
		// Resimler için maksimum 2000 px genişlik verilecek
		if(class_exists('Dosya'))
			Dosya::Cikart($newUrl)->Kucult(2000);
		// Sonucu geri döndür
		$link = str_replace("apli_dat/", "files/", "$newUrl;$name");

		$sonucObj = new stdClass();
		$sonucObj->name = $name;
		$sonucObj->url = $newUrl;
		$sonucObj->Ad = $name;
		$sonucObj->Yol = $newUrl;
		$sonucObj->Link = $link;
		return $sonucObj;
	}
	return "Dosya yüklenemedi!";
}

function Crop($src, $dst, $data) {
	$type = exif_imagetype($src);
	switch ($type) {
	  case IMAGETYPE_GIF:
		$src_img = imagecreatefromgif($src);
		break;

	  case IMAGETYPE_JPEG:
		$src_img = imagecreatefromjpeg($src);
		break;

	  case IMAGETYPE_PNG:
		$src_img = imagecreatefrompng($src);
		break;
	}

	if (!$src_img)
	  return "Kaynak resim okunamadı!";

	$size = getimagesize($src);
	$size_w = $size[0]; // natural width
	$size_h = $size[1]; // natural height

	$src_img_w = $size_w;
	$src_img_h = $size_h;

	$degrees = $data->rotate;

	// Rotate the source image
	if (is_numeric($degrees) && $degrees != 0) {
	  // PHP's degrees is opposite to CSS's degrees
	  $new_img = imagerotate( $src_img, -$degrees, imagecolorallocatealpha($src_img, 0, 0, 0, 127) );

	  imagedestroy($src_img);
	  $src_img = $new_img;

	  $deg = abs($degrees) % 180;
	  $arc = ($deg > 90 ? (180 - $deg) : $deg) * M_PI / 180;

	  $src_img_w = $size_w * cos($arc) + $size_h * sin($arc);
	  $src_img_h = $size_w * sin($arc) + $size_h * cos($arc);

	  // Fix rotated image miss 1px issue when degrees < 0
	  $src_img_w -= 1;
	  $src_img_h -= 1;
	}

	$tmp_img_w = $data->width;
	$tmp_img_h = $data->height;
	$dst_img_w = $data->width;
	$dst_img_h = $data->height;

	$src_x = $data->left;
	$src_y = $data->top;

	if ($src_x <= -$tmp_img_w || $src_x > $src_img_w) {
	  $src_x = $src_w = $dst_x = $dst_w = 0;
	} else if ($src_x <= 0) {
	  $dst_x = -$src_x;
	  $src_x = 0;
	  $src_w = $dst_w = min($src_img_w, $tmp_img_w + $src_x);
	} else if ($src_x <= $src_img_w) {
	  $dst_x = 0;
	  $src_w = $dst_w = min($tmp_img_w, $src_img_w - $src_x);
	}

	if ($src_w <= 0 || $src_y <= -$tmp_img_h || $src_y > $src_img_h) {
	  $src_y = $src_h = $dst_y = $dst_h = 0;
	} else if ($src_y <= 0) {
	  $dst_y = -$src_y;
	  $src_y = 0;
	  $src_h = $dst_h = min($src_img_h, $tmp_img_h + $src_y);
	} else if ($src_y <= $src_img_h) {
	  $dst_y = 0;
	  $src_h = $dst_h = min($tmp_img_h, $src_img_h - $src_y);
	}

	// Scale to destination position and size
	$ratio = $tmp_img_w / $dst_img_w;
	$dst_x /= $ratio;
	$dst_y /= $ratio;
	$dst_w /= $ratio;
	$dst_h /= $ratio;

	$dst_img = imagecreatetruecolor($dst_img_w, $dst_img_h);

	// Add white background to the destination image
	imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, 255, 255, 255, 0));
	imagesavealpha($dst_img, true);

	$result = imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);

	if ($result) {
	  if (!imagepng($dst_img, $dst))
		return "Kesilen resim kaydedilemedi";
	} else {
		return "Resim kesilemedi";
	}

	imagedestroy($src_img);
	imagedestroy($dst_img);

	return 1;
}

function MultiFileUpload()
{
	$sonuclar = [];
	$uploadPathDgr = "files";
	if(isset($_GET["upload_path_dgr"]))
		$uploadPathDgr = $_GET["upload_path_dgr"];
	{
		$fileCount = count($_FILES[$uploadPathDgr]['name']);

		for($i=0; $i < $fileCount; $i++)
		{
			$name = $_FILES[$uploadPathDgr]['name'][$i];
			$tmp = $_FILES[$uploadPathDgr]['tmp_name'][$i];
			$err = $_FILES[$uploadPathDgr]['error'][$i];
			$sonuclar[] = FileUpload($name, $tmp, $err);
		}
	}
	echo Kodlama::JSON($sonuclar);
	die();
}

function MergeChunked()
{
    ini_set('post_max_size', '2048M');
    ini_set('upload_max_filesize', '2048M');
    ini_set('memory_limit', '-1');

    $parts = $_POST["ChunkedParts"];

    for ($i=0; $i < count($parts) - 1; $i++)
    {
        $eklenecekUrl = DosyaSistem::RealPath($parts[$i + 1]);
        $eklenecekDosya = file_get_contents($eklenecekUrl);
        $byt = file_put_contents(DosyaSistem::RealPath($parts[0]), $eklenecekDosya, FILE_APPEND);
//        @unlink($eklenecekUrl);
    }
    DosyaSistem::Tasi(DosyaSistem::RealPath($parts[0]),DosyaSistem::RealPath($parts[count($parts) - 1]));
    for ($i=0; $i < count($parts) - 2; $i++)
        @unlink(DosyaSistem::RealPath($parts[$i]));
    echo Kodlama::JSON($parts[0]);
    die();
}
if (isset($_POST["ChunkedParts"]))
{
    MergeChunked();
}
else
    MultiFileUpload();

