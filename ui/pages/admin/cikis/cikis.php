<?php
class AdminCikisPage extends AppPageController
{
    public function Index()
    {
        KullaniciKimlik::CikisYap();
        $this->ReLocate('');
    }
}
