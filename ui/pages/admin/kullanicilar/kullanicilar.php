<?php
class AdminKullanicilarPage extends AppPageController
{
	/**
	 * Alanlara ait özellikleri çalışma zamanında değiştirmek için callback
	 * @param ColumnTemplate $col
	 */
	public static function ColumnPropertyRenderer($col)
	{

	}

	/**
	 * Arama kriterlerine ekstra parametre eklemek için kullanılır
	 * @param User $params ModelParam türünde
	 * @group DbList
	 */
	public function DataProcessParam($params)
	{
        $kisi = Kisi();
        $params->UserType = Condition::GT($kisi->UserType);
	}
}
