<?php
/**
 * @property int $Day Day of month
 * @property int $Month
 * @property int $Year
 * @property int $DayOfWeek
 */
class TarihObj extends DateTime
{
	public function __get($name)
	{
		$method = "get$name";
		if (method_exists($this, $method))
			return call_user_method($method, $this);
		return NULL;
	}

	public function __toString()
	{
		return $this->ToString();
	}

	public function getDay()
	{
		return (int) $this->format('d');
	}

	public function getDayOfWeek()
	{
		return (int) $this->format('N');
	}

	public function getMonth()
	{
		return (int) $this->format('m');
	}

	public function getMonthFullName()
	{
		return (int) $this->format('F');
	}

	public function getTerm()
	{
		return $this->format('Ym');
	}

	public function getTermFull()
	{
		return $this->ToLocalString('%B %Y');
	}

	public function getYear()
	{
		return (int) $this->format('Y');
	}

	/**
	 * Ay başına setler
	 */
	public function setMonthBegin()
	{
		return $this->addDay(-$this->getDay() + 1);
	}

	/**
	 * Ay başına setler
	 */
	public function setWeekBegin()
	{
		return $this->addDay(-$this->getDayOfWeek() + 1);
	}

	public function addDay($nDay = 1)
	{
		$inv = $nDay < 0;
		$dint = new DateInterval('P' . abs($nDay) . 'D');
		$dint->invert = $inv;
		return $this->add($dint);
	}

	/**
	 * @return TarihObj
	 */
	public function addMonth($n = 1)
	{
		$inv = $n < 0;
		$dint = new DateInterval('P' . abs($n) . 'M');
		$dint->invert = $inv;
		return $this->add($dint);
	}

	/**
	 * @return TarihObj
	 */
	public function getMonthEnd()
	{
		$tarih = new TarihObj();
		$tarih->copy($this);
		$dint = new DateInterval('P1M');
		return $tarih->add($dint)->addDay(-1);
	}

	public function ToString($includeTime = FALSE)
	{
		return $this->format('d-m-Y' . ($includeTime ? ' H:i:s' : ''));
	}

	public function ToLocalString($format)
	{
		return Kodlama::UTF8(strftime($format, $this->getTimestamp()));
	}

	public function ToLocalShortMonthString()
	{
		return $this->ToLocalString('%b %Y');
	}

	public function ToLocalShortString()
	{
		return $this->ToLocalString('%d %b %a');
	}

	public function ToLocalLongString()
	{
		return $this->ToLocalString('%d %B %A');
	}

	public function ToMySqlString($includeTime = FALSE)
	{
		return $this->format('Y-m-d' . ($includeTime ? ' H:i:s' : ''));
	}

	/**
	 * @param TarihObj $dateObj
	 * @return TarihObj
	 */
	public function copy($dateObj)
	{
		return $this->setTimestamp($dateObj->getTimestamp());
	}
}

class Tarih
{
	public static $Aylar = array('Ocak', 'Şubat', 'Mart', 'Nisan', 'Mayıs'
		, 'Haziran', 'Temmuz', 'Ağustos', 'Eylül', 'Ekim', 'Kasım', 'Aralık');
    public static $TumAylar = array('1' => 'Ocak', '2' => 'Şubat', '3' => 'Mart',
        '4' => 'Nisan', '5' => 'Mayıs', '6' => 'Haziran', '7' => 'Temmuz',
        '8' => 'Ağustos', '9' => 'Eylül', '10' => 'Ekim', '11' => 'Kasım', '12' => 'Aralık');
    public static $TumAylarEn = array('1' => 'January', '2' => 'February', '3' => 'March',
        '4' => 'April', '5' => 'May', '6' => 'June', '7' => 'July',
        '8' => 'August', '9' => 'September', '10' => 'October', '11' => 'November', '12' => 'December');
	public static $KisaAylar = array('Oca', 'Şub', 'Mar', 'Nis', 'May'
		, 'Haz', 'Tem', 'Ağu', 'Eyl', 'Eki', 'Kas', 'Ara');

