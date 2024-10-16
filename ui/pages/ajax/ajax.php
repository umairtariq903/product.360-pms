<?php
class AjaxPage extends AppPageController
{
    public static function BeniHatirlaDogrula()
    {
        return KullaniciKimlik::BeniHatirlaDogrula();
    }

    public static function GonderilecekMailKontrol()
    {
        $mailler = GonderilecekMailDb::Get()->SetOrderByExp("id DESC")->GetList();
        if (count($mailler) <= 0)
            return "Mail yok";
        foreach($mailler as $mail)
        {
            $aktif = Debug::$IsAktif;
            Debug::$IsAktif = false;
            $snc = Mailer::Send($mail->Email, $mail->Baslik, $mail->Icerik);
            Debug::$IsAktif = $aktif;
            if ($snc == 1)
                $mail->Delete();
        }
        return 1;
    }
    public static function RunWorkImport()
    {
        return RunWorkImport();
    }

    public static function PMSCompanyChange($id)
    {
        $_SESSION["active_company_id"] = $id;
        return 1;
    }

    public static function PMSProjectChange($id)
    {
        $_SESSION["active_project_id"] = $id;
        return 1;
    }
}
