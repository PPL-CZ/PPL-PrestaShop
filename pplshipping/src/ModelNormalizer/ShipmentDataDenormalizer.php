<?php
namespace PPLShipping\ModelNormalizer;

use PPLShipping\Model\Model\PackageModel;
use PPLShipping\Model\Model\ParcelAddressModel;
use PPLShipping\Model\Model\ParcelDataModel;
use PPLShipping\Model\Model\RecipientAddressModel;
use PPLShipping\Model\Model\SenderAddressModel;
use PPLShipping\Model\Model\ShipmentModel;
use PPLShipping\Model\Model\UpdateShipmentModel;
use PPLShipping\Model\Model\UpdateShipmentSenderModel;
use PPLShipping\Serializer;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use PPLShipping\Symfony\Component\Validator\Constraints\Currency;

class ShipmentDataDenormalizer  implements DenormalizerInterface
{

    public function getServiceCodeFromOrder(\Order $order)
    {
        $id_carrier = $order->id_carrier;
        $carrier = new \Carrier($id_carrier);
        $payment = $order->module;
        if ($carrier)
        {
            $code = \Configuration::getGlobalValue("PPLCarrier{$carrier->id_reference}");
            if (!$code)
                return [
                    null,
                    null,
                    null,
                    null,
                ];
            $services = pplcz_get_services();
            $name = $services[$code];
            $parcelRequired = pplcz_parcel_required($code);
            $isCod =false;
            if ($payment === 'ps_cashondelivery')
            {
                $code = pplcz_get_cod_name($code);
                $isCod = true;
                $name .=  ' (dobírka)';
            }
            if (!$code)
                return [null, null, null, null];
            return [
                $code,
                $name,
                $isCod,
                $parcelRequired
            ];
        }
        return [
            null,
            null,
            null,
            null
        ];
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        if ($data instanceof \PPLShipment && $type == ShipmentModel::class) {
            return $this->ShipmentDataToModel($data, $context);
        }
        else if ($data instanceof \Order && $type == ShipmentModel::class) {
            return $this->OrderToModel($data, $context);
        }
        else if ($data instanceof ShipmentModel && $type == \PPLShipment::class) {
            return $this->ShipmentModelToShipmentData($data, $context);
        }
        else if ($data instanceof UpdateShipmentModel && $type === \PPLShipment::class)
        {
            return $this->UpdateShipmentToData($data, $context);
        }
        else if($data instanceof UpdateShipmentSenderModel && $type === \PPLShipment::class)
        {
            return $this->UpdateShipmentSenderToData($data, $context);
        }
        else if ($data instanceof RecipientAddressModel && $type === \PPLShipment::class)
        {
            return $this->UpdateRecipientToData($data, $context);
        }
    }

    public function ShipmentDataToModel(\PPLShipment $data, array $context)
    {
        $shipmentModel = new ShipmentModel();
        $shipmentModel->setId($data->id);
        $shipmentModel->setImportState($data->import_state);
        $shipmentModel->setImportErrors(array_filter(explode("\n", $data->import_errors ?: ""), "trim"));
        $shipmentModel->setPrintState($data->print_state);
        $shipmentModel->setOrderId($data->id_order);


        if ($data->note)
            $shipmentModel->setNote($data->note);

        if ($data->has_parcel)
            $shipmentModel->setHasParcel($data->has_parcel);

        if ($data->reference_id)
            $shipmentModel->setReferenceId($data->reference_id);

        if ($data->service_code)
            $shipmentModel->setServiceCode($data->service_code);

        if ($data->service_name)
            $shipmentModel->setServiceName($data->service_name);

        if ($data->batch_label_group)
            $shipmentModel->setBatchLabelGroup($data->batch_label_group);

        if ($data->id_batch_local)
            $shipmentModel->setBatchId($data->id_batch_local);


        if ($data->batch_id)
            $shipmentModel->setBatchRemoteId($data->batch_id);

        if($data->cod_value)
            $shipmentModel->setCodValue($data->cod_value);

        if ($data->cod_value_currency)
            $shipmentModel->setCodValueCurrency($data->cod_value_currency);

        if ($data->cod_variable_number)
            $shipmentModel->setCodVariableNumber($data->cod_variable_number);

        $id = $data->id_sender_address;
        if ($id)
        {
            $sender = new \PPLAddress($id);
            if ($sender->id)
                $shipmentModel->setSender(Serializer::getInstance()->denormalize($sender, SenderAddressModel::class));
        }

        $id = $data->id_recipient_address;
        if ($id) {
            $recipient = new \PPLAddress($id);
            if ($recipient->id)
                $shipmentModel->setRecipient(Serializer::getInstance()->denormalize($recipient, RecipientAddressModel::class));
        }

        if ($data->id_parcel)
        {
            $parcel = new \PPLParcel($data->id_parcel);
            if ($parcel) {
                $shipmentModel->setParcel(Serializer::getInstance()->denormalize($parcel, ParcelAddressModel::class));
            }
        }

        if ($data->note)
            $shipmentModel->setNote($data->note);
        if ($data->age)
            $shipmentModel->setAge($data->age);
        if ($data->lock)
            $shipmentModel->setLock($data->lock);

        if ($data->import_errors)
            $shipmentModel->setImportErrors(array_filter(explode("\n", $data->import_errors), "trim"));
        else
            $shipmentModel->setImportErrors([]);

        $packages =array_map(function ($item) {
            $model = new \PPLPackage($item);
            return Serializer::getInstance()->denormalize($model, PackageModel::class);
        }, $data->get_package_ids());

        if (!$packages) {
            $orderId = $shipmentModel->getOrderId();
            $model = new PackageModel();
            $model->setReferenceId($orderId);
            $model->setPhase("None");
            $package = Serializer::getInstance()->denormalize($model, PackageData::class);
            $package->phase = ("None");
            $package->save();
            $data->set_package_ids([$package->id]);
            $data->save();
            $packages[] = $model;
        }

        $shipmentModel->setPackages($packages);
        return $shipmentModel;

    }

