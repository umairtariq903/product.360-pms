<?php
class KullaniciKimlik
{
    const CTRL_IMAP = 1;
    const CTRL_LDAP = 2;
    const CTRL_DB = 3;
    const CTRL_OZEL	= 4;

	private static $LoggedUser = null;

    /** @var integer Kimlik doğrulama türünün ne olacağı varsayılan CTRL_IMAP*/
    public static $CONTROL = self::CTRL_DB;

	/**
	 * Giriş yapmış kişiyi döndürür
	 * eğer giriş yapmış kullanıcı yoksa, geriye null döndürür
	 *
	 * @return User
	 */
	public static function GirisYapmisKisiVer()
	{
		if(self::$LoggedUser)
			return self::$LoggedUser;
		if (self::GirisYapmisKisiIdVer())
		{
			$kisi = self::$LoggedUser = UserDb::Get()->GetById(self::GirisYapmisKisiIdVer());
			if (!$kisi)
				return null;
			return $kisi;
		}
		else
			return null;
	}

	public static function GirisYapmisKisiIdVer()
	{
		if (isset($_SESSION['id']))
			return $_SESSION['id'];
		else
			return null;
	}

	//---------------------------------------------------
	// Verilen kullanıcı adı ve parolayı doğrular
	//---------------------------------------------------
	public static function Dogrula($email, $parola, $hatirla=false, $cl = "")
	{
        global $SITE_URL;

        $email = trim($email);
		$parola = DgrCode::Decode(trim($parola));

		if($email == "" || $parola == "")
			return "Giriş yapılamadı. Lütfen bilgileri tam giriniz.";

		$success = 0;

        $params = User::AsParams();
        $params->Email = Condition::EQ($email);
        $params->Password = Condition::EQ(DgrCode::Encode($parola));

        $kullanici = UserDb::Get()->GetFirst($params);

        if ($kullanici)
        {
            if (! $kullanici->Active)
                $success = "Hesabınız pasif durumdadır. Lütfen site yöneticisi ile iletişime geçiniz";

            else if (! in_array($kullanici->UserType,User::$TurIds))
                $success = "Hesabınız ile ilgili bir sorun oluştu lütfen bizimle iletişime geçiniz";
            else
            {
                self::OturumBaslat($kullanici);
                if ($hatirla && $cl != "")
                    self::BeniHatirla($kullanici,$cl);
                $success = 1;
            }
        }
        else
        {
            $success = "Email veya şifre hatalı";
        }


		return $success;
	}

    public static function BeniHatirla($kullanici, $cl)
    {
        $secure = !isLocalhost();
        setcookie("_dgr_us", self::KullaniciSifrele($kullanici, $cl), time()+60*60*24*90, '/', '', $secure,true);
        setcookie("_dgr_cl", $cl, time()+60*60*24*90, '/', '', $secure,true);
    }

    public static function KullaniciSifrele($kullanici, $cl)
    {
        $user = md5($kullanici->Email);
        $pass = md5($kullanici->Password);
        $object = array('user' => $user, 'password' => $pass, 'cl' => $cl);
        return DgrCode::Encode(json_encode($object));
    }

    public static function KullaniciSifreCoz($strVal)
    {
        $json = DgrCode::Decode($strVal);
        $object = Kodlama::JSONTryParse($json);
        if (! $object)
            return null;
        $clCookie = IfNull($_COOKIE, '_dgr_cl', '');
        if ($object->cl != $clCookie)
            return null;
        $params = User::AsParams();
        $params->Email = Condition::MD5($object->user);
        $params->Password = Condition::MD5($object->password);
        $kullanici = UserDb::Get()->GetFirst($params);
        return $kullanici;
    }

    public static function BeniHatirlaKontrol()
    {
        if (Kisi())
            return 0;
        $cookie = IfNull($_COOKIE, '_dgr_us', '');
        if (! $cookie)
            return 0;
        $clCookie = IfNull($_COOKIE, '_dgr_cl', '');
        if (! $clCookie)
            return 0;
        $kullanici = self::KullaniciSifreCoz($cookie);
        if (! $kullanici)
        {
            KullaniciKimlik::BeniHatirlaTemizle();
            return 0;
        }

        self::OturumBaslat ($kullanici);
        self::BeniHatirla($kullanici,$clCookie);

        return 1;
    }

    public static function BeniHatirlaDogrula()
    {
        $cookie = IfNull($_COOKIE, '_dgr_us', '');
        if (! $cookie)
            return 0;
        $clCookie = IfNull($_COOKIE, '_dgr_cl', '');
        if (! $clCookie)
            return 0;
        $cljsCookie = IfNull($_COOKIE, '_dgr_cljs', '');
        if (! $cljsCookie)
            return 0;

        $json = DgrCode::Decode($cookie);
        $object = Kodlama::JSONTryParse($json);
        if (! $object)
            return 0;

        if ($object->cl != $clCookie || $clCookie != $cljsCookie)
        {
            KullaniciKimlik::CikisYap();
            return 1;
        }

        return 0;
    }

    public static function BeniHatirlaTemizle()
    {
        $secure = !isLocalhost();
        setcookie("_dgr_us", 'nothing', 0, '/', '', $secure,true);
        setcookie("_dgr_cl", 'nothing', 0, '/', '', $secure,true);
    }

    public static function CikisYap()
    {
        session_destroy();
        KullaniciKimlik::BeniHatirlaTemizle();
    }

	/**
	 *
	 * @param User $kullanici
	 */
	public static function OturumBaslat($kullanici)
	{
		self::$LoggedUser = $kullanici;
		$_SESSION['id'] = $kullanici->Id;
		$_SESSION['son_giris_tarihi'] = $kullanici->LastLoginDate;
		$_SESSION['UserUniqueKey'] = "User.$kullanici->Id";
		$kullanici->LastLoginDate = Tarih::Simdi();
		$kullanici->LoginCount += 1;
		$kullanici->Save();
	}

	public static function IsAdmin()
	{
		$kisi = self::GirisYapmisKisiVer();
		return $kisi && $kisi->isAdmin();
	}

	public static function IsMudur()
	{
		$kisi = self::GirisYapmisKisiVer();
		return $kisi && $kisi->isMudur();
	}
}