	public static $Gunler = array(
		1 => 'Pazartesi',
		2 => 'Salı',
		3 => 'Çarşamba',
		4 => 'Perşembe',
		5 => 'Cuma',
		6 => 'Cumartesi',
		7 => 'Pazar');
	public static $HaftaIciGunler = array(
		1 => 'Pazartesi',
		2 => 'Salı',
		3 => 'Çarşamba',
		4 => 'Perşembe',
		5 => 'Cuma'
	);
	public static $GunlerKisa = array('Pts', 'Sal', 'Çar', 'Per', 'Cum', 'Cts', 'Paz');

	public static function StrToTime($tarih)
	{
		$p1 = preg_split("/[^0-9]/", Tarih::ToNormalDate($tarih));
		$gun1 = (int)$p1[0];
		$ay1  = (int)$p1[1];
		$yil1 = (int)$p1[2];
		$saat1= (int)@$p1[3];
		$dakika1= (int)@$p1[4];
		$saniye1= (int)@$p1[5];
		return mktime($saat1, $dakika1, $saniye1, $ay1, $gun1, $yil1);
	}

	/**
	 * Verilen iki tarih arasındaki farkı istenen cinsten verir
	 * (AY, GÜN, YIL olarak)
	 * Ör: FarkVer('10-01-2013', '11-01-2013')
	 *     Sonuç: "1"
	 */
	public static function FarkVer($bas, $bit, $tur = 'GÜN',$floor = true)
	{
		$time1 = self::StrToTime($bas);
		$time2 = self::StrToTime($bit);
		if ($tur == 'YIL')
			$kat = 365.25;
		else if ($tur == 'AY')
			$kat = 30.5;
		else if ($tur == 'GÜN' || $tur == 'GUN')
			$kat = 1.0;
		else if($tur == 'SAAT')
			$kat=1/24;
		else if($tur == 'DAKIKA')
			$kat=1/(24*60);
		else
			$kat = 365.25;

		$diff = ($time2 - $time1 ) / (60 * 60 * 24 * $kat);

		return $floor ? floor($diff) : ceil($diff);
	}

	/**
	 * Verilen tarihe kaç gün kaldığını verir, geçmiş ise negatif döndürür
	 * @return int
	 */
	public static function KalanGun($date)
	{
		return self::FarkVer(self::Bugun(), $date);
	}

	public static function BugunFarkStr($date)
	{
		$date = self::GetDate($date);
		$diff = self::FarkVer($date, Tarih::Bugun());
		$next = $diff < 0;
		$suffix = $next ? 'sonra' : 'önce';
		$diff = abs($diff);
		if ($diff < 1)
			return 'Bugün';
		else if ($diff < 2)
			return $next ? 'Yarın' : 'Dün';
		else if ($diff <= 7)
			return "$diff gün $suffix";
		return $date;
	}

	public static function SimdiFarkStr($verilenTarih)
	{
        // Belirli bir tarih ve saat
        $verilenTarihObjesi = new DateTime($verilenTarih);

        // Şu anki tarih ve saat
        $suAn = new DateTime();

        // Farkı hesapla
        $fark = $suAn->diff($verilenTarihObjesi);

        // Okunabilir formata çevir
        $yazdir = '';

        if ($fark->y > 0) {
            $yazdir .= $fark->y . ' yıl ';
        }
        if ($fark->m > 0) {
            $yazdir .= $fark->m . ' ay ';
        }
        if ($fark->d > 0) {
            $yazdir .= $fark->d . ' gün ';
        }
        if ($fark->h > 0) {
            $yazdir .= $fark->h . ' saat ';
        }
        if ($fark->i > 0) {
            $yazdir .= $fark->i . ' dakika ';
        }
        if ($fark->s > 0) {
            $yazdir .= $fark->s . ' saniye ';
        }

        if (empty($yazdir)) {
            return 'Şimdi';
        } else {
            return $yazdir . 'önce';
        }
	}

