<?php
namespace PPLShipping\ModelNormalizer;


use PPLShipping\Model\Model\BatchModel;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;


class BatchModelDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof \PPLBatch) {
            $batch = new BatchModel();
            $batch->setId($data->id);
            $batch->setCreated($data->created_at);
            $batch->setLock($data->lock);
            $batch->setRemoteBatchId($data->remote_batch_id);
            return $batch;
        }

        return null;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof \PPLBatch && $type === BatchModel::class;
    }
}