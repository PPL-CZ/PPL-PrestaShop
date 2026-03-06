<?php
namespace PPLShipping\Setting;

use PPLShipping\Model\Model\ParcelPlacesModel;
use PPLShipping\Model\Model\ShipmentMethodModel;

class MethodSetting
{
    public static function getGlobalParcelboxesSetting()
    {
        $parcelPlaces = new ParcelPlacesModel();


        $disabledParcelBox = !!\Configuration::getGlobalValue("PPLDisabledParcelBox");
        $disabledParcelShop = !!\Configuration::getGlobalValue("PPLDisabledParcelShop");
        $disabledAlzaBox = !!\Configuration::getGlobalValue("PPLDisabledAlzaBox");
        $languageMap = \Configuration::getGlobalValue("PPLMapLanguage");

        $disabledCountriesFromBaseSetting = \Configuration::getGlobalValue("PPLDisabledParcelCountries");
        $disabledCountriesFromBaseSetting = json_decode($disabledCountriesFromBaseSetting);

        if (!is_array($disabledCountriesFromBaseSetting))
            $disabledCountriesFromBaseSetting = [];

        $parcelPlaces->setDisabledCountries($disabledCountriesFromBaseSetting);
        $parcelPlaces->setMapLanguage($languageMap);
        $parcelPlaces->setDisabledParcelBox($disabledParcelBox);
        $parcelPlaces->setDisabledParcelShop($disabledParcelShop);
        $parcelPlaces->setDisabledAlzaBox($disabledAlzaBox);

        return $parcelPlaces;
    }

    public static function setGlobalParcelboxesSetting(ParcelPlacesModel $setting)
    {
        $countries = json_encode($setting->getDisabledCountries(), true);
        \Configuration::updateGlobalValue("PPLDisabledParcelBox", $setting->getDisabledParcelBox());
        \Configuration::updateGlobalValue("PPLDisabledParcelShop", $setting->getDisabledParcelShop());
        \Configuration::updateGlobalValue("PPLDisabledAlzaBox", $setting->getDisabledAlzaBox());
        \Configuration::updateGlobalValue("PPLDisabledParcelCountries", $countries);
        \Configuration::updateGlobalValue("PPLMapLanguage", $setting->getMapLanguage());
    }

    public static function getCodMethods($code) {
        $methods =  [
            "PRIV" => "PRID",
            "CONN" => "COND",
            "SMAR" => "SMAD",
            "SMEU" => "SMED",
            "SBOX" => "SBOD"
        ];
        if (isset($methods[$code]))
            return $methods[$code];
        return null;
    }

    public static function getMethod($code)
    {
        foreach (static::getMethods() as $method)
        {
            if ($method->getCode() === $code)
                return $method;
        }
        return null;
    }

