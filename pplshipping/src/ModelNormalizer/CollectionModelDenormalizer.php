<?php
namespace  PPLShipping\ModelNormalizer;

use PPLShipping\Model\Model\CollectionModel;
use PPLShipping\Model\Model\NewCollectionModel;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class CollectionModelDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof \PPLCollection && $type == CollectionModel::class) {
            $collection = new CollectionModel();
            $collection->setId($data->id);
            $collection->setShipmentCount($data->shipment_count);
            $collection->setEstimatedShipmentCount($data->estimated_shipment_count);
            $collection->setCreatedDate($data->created_date);

            if ($data->send_date)
                $collection->setSendDate($data->send_date);
            if ($data->send_to_api_date)
                $collection->setSendToApiDate($data->send_to_api_date);

            if ($data->reference_id)
                $collection->setReferenceId($data->reference_id);
            if ($data->state)
                $collection->setState($data->state);
            if ($data->email)
                $collection->setEmail($data->email);
            if ($data->telephone)
                $collection->setTelephone($data->telephone);
            if ($data->contact)
                $collection->setContact($data->contact);
            if ($data->remote_collection_id)
                $collection->setRemoteCollectionId($data->remote_collection_id);

            return $collection;
        }
        else if ($data instanceof NewCollectionModel && $type == \PPLCollection::class)
        {
            $collection = @$context["data"] ?: new \PPLCollection();

            $collection->state = "BeforeSend";
            $collection->send_date = $data->getSendDate();
            $collection->created_date = date("Y-m-d");
            $collection->estimated_shipment_count = $data->getEstimatedShipmentCount();
            $collection->shipment_count = $collection->estimated_shipment_count;
            if ($data->isInitialized("note"))
                $collection->note = $data->getNote();

            if ($data->isInitialized("contact"))
                $collection->contact = $data->getContact();

            if ($data->isInitialized("telephone"))
                $collection->telephone = $data->getTelephone();
            if ($data->isInitialized("email"))
                $collection->email = $data->getEmail();
            return $collection;
        }

        throw new \Exception("Unsupported denormalize");
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $type === CollectionModel::class && $data instanceof \PPLCollection
                || $type ===\PPLCollection::class && $data instanceof NewCollectionModel;
    }
}