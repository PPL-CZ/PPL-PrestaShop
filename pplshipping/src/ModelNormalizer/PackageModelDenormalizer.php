<?php
namespace PPLShipping\ModelNormalizer;



use PPLShipping\Model\Model\PackageModel;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class PackageModelDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof \PPLPackage) {
            $model = new PackageModel();
            if($data->id)
                $model->setId($data->id);
            if ($data->weight)
                $model->setWeight($data->weight);

            if ($data->insurance)
                $model->setInsurance($data->insurance);

            if ($data->phase_label)
                $model->setPhaseLabel($data->phase_label);
            else
                $model->setPhaseLabel("");

            $model->setPhase($data->phase ?: "None");

            if ($data->reference_id)
                $model->setReferenceId($data->reference_id);


            if ($data->ignore_phase)
                $model->setIgnorePhase($data->ignore_phase);
            if ($data->last_update_phase)
                $model->setLastUpdatePhase($data->last_update_phase);

            $model->setShipmentNumber($data->shipment_number ?: "");
            $model->setLabelId($data->label_id ?: "");
            $model->setImportError($data->import_error ?: "");
            $model->setImportErrorCode($data->import_error_code ?: "");

            return $model;
        } else if ($data instanceof  PackageModel) {
            /**
             * @var \PPLPackage $model
             */
            $model = $context['data'] ?? new \PPLPackage();
            if ($model->lock)
                $model = new \PPLPackage();

            if ($data->isInitialized("referenceId"))
                $model->reference_id = "{$data->getReferenceId()}";

            $model->weight = $data->getWeight() ?: null;
            $model->insurance = $data->getInsurance() ?: null;
            return $model;
        }
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        if ($data instanceof \PPLPackage && $type === PackageModel::class)
            return true;

        if ($data instanceof PackageModel && $type === \PPLPackage::class)
        {
            return true;
        }
        return false;
    }
}