    public function OrderToModel(\Order $data, array $context)
    {
        $shipmentModel = new ShipmentModel();

        $shipmentModel->setImportState("None");
        $shipmentModel->setOrderId($data->id);


        if (isset($data->note) && $data->note)
            $shipmentModel->setNote($data->note);

        $dm = date("ymd");
        $count = 10 - strlen($dm);
        $variable = str_pad($dm . $data->id, $count, "0", STR_PAD_LEFT);

        $shipmentModel->setReferenceId($data->id . '#' . date("YdmHi"));
        $shipmentModel->setImportErrors([]);

        list($code, $title, $isCod, $parcel) = $this->getServiceCodeFromOrder($data);

        if ($code)
            $shipmentModel->setServiceCode($code);
        if ($title)
            $shipmentModel->setServiceName($title);

        if ($isCod) {
            $shipmentModel->setCodVariableNumber($variable);
            $shipmentModel->setCodValue($data->getOrdersTotalPaid());
            $currency = new \Currency($data->id_currency);
            $shipmentModel->setCodValueCurrency($currency->iso_code);
        }
        $adresses = \PPLAddress::get_default_sender_addresses( null, $data->id_shop, true);
        if ($adresses)
        {
           $shipmentModel->setSender(Serializer::getInstance()->denormalize($adresses[0], SenderAddressModel::class));
        }
        $shipmentModel->setRecipient(Serializer::getInstance()->denormalize($data, RecipientAddressModel::class));




        if ($parcel) {
            /**
             * @var ParcelDataModel $parcel
             */
            $parceldata = \PPLParcel::getParcelByOrderId($data->id) ?: \PPLParcel::getParcelByCartId($data->id_cart);
            if ($parceldata)
                $parceldata = Serializer::getInstance()->denormalize($parceldata, ParcelAddressModel::class);
            $shipmentModel->setParcel($parceldata);
            $shipmentModel->setHasParcel(true);
        }

        $shipmentModel->setAge("");
        /*
        foreach ($data->get_items() as $item) {
            if ($item instanceof \WC_Order_Item_Product)
            {

                $product = new \WC_Product($item->get_product_id());
                /**
                 * @var ProductModel $age

                $age = Serializer::getInstance()->denormalize($product, ProductModel::class);
                if ($age->getPplConfirmAge18()) {
                    $shipmentModel->setAge("18");
                } else if ($age->getPplConfirmAge15()) {
                    if ($shipmentModel->getAge() < 18)
                        $shipmentModel->setAge("15");
                }

            }
        }
        */
        $packageModel = new PackageModel();
        $packageModel->setReferenceId("{$data->id}");
        $shipmentModel->setPackages([
            $packageModel
        ]);

        return $shipmentModel;
    }

