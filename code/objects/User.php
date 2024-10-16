<?php
class User extends UserBase
{
    const Admin = 10;
    const Mudur = 20;

    public static $Turler = array(
        User::Admin	=> "Admin",
        User::Mudur	=> "Müdür",
    );
    public static $TurIds = array(
        0	=> User::Admin,
        1	=> User::Mudur,
    );
    public static $Acts = array(
        User::Admin	=> "admin",
        User::Mudur	=> "mudur",
    );

    public function isAdmin()
    {
        return $this->UserType == self::Admin;
    }
    public function isMudur()
    {
        return $this->UserType == self::Mudur;
    }

    public function GetAct()
    {
        return self::$Acts[$this->UserType];
    }
}

class UserDb extends UserDbBase
{
}
