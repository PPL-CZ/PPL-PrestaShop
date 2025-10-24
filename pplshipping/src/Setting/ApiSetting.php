<?php
namespace PPLShipping\Setting;

use PPLShipping\Model\Model\MyApi2;

class ApiSetting
{
    public static function getApi()
    {
        $myapi2 = new MyApi2();

        $client_id = \Configuration::getGlobalValue("PPLClientId");
        $client_secret = \Configuration::getGlobalValue("PPLClientSecret");
        $myapi2->setClientId($client_id ?: "");
        $myapi2->setClientSecret($client_secret ?: "");

        return $myapi2;
    }

    public static function setApi(MyApi2  $myapi2)
    {
        \Configuration::updateGlobalValue("PPLClientSecret", $myapi2->getClientSecret());
        \Configuration::updateGlobalValue("PPLClientId", $myapi2->getClientId());

    }
}