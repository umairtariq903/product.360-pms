<?php
interface ExportWrapper
{
	/**
	 * Döküman ilk oluşturulurken çağrılır
	 * @param type $selectedFields
	 * @param type $fileTitle
	 * @param type $header
	 */
	public function Prepare($selectedFields, $fileTitle, $columnWidths = array(), $header = array());

	/**
	 * Dökümana yazılacak her satır için çağrılır
	 * @param type $obj
	 */
	public function WriteRow($obj);

	/**
	 * Döküman tamamlanıp, tarayıcıya gönderilmesi amacıyla çağrılır
	 * @param type $fileName
	 * @param type $columnWidths
	 */
	public function Finish($fileName, $columnWidths = array());
}

