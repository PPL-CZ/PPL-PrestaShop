<?php
namespace PPLShipping\ModelNormalizer;

use PPLShipping\Model\Model\ErrorLogItemModel;
use PPLShipping\CPLOperation;
use PPLShipping\Model\Model\ErrorLogModel;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ErrorLogModelDenormalizer  implements DenormalizerInterface
{
    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof ErrorLogModel && $type === ErrorLogModel::class;
    }


    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {

        $client_id = null;
        $client_secret = null;
        $accessToken = null;
        try {
            $client_secret = \Configuration::getGlobalValue("PPLClientSecret");
            $client_id = \Configuration::getGlobalValue("PPLClientId");

            if ($client_id && $client_secret) {
                $accessToken = (new CPLOperation())->getAccessToken();
            }
        }
        catch (\Error $e) {

        }

        $modules = array_filter(array_map(function($item) {
            if ($item->active) {
                return $item->name . ' - ' . $item->version;
            }
            return null;
        }, \Module::getModulesOnDisk()));

        $wordpress = _PS_VERSION_;
        $php = phpversion();

        if ($accessToken)
            $accessToken = "ano";
        else
            $accessToken = "ne";



        $summary = [
            "### Přístup",
            "Client ID: $client_id",
            "Získa accessToken: {$accessToken}",
            "***",
            "### Verze",
            "Wordpress: $wordpress",
            "PHP: $php",
            "***",
            "### Plugins",
            join("\n", $modules)
        ];
        $data->setMail(\Configuration::get('PS_SHOP_EMAIL'));
        $data->setInfo(join("\n", $summary));
        $items = [];

        $logs = \PPLLog::GetLogs();

        foreach ($logs as $log)
        {
            $item = new ErrorLogItemModel();
            $item->setTrace($log->message);
            $items[] = $item;
        }

        $data->setErrors($items);

        return $data;
    }
}