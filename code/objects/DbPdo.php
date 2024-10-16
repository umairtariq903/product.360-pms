<?php

class DbPdo {
    private static $db;

    public static function Set($host, $dbName, $userName, $password)
    {
        try {
            if(! self::$db)
            {
                self::$db = new PDO("mysql:host={$host};dbname={$dbName};charset=utf8", $userName, $password);
                self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
        } catch(PDOException $e) {
            die("Bağlantı hatası: " . $e->getMessage());
        }
    }

    public static function Get()
    {
        return self::$db;
    }

    public static function Save($tablo, $veri)
    {
        try {
            $alanlar = implode(', ', array_keys($veri));
            $parametreler = ':' . implode(', :', array_keys($veri));
            $query = "INSERT INTO $tablo ($alanlar) VALUES ($parametreler)";
            $stmt = self::$db->prepare($query);
            $stmt->execute($veri);
            return self::$db->lastInsertId();
        } catch(PDOException $e) {
            return false;
        }
    }

    public static function SaveAll($tablo, $veriler)
    {
        try {
            $alanlarDizisi = array_keys($veriler[0]);
            $alanlar = implode(', ', $alanlarDizisi);
            $query = "INSERT INTO $tablo ($alanlar) VALUES ";
            $parametreler = array();
            $sorguParcalari = array();
            foreach ($veriler as $key => $veri) {
                $alanlarDizisiKey = [];
                foreach ($alanlarDizisi as $v)
                {
                    $alanlarDizisiKey[] = $v . "_$key";
                    $parametreler[":$v"."_"."$key"] = $veri[$v];
                }
                $sorguParcalari[] = "(:" . implode(", :", $alanlarDizisiKey) . ")";
//                $sorguParcalari[] = "(:company_id_$key, :project_id_$key, :ean_$key, :title_$key, :sp1_stock_$key, :sp1_cost_$key)";
                /*$parametreler[":company_id_$key"] = $veri["company_id"];
                $parametreler[":project_id_$key"] = $veri["project_id"];
                $parametreler[":ean_$key"] = $veri["ean"];
                $parametreler[":title_$key"] = $veri["title"];
                $parametreler[":sp1_stock_$key"] = $veri["sp1_stock"];
                $parametreler[":sp1_cost_$key"] = $veri["sp1_cost"];*/
            }
            $query .= implode(', ', $sorguParcalari);

            $stmt = self::$db->prepare($query);

            $stmt->execute($parametreler);

            return 1;
        } catch(PDOException $e) {
            ArrayShortInfo($e,3);
            return false;
        }
    }

    public static function Update($tablo, $veri, $kosul) {
        try {
            $set = '';
            foreach ($veri as $alan => $deger) {
                $set .= "$alan=:$alan, ";
            }
            $set = rtrim($set, ', ');

            $query = "UPDATE $tablo SET $set WHERE $kosul";
            $stmt = self::$db->prepare($query);
            $stmt->execute($veri);
            return $stmt->rowCount();
        } catch(PDOException $e) {
            return false;
        }
    }

    public function sil($tablo, $kosul) {
        try {
            $query = "DELETE FROM $tablo WHERE $kosul";
            $stmt = self::$db->prepare($query);
            $stmt->execute();
            return $stmt->rowCount();
        } catch(PDOException $e) {
            return false;
        }
    }

    public static function GetFirst($tablo, $kosul = '', $parametreler = array()) {
        try {
            $query = "SELECT * FROM $tablo";
            if (!empty($kosul)) {
                $query .= " WHERE $kosul";
            }
            $stmt = self::$db->prepare($query);
            $stmt->execute($parametreler);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }

    public static function GetAll($tablo, $kosul="", $parametreler = array()) {
        try {
            $query = "SELECT * FROM $tablo";
            if (!empty($kosul)) {
                $query .= " WHERE $kosul";
            }
            $stmt = self::$db->prepare($query);
            $stmt->execute($parametreler);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return false;
        }
    }
}