    public function ShipmentModelToShipmentData(ShipmentModel $model, $context)
    {
        $shipmentData = $context["data"] ??  new \PPLShipment();
        if ($shipmentData->lock)
        {
            $oldData = $shipmentData;
            $shipmentData = new \PPLShipment();
            $shipmentData->import_state = "None";
            $shipmentData->id_order = $oldData->id_order;
        } else if (!$shipmentData->id)
        {
            $shipmentData->import_state = "None";
            if ($model->isInitialized("orderId"))
                $shipmentData->id_order = $model->getOrderId();
        }

        $shipmentData->reference_id = $model->getReferenceId();
        if ($model->isInitialized("orderId"))
            $shipmentData->id_order = $model->getOrderId();

        if ($model->isInitialized("note"))
        {
            $shipmentData->note = $model->getNote();
        } else {
            $shipmentData->mote = null;
        }
        if ($model->isInitialized("sender"))
            $shipmentData->id_sender_address = $model->getSender()->getId();

        if ($model->isInitialized("serviceCode")) {
            $shipmentData->service_code = $model->getServiceCode();
            $serviceCode = $model->getServiceCode();
            $services = pplcz_get_all_services();

            $shipmentData->service_name = $services[$serviceCode];
            $isCod = pplcz_get_cod_name($serviceCode);

            if ($isCod) {
                if ($model->isInitialized("codVariableNumber"))
                    $shipmentData->cod_variable_number =($model->getCodVariableNumber());
                if ($model->isInitialized("codValue"))
                    $shipmentData->cod_value = ($model->getCodValue());
                if ($model->isInitialized("codValueCurrency"))
                    $shipmentData->cod_value_currency = $model->getCodValueCurrency();

            } else {
                $shipmentData->cod_variable_number = null;
                $shipmentData->cod_value = null;
                $shipmentData->cod_value_currency = null;
            }

            if (pplcz_parcel_required($serviceCode)) {
                if ($model->isInitialized("hasParcel")
                    && $model->getHasParcel()) {
                    $shipmentData->has_parcel = true;
                    if ($model->isInitialized("parcel"))
                    {
                        $parcel = $model->getParcel();
                        if ($parcel)
                            $shipmentData->id_parcel = $parcel->getId();
                    }
                } else {
                    $shipmentData->has_parcel = (false);
                }
            } else {
                $shipmentData->has_parcel = (false);
            }
        }
        else {
            $shipmentData->has_parcel = false;
        }

        if ($model->isInitialized("packages"))
        {
            $modelPackages = $model->getPackages();
            foreach ($modelPackages as $key => $package) {
                if ($package->getId()) {
                    $packageData = new \PPLPackage($package->getId());
                } else {
                    $packageData = null;
                }
                $modelPackages[$key] = Serializer::getInstance()->denormalize($package, \PPLPackage::class, null, array("data" => $packageData));
                if (!$modelPackages[$key]->phase)
                    $modelPackages[$key]->phase = "None";
                $modelPackages[$key]->save();

                $modelPackages[$key] = $modelPackages[$key]->id;
            }
            $shipmentData->set_package_ids($modelPackages);
        }

        if ($model->isInitialized("recipient")) {
            $recipient = Serializer::getInstance()->denormalize($model->getRecipient(), \PPLAddress::class);
            if (!$recipient->id)
                $recipient->save();
            $shipmentData->id_recipient_address =($recipient->id);
        }

        return $shipmentData;

    }

