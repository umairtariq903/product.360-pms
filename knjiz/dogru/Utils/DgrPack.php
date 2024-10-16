<?php
/*
 * Dogru haberleşme paketi
 */
class DgrPack
{
	const TS_YONETIM = 'yonetim';
	const TS_SATINALMA = 'satinalma';

	public static $ServerUrl = array(
		self::TS_YONETIM	=> 'dgryazilim.com/yonetim',
		self::TS_SATINALMA	=> 'dgryazilim.com/satinalma'
	);
	/**
	 * Gönderilen bilginin geçerlilik süresi. eğer verilmemişse bu bilgi temp de
	 * saklanmaz ve her seferinde tekrar istenir.
	 * @var datetime
	 */
	public $ExpireDate = null;
	public $Data = null;

	public static function Serialize($data, $availabilityDay = NULL, $isJson = false)
	{
		if($availabilityDay)
			$availabilityDay = Tarih::GunEkle(Tarih::Bugun(), $availabilityDay);
		$pack = new DgrPack();
		$pack->ExpireDate = $availabilityDay;
		$pack->Data = $data;
		return $isJson ? Kodlama::JSON($pack, false) : serialize($pack);
	}

	public static function GetData($dataName, $targetServer = self::TS_YONETIM)
	{
		$val = GenelTablo::GetValue("DgrTemp_$dataName");
		$pack = @json_decode($val);
		/*@var $pack DgrPack */
		if(! is_object($pack) || Tarih::FarkVer(Tarih::Bugun(), $pack->ExpireDate) < 0)
		{
			$resp = GetDgrWebServis("customer", "islem=$dataName&is_json=1", $targetServer);
			$pack = @json_decode($resp);
			if(is_object($pack) && $pack->ExpireDate)
				GenelTablo::SetValue("DgrTemp_$dataName", $resp);
		}
		if (! App::IsUTF8())
			Kodlama::KarakterKodlamaDuzelt($pack, false, false);
		return $pack ? $pack->Data : $resp;
	}
}
