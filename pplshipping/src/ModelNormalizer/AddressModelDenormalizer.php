<?php
namespace PPLShipping\ModelNormalizer;

use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsCustomerAddressModel;
use PPLShipping\Model\Model\CollectionAddressModel;
use PPLShipping\Model\Model\RecipientAddressModel;
use PPLShipping\Model\Model\SenderAddressModel;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class AddressModelDenormalizer implements DenormalizerInterface
{

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof \PPLAddress)
        {
            if ($type === RecipientAddressModel::class)
                return $this->PPLAddressToRecipientAddressModel($data, $context);
            else if ($type === SenderAddressModel::class)
                return $this->PPLAddressToSenderAddressModel($data, $context);
        }
        else if ($data instanceof  RecipientAddressModel && $type === \PPLAddress::class)
            return $this->RecipientAddressModelToPPLAddress($data, $context);
        else if ($data instanceof  SenderAddressModel && $type === \PPLAddress::class)
            return $this->SenderAddressModelToPPLAddress($data, $context);
        else if ($type === RecipientAddressModel::class && $data instanceof \Order)
            return $this->OrderToRecipientAddressModel($data, $context);
        else if ($type === CollectionAddressModel::class && $data instanceof EpsApiMyApi2WebModelsCustomerAddressModel )
        {
            return $this->CplCollectionAddressToCollectionAddressModel($data, $type);
        }
    }

    public function OrderToRecipientAddressModel(\Order $data, array $context){
        $address = new RecipientAddressModel();
        $customer = new \Customer($data->id_customer);
        if ($customer->email)
            $address->setMail($customer->email);
        $shippingAddress = new \Address($data->id_address_delivery);
        $billingAddress = new \Address($data->id_address_invoice);
        $country = new \Country($billingAddress->id_country);

        $address->setPhone($shippingAddress->phone ?: $billingAddress->phone);
        $address->setStreet($shippingAddress->address1 . ' ' . $shippingAddress->address2);
        $address->setCity($shippingAddress->city);
        $address->setZip($shippingAddress->postcode);
        $address->setCountry($country->iso_code);
        if ($shippingAddress->company) {
            $address->setName($shippingAddress->company);
            $address->setName($shippingAddress->firstname . ' ' . $shippingAddress->lastname);
        }
        else if ($shippingAddress->firstname)
        {
            $address->setName($shippingAddress->firstname . ' ' . $shippingAddress->lastname);
        }
        return $address;
    }

    public function RecipientAddressModelToPPLAddress(RecipientAddressModel $data, array $context)
    {
        $address = $context["data"] ?? new \PPLAddress();
        if ($address->lock) {
            $address = new \PPLAddress();
            $address->type = 'recipient';
            $address->hidden = true;
        } else {
            $address->type = 'recipient';
            $address->hidden = true;
        }
        $address->name  =$data->getName();
        if ($data->isInitialized("contact"))
            $address->contact = $data->getContact();
        if ($data->isInitialized("mail"))
            $address->mail = $data->getMail();
        if ($data->isInitialized("phone"))
            $address->phone = $data->getPhone();

        $address->street = $data->getStreet();
        $address->city = $data->getCity();
        $address->zip = $data->getZip();
        $address->country = $data->getCountry();

        return $address;
    }

    public function SenderAddressModelToPPLAddress(SenderAddressModel $data, array $context)
    {
        $address = $context["data"] ?? new \PPLAddress();

        if ($address->lock) {
            $address = new \PPLAddress();
        }

        $address->hidden = true;
        $address->type = 'sender';

        if ($data->isInitialized("addressName"))
            $address->address_name = $data->getAddressName();
        if ($data->isInitialized("name"))
            $address->name = $data->getName();
        if ($data->isInitialized("contact"))
            $address->contact = $data->getContact();
        if ($data->isInitialized("mail"))
            $address->mail = $data->getMail();
        if ($data->isInitialized("phone"))
            $address->phone = $data->getPhone();
        if ($data->isInitialized("note"))
            $address->note = $data->getNote();
        if ($data->isInitialized("street"))
            $address->street = $data->getStreet();
        if ($data->isInitialized("city"))
            $address->city = $data->getCity();
        if ($data->isInitialized("zip"))
            $address->zip = $data->getZip();
        if ($data->isInitialized("country"))
            $address->country = $data->getCountry();
        $address->lock = false;


        return $address;
    }

    public function PPLAddressToRecipientAddressModel(\PPLAddress $data, array $context = [])
    {
        $address = new RecipientAddressModel();

        $address->setCity($data->city);
        $address->setName($data->name);
        $address->setZip($data->zip);
        $address->setStreet($data->street);
        $address->setCountry($data->country);
        if ($data->phone)
            $address->setPhone($data->phone);
        if ($data->mail)
            $address->setMail($data->mail);
        if ($data->contact)
            $address->setContact($data->contact);

        return $address;
    }

    public function PPLAddressToSenderAddressModel(\PPLAddress $data, array $context = [])
    {
        $address = new SenderAddressModel();
        $address->setAddressName($data->address_name);
        $address->setCity($data->city);
        $address->setName($data->name);
        $address->setZip($data->zip);
        $address->setStreet($data->street);
        $address->setCountry($data->country);
        if ($data->phone)
            $address->setPhone($data->phone);
        if ($data->mail)
            $address->setMail($data->mail);
        if ($data->contact)
            $address->setContact($data->contact);
        if ($data->id)
            $address->setId($data->id);
        if ($data->note)
            $address->setNote($data->note);

        return $address;
    }


    private function CplCollectionAddressToCollectionAddressModel(EpsApiMyApi2WebModelsCustomerAddressModel $data, string $type)
    {
        /**
         * @var EpsApiMyApi2WebModelsCustomerAddressModel $data
         */
        $collectionAddress = new CollectionAddressModel();
        $collectionAddress->setCity($data->getCity());
        $collectionAddress->setStreet($data->getStreet());
        $collectionAddress->setCountry($data->getCountry());
        $collectionAddress->setZip($data->getZipCode());
        $collectionAddress->setName(trim($data->getName() . ' ' . $data->getName2()));
        $collectionAddress->setCode(trim($data->getCode()));
        $collectionAddress->setDefault($data->getDefault());

        return $collectionAddress;

    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof \PPLAddress && in_array($type, [ RecipientAddressModel::class, SenderAddressModel::class], true)
            || ($data instanceof RecipientAddressModel || $data instanceof  SenderAddressModel) && $type === \PPLAddress::class
            || $type === RecipientAddressModel::class && $data instanceof \Order
            || $type === CollectionAddressModel::class && $data instanceof EpsApiMyApi2WebModelsCustomerAddressModel
            ;
    }
}