	public static function TahminiFarkStr($verilenTarih)
	{
        // Belirli bir tarih ve saat
        $verilenTarihObjesi = new DateTime($verilenTarih);

        // Şu anki tarih ve saat
        $suAn = new DateTime();

        // Farkı hesapla
        $fark = $suAn->diff($verilenTarihObjesi);

        // Okunabilir formata çevir
        $yazdir = '';

        if ($fark->y > 0) {
            $yazdir = $fark->y . ' yıl ';
            return $yazdir;
        }
        if ($fark->m > 0) {
            $yazdir = $fark->m . ' ay ';
            return $yazdir;
        }
        if ($fark->d > 0) {
            $yazdir = $fark->d . ' gün ';
            return $yazdir;
        }
        if ($fark->h > 0) {
            $yazdir = $fark->h . ' saat ';
            return $yazdir;
        }
        if ($fark->i > 0) {
            $yazdir = $fark->i . ' dk ';
            return $yazdir;
        }
        if ($fark->s > 0) {
            $yazdir = $fark->s . ' sn ';
            return $yazdir;
        }

        return 'Şimdi';
	}

	/**
	 * dd-mm-yyyy formatında verilen tarihe x kadar ay
	 * ekleyerek geriye aynı formatta bir string döndürür
	 */
	public static function AyEkle($tarihStr, $ayEk)
	{
		$parts = preg_split("/[^0-9]/", Tarih::ToNormalDate($tarihStr));
		$gun = (int)$parts[0];
		$ay  = (int)$parts[1];
		$yil = (int)$parts[2];

		$time= mktime(0, 0, 0, $ay + $ayEk, $gun , $yil);
		return date('d-m-Y', $time);
	}
	/**
	 * dd-mm-yyyy formatında verilen tarihe x kadar ay
	 * ekleyerek geriye aynı formatta bir string döndürür
	 */
	public static function YilEkle($tarihStr, $yilEk, $mysqlDate = false)
	{
		if(self::IsMySqlDate($tarihStr))
			$mysqlDate = true;
		$parts = preg_split("/[^0-9]/", Tarih::ToNormalDate($tarihStr));
		$gun = (int)$parts[0];
		$ay  = (int)$parts[1];
		$yil = (int)$parts[2];

		$time= mktime(0, 0, 0, $ay, $gun , $yil+$yilEk);
		$sonuc = date('d-m-Y', $time);
		if($mysqlDate)
			return self::ToMysqlDate ($sonuc);
		return $sonuc;
	}

	/**
	 * dd-mm-yyyy veya yyyy-mm-dd formatında verilen tarihe x kadar gün ekleyerek
	 * geriye aynı formatta bir string döndürür
	 */
	public static function GunEkle($tarihStr, $gunEk)
	{
		$isMysqlDate = self::IsMySqlDate($tarihStr);
		$tarihStr = Tarih::ToMysqlDate($tarihStr);
		$phpdate = strtotime($tarihStr);
		$phpdate = strtotime("$gunEk Day", $phpdate);
		$format = $isMysqlDate ? 'Y-m-d' : 'd-m-Y';
		if (strpos($tarihStr, ':'))
			$format .= ' H:i:s';
		return date($format, $phpdate);
	}

	/**
	 * Verilen tarih string'ini mysql formatında dönüştürür
	 */
	public static function ToMysqlDate($dateStr, $separator='-')
	{
		// Zaten mysql date formatında olan bir tarih stringini
		// olduğu gibi geri döndür
		if (self::IsMySqlDate($dateStr, $separator))
			return $dateStr;

		// Standart tarih formatında olmayan bir tarihi
		// 00-00-0000 olarak al
		if (! self::IsNormalDate($dateStr, $separator))
			$dateStr = '0000-00-00';

		// Tarihin saat kısmı varsa, dokunmayalım
		$tarihSaat = explode(' ', trim($dateStr));

		// Tarih stringini mysql formatında oluştur
		// Format: yyyy-aa-gg
		$parts = explode($separator, $tarihSaat[0]);
		return "$parts[2]-$parts[1]-$parts[0]" . (count($tarihSaat) > 1 ? " $tarihSaat[1]" : "");
	}

	/**
	 * Verilen tarihin gün ve ay bilgilerini iki haneli yapar
	 * @param string $dateStr
	 */
	public static function FormatDigits($dateStr, $separator = '-')
	{
		$day = str_pad(self::GetDay($dateStr), 2, '0', STR_PAD_LEFT);
		$month= str_pad(self::GetMonth($dateStr), 2, '0', STR_PAD_LEFT);
		$year = self::GetYear($dateStr);

		if (self::IsNormalDate($dateStr, $separator))
			return $day . $separator . $month . $separator . $year;
		return $year . $separator . $month . $separator . $day;
	}

