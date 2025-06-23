<?php

use PPLShipping\Model\Model\PrepareShipmentBatchModel;
use PPLShipping\Model\Model\ShipmentModel;
use PPLShipping\Model\Model\PrepareShipmentBatchReturnModel;
use PPLShipping\Model\Model\ShipmentLabelRefreshBatchModel;
use PPLShipping\CPLOperation;
use PPLShipping\Model\Model\RefreshShipmentBatchReturnModel;
use PPLShipping\Model\Model\CreateShipmentLabelBatchModel;
use PluginPpl\MyApi2\Model\EpsApiInfrastructureWebApiModelProblemJsonModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminShipmentBatchPPLController extends AdminPPLController
{


    public function CreateLabels(Request $request)
    {
        /**
         * @var CreateShipmentLabelBatchModel $data
         */
        $data = $this->getJson($request, CreateShipmentLabelBatchModel::class);
        $error = new \PPLShipping\Errors();
        $cpl = new CPLOperation();
        $isError = false;

        try {
            $batch_id = $cpl->createPackages($data->getShipmentId());
            $resp =  new Response("", 201);
            $resp->headers->set('x-entity-id', $batch_id );
        }
        catch (\Exception $exception)
        {
            $isError = true;
        }

        $output = [];
        $wp_errors = new \PPLShipping\Errors();

        foreach ($data->getShipmentId() as $key => $id) {
            $item = new PPLShipment($id);
            /**
             * @var ShipmentModel $shipmentModel
             */
            $shipmentModel = pplcz_denormalize($item, ShipmentModel::class);
            if ($shipmentModel->getImportErrors() || array_filter($shipmentModel->getPackages(), function (\PPLShipping\Model\Model\PackageModel $item){
                return !!$item->getImportError();
            }) )
            {
                $wp_errors->add("item.$key", "Problém se zásilkou");
            } else
                $item = pplcz_normalize($shipmentModel);
            $output[] = $item;
        }
        if ($wp_errors->errors) {
            return $this->send400($wp_errors);
        }
        return new \Symfony\Component\HttpFoundation\JsonResponse($output,  200);
    }

    public function RefreshLabels($id, Request $request)
    {
        if ($this->getToken() !== $request->query->get("_token"))
            return $this->send403();

        $shipment = PPLShipment::findBatchShipments($id);
        if (!$shipment)
            return new Response("", 404);
        $shipment = $shipment[0];

        $operations = new CPLOperation();
        $operations->loadingShipmentNumbers([$shipment->batch_id]);

        $refresh = new RefreshShipmentBatchReturnModel();

        $status = false;

        $output = array_map(function ($item) use (&$status) {
            if ($item->import_state === 'Complete')
                $status = true;
            return pplcz_normalize(pplcz_denormalize($item, ShipmentModel::class));
        }, \PPLShipment::findBatchShipments($id));

        $refresh->setShipments($output);
        $refresh->setBatchs([$id]);

        return  new \Symfony\Component\HttpFoundation\JsonResponse(pplcz_normalize($refresh), $status ? 200 : 204);
    }

    public function PrepareLabels(Request $request)
    {
        /**
         * @var PrepareShipmentBatchModel $data
         */
        $data = $this->getJson($request, PrepareShipmentBatchModel::class);
        $errors = new \PPLShipping\Errors();

        foreach ($data->getItems() as $key => $item) {
            $hasError = true;
            if ($item->getShipmentId())
            {
                $shipmentData = new PPLShipment($item->getShipmentId());
                if (!$shipmentData->import_state || $shipmentData->import_state === "None") {
                    /**
                     * @var ShipmentModel $shipmentModel
                     */
                    $shipmentModel = pplcz_denormalize($shipmentData, ShipmentModel::class);
                    $hasError = !!pplcz_validate($shipmentModel, "item.$key")->errors;

                }
            }
            else if ($item->getOrderId())
            {
                $order = new Order($item->getOrderId());
                $carrier = new Carrier($order->id_carrier);
                $hasError = true;
                if ($carrier->is_module && $carrier->external_module_name === "pplshipping")
                {
                    if (Configuration::getGlobalValue("PPLCarrier{$carrier->id_reference}"))
                    {
                        $shipmentModel = pplcz_denormalize($order, ShipmentModel::class);
                        $hasError = !!pplcz_validate($shipmentModel, "item.$key")->errors;

                    }
                }

            }
            if ($hasError) {
                $errors->add("item.$key", "Nelze automaticky vytvořit zásilku");
            }
        }

        if ($errors->errors)
            return $this->send400($errors);

        $output = [];

        foreach ($data->getItems() as $key => $item) {
            if ($item->getShipmentId()) {
                $output[$key] = $item->getShipmentId();
            }
            else if ($item->getOrderId())
            {
                $order = new \Order($item->getOrderId());
                $shipmentModel = pplcz_denormalize($order, ShipmentModel::class);
                $shipmentData = pplcz_denormalize($shipmentModel, PPLShipment::class);
                $shipmentData->importErrors = null;
                $shipmentData->save();
                $output[$key] = $shipmentData->id;
            }
        }

        $model = new PrepareShipmentBatchReturnModel();
        $model->setShipmentId($output);

        return $this->sendJsonModel($model);

    }

}