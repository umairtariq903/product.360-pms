<?php
/**
 * Günlük, Haftalık, Aylık Yapılan rutin işlemlerin toplandığı sınıf
 * Yapılacak işlemleri bu sınıftan türetilen sınıflarda tanımlanabilir.
 */
class RoutineJobs
{
	public $Logs = array();
	// Geceyarısından ne kadar sonra çalışacak
	public $OffsetMinute = 0;

	public function DayChanged($yesterday, $today)
	{
		$this->Logs[] = "Gün değişti($yesterday => $today)";
	}

	public function WeekChanged($preMonday, $thisMonday)
	{
		$this->Logs[] = "Hafta değişti($preMonday => $thisMonday)";
	}

	public function MonthChanged($preMonth, $thisMonth)
	{
		$this->Logs[] = "Ay değişti($preMonth => $thisMonth)";
	}

	public function YearChanged($preYear, $thisYear)
	{
		$this->Logs[] = "Yıl değişti($preYear => $thisYear)";
	}

	public function Check()
	{
		// Paralel işlemleri engellememek için oturumu kapatıyoruz
		session_write_close();
		set_time_limit(0);
        ini_set('memory_limit', '-1');

		$OfsMinute = -$this->OffsetMinute;
		$time = strtotime("$OfsMinute Minutes");
		$today = date('Y-m-d', $time);
		$idKey = 'LastRoutineJobDay';
		$lastDay = GenelTablo::GetValue($idKey);
		if(! $lastDay)
			$lastDay = Tarih::ToMysqlDate(Tarih::GunEkle($today, -1));
		if($lastDay >= $today)
			return FALSE;
		$sem = DogRu\AppSemaphore::begin('routine_job', 4);
		if (! $sem)
			return;
		GenelTablo::SetValue($idKey, $today);
		// Günlük işler
		$this->DayChanged($lastDay, $today);
		// Haftalık İşler
		$preMonday = Tarih::Pazartesi($lastDay);
		$thisMonday = Tarih::Pazartesi($today);
		if($preMonday < $thisMonday)
			$this->WeekChanged($preMonday, $thisMonday);
		// Aylık İşler
		$preMonth = Tarih::AyBasi($lastDay);
		$thisMonth = Tarih::AyBasi($today);
		if($preMonth < $thisMonth)
			$this->MonthChanged($preMonth, $thisMonth);
		// Yıllık İşler
		$preYear = Tarih::YilBasi($lastDay);
		$thisYear = Tarih::YilBasi($today);
		if($preYear < $thisYear)
			$this->YearChanged($preYear, $thisYear);
		$sem->release();
		return TRUE;
	}
}