	/**
	 * Verilen tarih ve öncesindeki ilk çalışma gününün tarihini verir. Resmi bayramlar hariç.
	 * @param type $date
	 * @return type
	 */
	public static function GetFirstWorkDay($date)
	{
		$isMysqlDate = self::IsMySqlDate($date);
		$date = self::FormatDigits($date);
		if (! $isMysqlDate)
			$date = self::ToMysqlDate($date);
		while(true)
		{
			if (preg_match("/[0-9]{4}\-(01-01)|(04-23)|(05-19)|(10-29)|(09-30)$/", $date))
			{
				$date = self::GunEkle($date, -1);
				continue;
			}
			$weekDay = date('N', strtotime($date));
			if ($weekDay > 5)
			{
				$date = self::GunEkle($date, -($weekDay - 5));
				continue;
			}
			break;
		}
		if ($isMysqlDate)
			return $date;
		return self::ToNormalDate($date);
	}

	public static function GetPreMonday($date = '')
	{
		if (!$date)
			$date = self::Bugun();
		$isMysqlDate = self::IsMySqlDate($date);
		$date = self::ToMysqlDate($date);
		$weekDay = date('N', strtotime($date));
		$date = self::GunEkle($date, -$weekDay + 1);
		if(! $isMysqlDate)
			$date = self::ToNormalDate($date);
		return $date;
	}

	//-----------------------------------------------------
	// Verilen tarih string'ini mysql formatından
	// alarak normal formata dönüştürür
	//-----------------------------------------------------
	public static function ToNormalDate($dateStr, $separator='-', $includeTime = true, $outputSeparator = '-')
	{
		// Zaten normal date formatında olan bir tarih stringini
		// olduğu gibi geri döndür
		if (self::IsNormalDate($dateStr, $separator))
			return str_replace($separator, $outputSeparator, $dateStr);

		// Mysql tarih formatında olmayan bir tarihi
		// 00-00-0000 olarak al
		if (! self::IsMySqlDate($dateStr, $separator))
			return '00'. $outputSeparator .  '00' . $outputSeparator . '0000';

		// Tarih stringini mysql formatında oluştur
		// Format: yyyy-aa-gg
		$dateTime = explode(' ', trim($dateStr));
		$parts = explode($separator, $dateTime[0]);
		return @$parts[2]. $outputSeparator. @$parts[1]. $outputSeparator . @$parts[0] .
			($includeTime && count($dateTime) > 1 ? " $dateTime[1]": "");
	}

	public static function SonYillar($adet = 5, $aralik = false)
	{
		$yil = (int)date('Y');

		// Son beş yılın tarihlerini string olarak birleştir
		if ($aralik)
			$sonYillar = ($yil - $adet) . '-' . ($yil - 1);
		else
		{
			$sonYillar = array();
			for($i = $yil - $adet; $i <= $yil; $i++)
				$sonYillar[] = $i;
		}
		return $sonYillar;
	}

	public static function YilVer()
	{
		$yil = (int)date('Y');
		$OnYil = array();
		for($i=$yil - 5; $i<=$yil + 5; $i++)
			$OnYil[]= $i;
		return $OnYil;
	}

	public static function Bugun($asMysqlDate = false)
	{
		return $asMysqlDate ? date('Y-m-d') : date('d-m-Y');
	}

	public static function Simdi($asMysqlDate = false)
	{
		return $asMysqlDate ? date('Y-m-d H:i:s') : date('d-m-Y H:i:s');
	}

	public static function Yas($basTarih, $curDate = null)
	{
		if(!$curDate)
			$curDate = Tarih::Bugun();
		if(! Tarih::IsDate($basTarih))
			return NULL;
		return self::FarkVer($basTarih, $curDate, 'YIL');
	}

