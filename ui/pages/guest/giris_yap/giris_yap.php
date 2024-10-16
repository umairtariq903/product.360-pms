<?php
class GuestGirisYapPage extends AppPageController
{
    public function Index()
    {
        global $SITE_URL;
        $kisi = Kisi();
        if ($kisi)
            die(header("Location: ". $SITE_URL . "act/".$kisi->GetAct()."/ozet"));
    }

    public function SetDefaultResources()
    {
        global $PAGE_PLUGINS;
        /*Giriş yap sayfası guest içerisinde olduğundan bu sayfaya özel yönetim dosyalarını yükle*/
        $PAGE_PLUGINS[] = "metronic/global/css/google-font.css";
        $PAGE_PLUGINS[] = "metronic/global/plugins/simple-line-icons/simple-line-icons.min.css";
        $PAGE_PLUGINS[] = "metronic/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css";
        $PAGE_PLUGINS[] = "metronic/global/plugins/bootstrap-sweetalert/sweetalert.min.js";
        $PAGE_PLUGINS[] = "metronic/global/plugins/bootstrap-sweetalert/sweetalert.css";
        $PAGE_PLUGINS[] = "metronic/global/plugins/bootstrap-toastr/toastr.min.css";
        $PAGE_PLUGINS[] = "metronic/global/plugins/bootstrap-toastr/toastr.min.js";
        $PAGE_PLUGINS[] = "metronic/global/css/components-rounded.min.css";
        $PAGE_PLUGINS[] = "metronic/global/css/plugins.min.css";
        $PAGE_PLUGINS[] = "metronic/layouts/layout/css/layout.min.css";
        $PAGE_PLUGINS[] = "metronic/layouts/layout/css/themes/default.min.css";
        $PAGE_PLUGINS[] = "metronic/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js";
        $PAGE_PLUGINS[] = "metronic/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js";
        $PAGE_PLUGINS[] = "metronic/global/plugins/bootstrap-maxlength/bootstrap-maxlength.min.js";
        $PAGE_PLUGINS[] = "metronic/global/scripts/app.min.js";
        $PAGE_PLUGINS[] = "metronic/layouts/layout/scripts/layout.min.js";
        $PAGE_PLUGINS[] = "metronic/layouts/layout/scripts/demo.min.js";
        /*Giriş yap sayfası guest içerisinde olduğundan bu sayfaya özel yönetim dosyalarını yükle*/

        $PAGE_PLUGINS[] = "metronic/global/plugins/select2/css/select2.min.css";
        $PAGE_PLUGINS[] = "metronic/global/plugins/select2/css/select2-bootstrap.min.css";
        $PAGE_PLUGINS[] = "metronic/pages/css/login-3.css";
        $PAGE_PLUGINS[] = "metronic/pages/css/profile.min.css";
        parent::SetDefaultResources();
    }
    public static function GirisYap($obj)
    {
        return KullaniciKimlik::Dogrula($obj->Email, $obj->Password, $obj->Hatirla, $obj->cl);
    }
    public static function ParolaSifirla($email)
    {
        global $SITE_URL, $SITE_ADI;
        //Parola Sıfırla
        $kullanici = UserDb::Get()->GetFirst(array("Email" => Condition::EQ($email)));

        if(! $kullanici)
            return "Bu mail adresi sistemde kayıtlı değil";

        $yenileme = new ParolaYenileme();
        $yenileme->Email = $kullanici->Email;
        $yenileme->Text = GenerateRandomString(20);
        $yenileme->EklenmeTarihi = Tarih::Simdi();
        $snc = $yenileme->Save();
        if ($snc == 1)
        {
            $smarty = SmartyWrap::Load(true);
            $smarty->assign("Text",$yenileme->Text);
            $smarty->assign("SITE_URL",$SITE_URL);
            $body = $smarty->fetch("kullanici/parola_unuttum.tpl");
            $snc = YeniMailEkle($kullanici->Email, $SITE_ADI ." Parola", $body);
        }

        return $snc;
    }
    public static function KayitOl($obj)
    {
        /*if ($obj->Ad == "" || $obj->Soyad == "" || $obj->AlanKodu == "" || $obj->Telefon == "" || $obj->Email == "" || $obj->Parola == "")
            return "Zorunlu alanların doldurulması gerekmektedir";
        if ($obj->AlanKodu == "+90" && strlen($obj->Telefon) != 10)
            return "Telefon alanı geçersiz";
        if ($obj->Parola != $obj->ParolaTekrar)
            return "Parola ve Parola Tekrar alanları birbirine eşit olmalıdır";
        $kullanici = UserDb::Get()->GetFirst(array('Email' => Condition::EQ($obj->Email)));
        if($kullanici)
            return "Bu mail adresi sistemde kayıtlıdır.";

        $obj->Parola = DgrCode::Encode($obj->Parola);

        $kullanici = new User();
        $kullanici->SetFromObj($obj);
        $kullanici->UyelikTarihi = Tarih::Simdi();
        $kullanici->Email = mb_strtolower($kullanici->Email);

        if ($kullanici->Meslek == "")
        {
            $kullanici->Meslek = "Tanıtım Metni 1";
            $kullanici->Bio = "Tanıtım Metni 2";
        }

        $kullanici->Telefon = $obj->AlanKodu . $obj->Telefon;

        $snc = $kullanici->Save();

        if ($snc == 1)
        {
            $emailBilgi = new VcardIletisimBilgi();
            $emailBilgi->IletisimTurId = VcardIletisimTur::$Email;
            $emailBilgi->KullaniciId = $kullanici->Id;
            $emailBilgi->Deger = $kullanici->Email;
            $emailBilgi->Save();

            $telefonBilgi = new VcardIletisimBilgi();
            $telefonBilgi->IletisimTurId = VcardIletisimTur::$Mobil;
            $telefonBilgi->KullaniciId = $kullanici->Id;
            $telefonBilgi->AlanKodu = $kullanici->AlanKodu;
            $telefonBilgi->Deger = $kullanici->Telefon;
            $telefonBilgi->Save();

            $whatsappBilgi = new VcardIletisimBilgi();
            $whatsappBilgi->IletisimTurId = VcardIletisimTur::$Whatsapp;
            $whatsappBilgi->KullaniciId = $kullanici->Id;
            $whatsappBilgi->AlanKodu = $kullanici->AlanKodu;
            $whatsappBilgi->Deger = $kullanici->Telefon;
            $whatsappBilgi->Save();

            KullaniciKimlik::OturumBaslat($kullanici);
            return $snc;
        }
        return $snc;*/
    }
}
