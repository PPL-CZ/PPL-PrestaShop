<?php
namespace PPLShipping\ModelNormalizer;

use PPLShipping\Model\Model\ParcelPlacesModel;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ParcelPlacesModelDenormalizer implements DenormalizerInterface
{
    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof \Configuration)
        {
            $parcelplaces = new ParcelPlacesModel();

            $parcelplaces->setDisabledAlzaBox(!!\Configuration::getGlobalValue("PPLDisabledAlzaBox"));
            $parcelplaces->setDisabledParcelBox(!!\Configuration::getGlobalValue("PPLDisabledParcelBox"));
            $parcelplaces->setDisabledParcelShop(!!\Configuration::getGlobalValue("PPLDisabledParcelShop"));

            $disabledCountries  = \Configuration::getGlobalValue("PPLDisabledParcelCountries");
            $disabledCountries = @json_decode($disabledCountries);
            if (!is_array($disabledCountries))
            {
                $disabledCountries = [];
            }
            $parcelplaces->setDisabledCountries($disabledCountries);
            $parcelplaces->setMapLanguage(\Configuration::getGlobalValue("PPLMapLanguage"));

            return $parcelplaces;
        }
        else if ($data instanceof ParcelPlacesModel)
        {
            if ($data->getDisabledParcelShop())
                \Configuration::updateGlobalValue("PPLDisabledParcelShop", "1");
            else
                \Configuration::updateGlobalValue("PPLDisabledParcelShop", "");

            if ($data->getDisabledParcelBox())
                \Configuration::updateGlobalValue("PPLDisabledParcelBox", "1");
            else
                \Configuration::updateGlobalValue("PPLDisabledParcelBox", "");

            if ($data->getDisabledAlzaBox())
                \Configuration::updateGlobalValue("PPLDisabledAlzaBox", "1");
            else
                \Configuration::updateGlobalValue("PPLDisabledAlzaBox", "");

            $countries = array_filter($data->getDisabledCountries() ?: [], function($item) {
              return in_array($item, ['CZ', 'SK', "PL", "DE"], true);
            });

            $lang = $data->getMapLanguage();
            if (!in_array($lang, ['CS', 'EN'], true))
                $lang = "";

            $countries = json_encode($countries);

            \Configuration::updateGlobalValue("PPLDisabledParcelCountries", $countries);

            \Configuration::updateGlobalValue("PPLMapLanguage", $lang);

        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        if ($data instanceof \Configuration && $type === ParcelPlacesModel::class)
            return true;
        else if ($data instanceof ParcelPlacesModel && \Configuration::class === $type )
            return true;
        return false;
    }
}