	/**
	 * Verilen tarihin içinde bulunduğu pazartesini verir,
	 * Not: pazartesi günü verilirse aynı günü geri dönderir.
	 * @param string $tarih MySQL Date formatında tarih
	 */
	public static function Pazartesi($tarih)
	{
		$isNormalDate = self::IsNormalDate($tarih);
		$tarih = self::ToMysqlDate($tarih);
		$tarih =  date('Y-m-d', strtotime('last sunday +1 day', strtotime($tarih)));
		if ($isNormalDate)
			return self::ToNormalDate($tarih);
		return $tarih;
	}

	/**
	 * Verilen tarihin içinde bulunduğu Cuma gününü verir,
	 * Not: pazartesi günü verirlirse aynı günü geri dönderir.
	 * @param string $tarih MySQL Date formatında tarih
	 */
	public static function Cuma($tarih)
	{
		return self::HaftaGunuVer($tarih, 5);
	}

	public static function Pazar($tarih)
	{
		return self::HaftaGunuVer($tarih, 7);
	}

	private static function HaftaGunuVer($tarih, $gun)
	{
		$isNormalDate = self::IsNormalDate($tarih);
		$tarih = self::ToMysqlDate($tarih);
		$tarih =  date('Y-m-d', strtotime('last sunday +' . $gun. ' day', strtotime($tarih)));
		if ($isNormalDate)
			return self::ToNormalDate($tarih);
		return $tarih;
	}
	/**
	 * Verilen tarihin içinde bulunduğu ayın ilk gününü verir,
	 * @param string $tarih MySQL veya Normal formatta tarih
	 * @return string
	 */
	public static function AyBasi($tarih = '')
	{
		if (!$tarih)
			$tarih = self::Bugun();
		$normal = self::IsNormalDate($tarih);
		$tarih = self::ToMysqlDate($tarih);
		$tarih = preg_replace("#([0-9]{4})-([0-9]{1,2})-([0-9]{1,2})#", '$1-$2-01', $tarih);
		if ($normal)
			$tarih = self::ToNormalDate ($tarih);
		return $tarih;
	}

	/**
	 * Verilen tarihin bulunduğu ayın son gününü verir
	 * @param string $tarih MySQL veya Normal formatta tarih
	 * @return string
	 */
	public static function AySonu($tarih = '')
	{
		if (!$tarih)
			$tarih = self::Bugun();
		$normal = self::IsNormalDate($tarih);
		$tarih = self::ToMysqlDate($tarih);
		$tarih = date("t-m-Y", strtotime($tarih));
		if (! $normal)
			$tarih = self::ToMysqlDate($tarih);
		return $tarih;
	}

	/**
	 * Verilen tarihin içinde bulunduğu yılın ilk gününü verir,
	 * @param string $tarih MySQL Date formatında tarih
	 */
	public static function YilBasi($tarih,$MysqlDate = FALSE)
	{
		$isMysqlDate = self::IsMySqlDate($tarih);
		$tarih = self::ToMysqlDate($tarih);
		$tarih = date('Y-m-d', strtotime('1/1', strtotime($tarih)));
		if(! $isMysqlDate)
			$tarih = self::ToNormalDate($tarih);
		if($MysqlDate)
			$tarih = self::ToMysqlDate($tarih);
		return $tarih;
	}

	/**
	 * verilen date değişkeninin 01-12-2010 formatında olup olmadığını kontrol eder
	 */
	public static function IsNormalDate($date, $separator='-')
	{
		return preg_match("#[0-9]{1,2}". $separator . "[0-9]{1,2}". $separator . "[0-9]{4}#", $date);
	}

	/**
	 * verilen date değişkeninin 2010-12-01 formatında olup olmadığını kontrol eder
	 */
	public static function IsMySqlDate($date, $separator='-')
	{
		return preg_match("#[0-9]{4}\\" . $separator . "[0-9]{1,2}\\" . $separator . "[0-9]{1,2}#", $date);
	}

	public static function GetDate($str)
	{
		$parts = explode(' ', $str);
		return $parts[0];
	}

	public static function GetTime($str)
	{
		$parts = explode(' ', $str);
		$parts2 = explode(':', $parts[1]);
		$time = $parts2[0].':'.$parts2[1];
		return $time;
	}

	public static function IsDate($date)
	{
		return !preg_match('/0000/', $date) && (self::IsNormalDate($date) || self::IsMySqlDate($date));
	}

