<?php
class Import extends ImportBase
{
    const Main = 1;
    const Feed = 2;

    const File = 1;
    const Ftp = 2;
    const Url = 3;

    const TxtFile=1;
    const CsvFile=2;
    const XmlFile=3;
    const JsonFile=4;

    public static $FromAts = array(
        self::File	=> "File",
        self::Ftp	=> "FTP",
        self::Url	=> "URL",
    );
    public static $ImportTypes = array(
        self::Main	=> "main",
        self::Feed	=> "feed"
    );

    public function Init($row)
    {
        parent::Init($row);

        if($this->PFieldKeys != "")
        {
            $lines = explode("\n", $this->PFieldKeys);

            $this->PFields = [];
            foreach ($lines as $line)
            {
                $parts = explode("=", $line, 2);
                $this->PFields[$parts[0]] = $parts[1];
            }
        }

        if($this->PFieldWhereKeys != "")
        {
            $lines = explode("\n", $this->PFieldWhereKeys);

            $this->PFieldWheres = [];
            foreach ($lines as $line)
            {
                $parts = explode("=", $line, 2);
                $this->PFieldWheres[$parts[0]] = $parts[1];
            }
        }

        return $this;
    }
}

class ImportDb extends ImportDbBase
{
}
