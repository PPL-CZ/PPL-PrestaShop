<?php
namespace PPLShipping\ModelNormalizer;

use PPLShipping\Model\Model\CategoryRulesModel;
use PPLShipping\Model\Model\ProductRulesModel;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class RulesDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof \PPLBaseDisabledRule)
        {
            $model = $type === CategoryRulesModel::class ? new CategoryRulesModel() : new ProductRulesModel();
            $model->setPplDisabledAlzaBox(!!$data->disabled_alzabox);
            $model->setPplDisabledParcelShop(!!$data->disabled_parcelshop);
            $model->setPplDisabledParcelBox(!!$data->disabled_parcelbox);
            $model->setPplDisabledTransport(array_filter(explode(";", $data->disabled_methods ?: "")));
            $model->setPplConfirmAge15(!!$data->required_age15);
            $model->setPplConfirmAge18(!!$data->required_age18);
            return $model;
        }
        else if ($data instanceof CategoryRulesModel || $data instanceof  ProductRulesModel)
        {
            if (isset($context['data']) && $context['data'])
            {
                $model = $context['data'];
            }
            else
            {
                $model = new \PPLBaseDisabledRule;
            }
            $model->disabled_parcelbox = !!$data->getPplDisabledParcelBox();
            $model->disabled_parcelshop = !!$data->getPplDisabledParcelShop();
            $model->disabled_alzabox = !!$data->getPplDisabledAlzaBox();
            $transport = $data->getPplDisabledTransport();

            if (!is_array($transport))
                $transport = [];

            $methods = pplcz_get_all_services();
            foreach ($transport as $key => $value)
                if (!isset($methods[$value]))
                    unset($transport[$key]);

            $model->disabled_methods = $transport ? join(';', $transport) : null;
            $model->required_age18 = !!$data->getPplConfirmAge18();
            $model->required_age15 = !!$data->getPplConfirmAge15();
            return $model;

        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof \PPLBaseDisabledRule && ($type === CategoryRulesModel::class || $type === ProductRulesModel::class)
            || ($data instanceof CategoryRulesModel || $data instanceof ProductRulesModel) && $type === \PPLBaseDisabledRule::class;

    }
}