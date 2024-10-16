<?php
class AdminBilgilerimPage extends AppPageController
{
	/**
	 * DbList veya DbForm için xml parse edildikten sonra ViewModel build edilmeden
	 * önce yapılacak işlemler
	 * @param PageInfo $pageInfo
	 * @return PageInfo
	 */
	public function PreBuildViewModel(PageInfo $pageInfo)
	{
        $_GET["id"] = KisiId();
	}
}