    /**
     * @return ShipmentMethodModel[]
     */
    public static function getMethods()
    {
        $output = [];

        $methods = [
            "PRIV"=> "PPL Parcel CZ Private", // cz
            "SMAR" => "PPL Parcel CZ Smart", // cz, VM
            "SBOX" => "PPL Parcel CZ Smart To Box",

            "SMEU" => "PPL Parcel Smart Europe", // necz
            "CONN" => "PPL Parcel Connect", // necz,

            "COPL" => "PPL Parcel Connect Plus",
        ];

        foreach ($methods as $key => $value) {
            $method = new ShipmentMethodModel();
            $method->setCode($key);
            $method->setTitle($value);
            $method->setCodAvailable(false);
            $method->setParcelRequired(in_array($key, ["SMAR", "SMEU", "SBOX"], true));
            $method->setAgeValidation(null);

            if (in_array($key, ["SMAR", "PRIV"], true)) {
                $method->setAgeValidation(true);
            } else if ($key === "SBOX") {
                $method->setAgeValidation(false);
                $method->setMaxWeight(10);
                $method->setMaxDimension([50, 40, 38]);
            } else if (in_array($key, ["SMEU", "CONN"], true)) {
                $method->setMaxPackages(1);
            }

            if($method->getParcelRequired())
            {
                $method->setDisabledParcelTypes([]);
                $method->setAvailableParcelTypes(["ParcelBox", "ParcelShop", "AlzaBox"]);

                if ($method->getCode() === "SBOX") {
                    $method->setDisabledParcelTypes(["AlzaBox", "ParcelShop"]);
                    $method->setAvailableParcelTypes(["ParcelBox"]);
                    $method->setMaxPackages(1);
                }
            }
            $output[] = $method;
        }

        $codMethods = [
            "PRID"=> "PPL Parcel CZ Private - dobírka", // cz
            "SMAD" => "PPL Parcel CZ Smart - dobírka", // cz, VM
            "SBOD" => "PPL Parcel CZ Smart To Box - dobírka",
            "SMED" => "PPL Parcel Smart Europe - dobírka", // necz
            "COND" => "PPL Parcel Connect - dobírka", // necz
        ];

        foreach ($codMethods as $key => $value) {
            $method = new ShipmentMethodModel();
            $method->setCode($key);
            $method->setTitle($value);
            $method->setCodAvailable(true);
            $method->setParcelRequired(in_array($key, ["SMAD", "SMED", "SBOD"], true));
            $method->setAgeValidation(null);

            if (in_array($key, ["SMAD", "PRID"], true)) {
                $method->setAgeValidation(true);
            } else if ($key === "SBOD") {
                $method->setAgeValidation(false);
                $method->setMaxWeight(10);
                $method->setMaxDimension([50, 40, 38]);
                $method->setMaxPackages(1);
            } else if (in_array($key, ["SMED", "COND"], true)) {
                $method->setMaxPackages(1);
            }

            if($method->getParcelRequired())
            {
                $method->setAvailableParcelTypes(["ParcelBox", "ParcelShop", "AlzaBox"]);
                $method->setDisabledParcelTypes([]);
                if ($method->getCode() === "SBOD") {
                    $method->setAvailableParcelTypes(["ParcelBox"]);
                    $method->setDisabledParcelTypes(["AlzaBox", "ParcelShop"]);
                }
            }

            $output[] = $method;
        }

        foreach ($output as $value)
        {
            $code = $value->getCode();
            $countries = [];
            if (in_array($code, ['SMAR', "SMAD", 'PRIV', 'PRID', "SBOX", "SBOD"]))
                $countries = ["CZ"];
            else if (in_array($code, ['SMEU', "SMED", 'CONN', 'COND']))
                $countries = self::getEuCountries();
            else {
                $countries = require __DIR__ . '/../config/countries.php';
                $eu_countries = self::getEuCountries();
                $countries = array_diff(array_keys($countries), $eu_countries);
            }
            $value->setCountries($countries);
        }

        return $output;
    }

    public static function getEuCountries()
    {
        return [
            'AT', // Rakousko
            'BE', // Belgie
            'BG', // Bulharsko
            'HR', // Chorvatsko
            'CY', // Kypr
            'DK', // Dánsko
            'EE', // Estonsko
            'FI', // Finsko
            'FR', // Francie
            'DE', // Německo
            'GR', // Řecko
            'HU', // Maďarsko
            'IE', // Irsko
            'IT', // Itálie
            'LV', // Lotyšsko
            'LT', // Litva
            'LU', // Lucembursko
            'MT', // Malta
            'NL', // Nizozemsko
            'PL', // Polsko
            'PT', // Portugalsko
            'RO', // Rumunsko
            'SK', // Slovensko
            'SI', // Slovinsko
            'ES', // Španělsko
            'SE', // Švédsko
        ];
    }

    public static function getMethodForCountry($country, $method)
    {

        $countries = require __DIR__ . '/../config/countries.php';
        if (!isset($countries[$country]))
            return null;

        if ($country === 'CZ')
        {
            $codes = [
                'PRIV' => 'PRIV', 'PRID'=> "PRID", 'SMAR'=> 'SMAR', 'SMAD'=> 'SMAD',
                'SMEU' => "SMAR", "SMED" => "SMAD", "CONN" => "PRIV", 'COND'=> "PRID",
                "COPL" => "PRIV", "SBOX" => "SBOX", "SBOD" => "SBOD"
            ];
            if (isset($codes[$method]))
                return $codes[$method];
            return null;

        }
        else
        {
            if (in_array($country, self::getEuCountries(), true))
            {
                $codes = [
                    "COPL" => "CONN",
                    'PRIV' => 'CONN', 'PRID'=> "COND", 'SMAR'=> 'SMEU', 'SMAD'=> 'SMED',
                    'SMEU' => "SMEU", "SMED" => "SMED", "CONN" => "CONN", 'COND'=> "COND",
                    "SBOX" => "SMEU", "SBOD" => "SMED"
                ];

                if (isset($codes[$method]))
                    return $codes[$method];

                return null;
            }
            else
            {
                if (in_array($method, ['COPL', "PRIV", "SMAR", "CONN", "SMEU"], true))
                    return "COPL";
                return null;
            }
        }
    }

}