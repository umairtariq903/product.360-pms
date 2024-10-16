<?php

class DefaultPage extends AppPageController
{
    public function Index()
    {
        global $SITE_URL;
        header("Location:$SITE_URL". "adminpanel");
        exit;
    }
}