    public function UpdateShipmentToData(UpdateShipmentModel $model, $context = [])
    {
        $shipmentData = $context["data"] ??  new \PPLShipment();
        if ($shipmentData->lock)
        {
            $oldData = $shipmentData;
            $shipmentData = new \PPLShipment();
            $shipmentData->import_state = ("None");
            $shipmentData->id_order = ($oldData->id_order);
        } else if (!$shipmentData->id)
        {
            $shipmentData->import_state = ("None");
            if ($model->isInitialized("orderId"))
                $shipmentData->id_order = ($model->getOrderId());
        }

        if ($model->getReferenceId())
            $shipmentData->reference_id = ($model->getReferenceId());
        if ($model->isInitialized("orderId"))
            $shipmentData->id_order = ($model->getOrderId());

        if ($model->isInitialized("age")) {
            $shipmentData->age = ($model->getAge());
        } else {
            $shipmentData->age = (null);
        }

        if ($model->isInitialized("note"))
        {
            $shipmentData->note = ($model->getNote());
        } else {
            $shipmentData->note = (null);
        }

        if ($model->isInitialized("senderId"))
        {
            $shipmentData->id_sender_address = ($model->getSenderId());
        }

        if ($model->isInitialized("serviceCode"))
        {
            $shipmentData->service_code = ($model->getServiceCode());
            $serviceCode = $model->getServiceCode();
            $shipmentData->service_name = (pplcz_get_all_services()[$serviceCode]);

            if (pplcz_get_cod_name($serviceCode) && $serviceCode === pplcz_get_cod_name($serviceCode)) {
                if ($model->isInitialized("codVariableNumber"))
                    $shipmentData->cod_variable_number = ($model->getCodVariableNumber());
                if ($model->isInitialized("codValue"))
                    $shipmentData->cod_value = ($model->getCodValue());
                if ($model->isInitialized("codValueCurrency"))
                    $shipmentData->cod_value_currency = ($model->getCodValueCurrency());
                /*
                if ($model->isInitialized("codBankAccountId"))
                    $shipmentData->set_cod_bank_account_id($model->getCodBankAccountId());
                */
            }
            else {
                $shipmentData->cod_variable_number = (null);
                $shipmentData->cod_value = (null);
                $shipmentData->cod_bank_account_id = (null);
                $shipmentData->cod_value_currency = (null);
            }

            if (pplcz_parcel_required($serviceCode)) {
                if ($model->isInitialized("hasParcel") && $model->getHasParcel())
                {
                    $shipmentData->has_parcel = true;
                    if ($model->isInitialized("parcelId")) {
                        $parceldata = new \PPLParcel($model->getParcelId());
                        if ($parceldata->id)
                        {
                            $shipmentData->parcel_id = $parceldata->id;
                        }

                    }
                }
                else
                {
                    $shipmentData->has_parcel = (false);
                }
            }
            else {
                $shipmentData->has_parcel = (false);
                $shipmentData->parcel_id = (null);
            }
        }

        if ($model->isInitialized("packages"))
        {
            $modelPackages = $model->getPackages();
            foreach ($modelPackages as $key => $package) {
                if ($package->isInitialized("id") && $package->getId()) {
                    $packageData = new \PPLPackage($package->getId());
                } else {
                    $packageData = null;
                }
                $modelPackages[$key] = Serializer::getInstance()->denormalize($package, \PPLPackage::class, null, array("data" => $packageData));
                if (!$modelPackages[$key]->phase)
                    $modelPackages[$key]->phase = "None";
                $modelPackages[$key]->save();

                $modelPackages[$key] = $modelPackages[$key]->id;
            }
            $shipmentData->set_package_ids($modelPackages);
        }

        if (!$shipmentData->id) {
            if ($shipmentData->id_order)
            {
                $order = new \Order($shipmentData->id_order);
                /**
                 * @var ShipmentModel $normalizer
                 */
                $normalizer = Serializer::getInstance()->denormalize($order, ShipmentModel::class);
                if ($normalizer->isInitialized('recipient'))
                {
                    $recipient = $normalizer->getRecipient();
                    $recipient = Serializer::getInstance()->denormalize($recipient, \PPLAddress::class);
                    $recipient->save();
                    $shipmentData->recipient_address_id = ($recipient->id);
                }
            }
        }

        return $shipmentData;
    }

    public function UpdateShipmentSenderToData(UpdateShipmentSenderModel $sender, $context)
    {
        /**
         * @var \PPLShipment $shipment
         */
        if (!isset($context['data']))
            throw new \Exception("Undefined shipment");
        $shipment = $context["data"];
        if ($sender->isInitialized("senderId")) {
            if ($sender->getSenderId()) {
                $sender = new \PPLAddress($sender->getSenderId());
                $shipment->id_sender_address = $sender->id;
            }
            else
                $shipment->id_sender_address = (null);
        }
        return $shipment;
    }

    public function UpdateRecipientToData(RecipientAddressModel $recipientAddressModel, $context)
    {
        if (!isset($context["data"]))
            throw new \Exception("Undefined shipment");;
        $shipment = $context["data"];
        /**
         * @var \PPLShipment $shipment
         */
        $id = $shipment->id_recipient_address;
        $founded = new \PPLAddress($id);
        $founded->type = "recipient";
        $address = Serializer::getInstance()->denormalize($recipientAddressModel, \PPLAddress::class,null, ["data" =>$founded]);
        $address->save();
        $shipment->id_recipient_address = ($address->id);
        return $shipment;
    }

    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        if($data instanceof \PPLShipment && $type === ShipmentModel::class)
            return true;
        if ($data instanceof \Order && $type === ShipmentModel::class)
            return true;
        if ($data instanceof UpdateShipmentModel && $type === \PPLShipment::class)
            return true;
        if ($data instanceof UpdateShipmentSenderModel && $type === \PPLShipment::class)
            return true;
        if ($data instanceof RecipientAddressModel && $type === \PPLShipment::class)
            return true;
        if ($data instanceof ShipmentModel && $type == \PPLShipment::class) {
            return true;
        }

        return false;
    }
}