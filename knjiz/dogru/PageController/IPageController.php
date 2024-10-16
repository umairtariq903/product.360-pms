<?php
interface IPageController
{
	/**
	 * Sayfanın GET isteğiyle istendiği durumlarda çalışan metot
	 */
	public function Index();

	/**
	 * Sayfanın POST isteğiyle istendiği durumlarda çalışan metot
	 */
	public function FormPosted();

	/**
	 * Sayfa ile ilgili yapılması gereken ilk işlemleri yapar
	 */
	public function Init();

	/**
	 * Sayfaya ait template'in tam yolunu verir
	 * @return string
	 */
	public function GetTemplateUri();

	/**
	 * Sayfaya ait template'i set eder
	 * @param string $templateUrl Atanan template'in tam veya göreceli yolu
	 */
	public function SetTemplateUri($templateUrl, $relativePath = true);

	/**
	 * Ajax istekleri tarafından çağrılabilecek metotların listesi
	 * @return string[]
	 */
	public function GetAllowedAjaxMethods();

	/**
	 * Ajax isteklerini karşılayan metot
	 */
	public function HandleAjaxRequest();
}
?>