	public static function IsBetween($date, $startDate, $endDate)
	{
		$fark1 = self::FarkVer($startDate, $date);
		$fark2 = self::FarkVer($date, $endDate);
		return $fark1 >= 0 && $fark2 >= 0;
	}

	/**
	 * Verilen iki tarih aralığının çakışan günleri olup olmadığına bakar
	 * @param date $start1
	 * @param date $end1
	 * @param date $start2
	 * @param date $end2
	 */
	public static function HasCommonDays($start1, $end1, $start2, $end2)
	{
		$days = 0;
		while(self::Compare($start1, $end1, '<='))
		{
			if (self::IsBetween($start1, $start2, $end2))
				$days++;
			$start1 = self::GunEkle($start1, 1);
		}
		return $days;
	}

	public static function IsEqual($date1, $date2)
	{
		return self::FarkVer($date1, $date2) < 1;
	}

	public static function IsValidDate($date)
	{
		if (!self::IsDate($date))
			return false;
		$date = explode('-', self::GetDate(self::ToNormalDate($date)));
		return checkdate(intval($date[1]), intval($date[0]), intval($date[2])) && $date[2] > 1900;
	}

	/**
	 * Verilen metni MySql date formatında döndürür
	 * Desteklenen formatlar
	 * - yyyy-mm-dd
	 * - dd-mm-yyyy
	 * - dd-ay adı-yyyy
	 */
	public static function StrToDate($str, $separator = '-')
	{
		$parts = explode($separator, $str);
		if (in_array($parts[1], Tarih::$Aylar))
			$parts[1] = array_search($parts[1], Tarih::$Aylar) + 1;
		return Tarih::ToMysqlDate(implode('-', $parts));
	}

	/**
	 * Her türlü formatta verilen date time parse ederek datetime a çevirir.
	 */
	public static function ParseStr($str, $onlyNumeric = false, $asMySqlFormat = false)
	{
		$str = preg_replace("/[\&nbsp\;]/i", ' ', $str);
		$patern = $onlyNumeric ? '0-9' : '0-9a-z';
		$str = trim(preg_replace("/[^$patern]/i", ' ', $str));
		$str = preg_replace('/[ ]+/', ' ', $str);
		$str = explode(' ', $str);
		if(count($str) < 3)
			return '00-00-0000';
		if ((int)$str[1] <= 0)
			$str[1] = StringLib::FindNearest(Tarih::$Aylar, $str[1]) + 1;
		$dt = "$str[0]-$str[1]-$str[2]";
		if(isset($str[3]) && isset($str[4]))
			$dt .= " $str[3]:$str[4]". (isset($str[5]) ? ":$str[5]" : '');
		return $asMySqlFormat ? self::ToMysqlDate($dt) : self::ToNormalDate($dt);
	}

	public static function GetYear($tarih)
	{
		$tarih = Tarih::ToMysqlDate($tarih);
		$parts = explode('-', $tarih);
		return intval($parts[0]);
	}

	public static function GetMonth($tarih)
	{
		$tarih = Tarih::ToMysqlDate($tarih);
		$parts = explode('-', $tarih);
		return intval($parts[1]);
	}

	public static function GetYearMonth($tarih)
	{
		$tarih = Tarih::ToMysqlDate($tarih);
		$parts = explode('-', $tarih);
		return 100 * $parts[0] + $parts[1];
	}

	public static function GetDay($tarih)
	{
		$tarih = Tarih::ToMysqlDate($tarih);
		$parts = explode('-', $tarih);
		return intval($parts[2]);
	}

	public static function ParseDateTime($dt)
	{
		$dt = explode(' ', self::ToMysqlDate($dt));
		$date = explode('-', $dt[0]);
		$time = explode(':', IfNull($dt, 1));
		$sonuc = array(
			'year'  => $date[0],
			'month' => $date[1],
			'day'   => $date[2],
			'hour'	=> IfNull($time, 0),
			'minute'=> IfNull($time, 1),
			'second'=> IfNull($time, 2)
		);
		return $sonuc;
	}

	public static function SaatDakika($time, $asStr = FALSE)
	{
		$parts = explode(':', $time);
		if(count($parts) < 2)
			return $time;
		$saat =  $parts[0];
		$dakika = $parts[1];
		if ($asStr)
		{
			$saat = intval($saat);
			$dakika = intval($dakika);
			$sonuc = '';
			if($saat > 0)
				$sonuc = "$saat saat, ";
			if($dakika > 0)
				$sonuc .= "$dakika dk.";
			return $sonuc;
		}
		return "$parts[0]:$parts[1]";
	}

	public static function Compare($tarih1, $tarih2, $operator = '>')
	{
		$tarih1 = self::StrToTime($tarih1);
		$tarih2 = self::StrToTime($tarih2);
		switch($operator)
		{
			case '>' : return $tarih1 > $tarih2;
			case '<' : return $tarih1 < $tarih2;
			case '<=': return $tarih1 <= $tarih2;
			case '>=': return $tarih1 >= $tarih2;
			case '=' : return $tarih1 == $tarih2;
		}
		return 0;
	}

	/**
	 * @return TarihObj
	 */
	public static function StrToObj($tarih = '')
	{
		if (! $tarih)
			$tarih = 'now';
		else
			$tarih = self::StrToTime($tarih);
		$obj = new TarihObj();
		$obj->setTimestamp($tarih);
		return $obj;
	}

	public static function GetDays($baslangic, $bitis)
	{
		$days = array();
		do{
			$baslangic = Tarih::GetDate($baslangic);
			$days[] = $baslangic;
			$baslangic = Tarih::GunEkle($baslangic, 1);
		} while(Tarih::Compare($baslangic, $bitis, '<='));
		return $days;
	}

	public static function IsWeekend($date)
	{
		return (date('N', Tarih::GetTime($date)) >= 6);
	}

	/**
	 * Verilen tarihin bugüne göre ne kadar süre önceye ait
	 * olduğunu geri döndürür (Ör: 2 gün önce, 1 ay önce v.b.)
	 * @param date $date
	 */
	public static function ToDateDiffStr($date)
	{
		$diff = self::FarkVer($date, Tarih::Bugun());
		if ($diff < 1)
			return "<font color=red>Bugün</font>";
		else if ($diff < 2)
			return "<font color=blue>Dün</font>";
		else if ($diff <= 7)
			return "<font color=green>$diff gün önce</font>";
		$str = "$diff gün önce";
		if ($diff >= 30)
		{
			$count = intval($diff/30);
			$fraction = $diff/30 - $count;
			if ($fraction >= 0.8)
				$count = "Yaklaşık " . ($count+1);
			else if ($fraction > 0.5)
				$count = "Yaklaşık $count.$fraction";
			else if ($fraction > 0.2)
				$count = "Yaklaşık $count";
			$str =  "$count ay önce";
		}
		return "<font color=gray>$str</font>";
	}

	public static function ShortDate($date)
	{
		$month	= self::GetMonth($date);
		$day	= self::GetDay($date);
		$year	= self::GetYear($date);
		$kisaAy = self::$KisaAylar[$month-1];
		$shortDate = $day." ".$kisaAy." ".$year;
		return $shortDate;
	}

	public static function ToWorkTime($bas,$bit)
	{
		$farkSaat = Tarih::FarkVer($bas, $bit,'DAKIKA');
		$farkGun = Tarih::FarkVer($bas, $bit);
		//8 saat günlük mesai süresi
		$fark = $farkGun*8*60+$farkSaat;
		return gmdate("H:i:s", $fark*60);
	}

	public static function GetAge($date)
	{
		if (! Tarih::IsValidDate($date))
			return;
		$fark = Tarih::FarkVer($date, Tarih::Bugun(), 'AY');
		if ($fark < 1)
			return 'Yeni';
		if ($fark < 12)
			return "$fark Aylık";
		$yil = round($fark / 12.0);
		return "$yil Yıllık";
	}
}

if (App::IsUTF8())
{
	Kodlama::UTF8Donustur(Tarih::$Aylar, FALSE);
	Kodlama::UTF8Donustur(Tarih::$Gunler, FALSE);
	Kodlama::UTF8Donustur(Tarih::$HaftaIciGunler, FALSE);
	Kodlama::UTF8Donustur(Tarih::$GunlerKisa, FALSE);
}
