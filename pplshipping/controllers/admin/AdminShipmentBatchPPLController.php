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
use PPLShipping\Model\Model\BatchModel;
use PPLShipping\Model\Model\ShipmentWithAdditionalModel;

class AdminShipmentBatchPPLController extends AdminPPLController
{


    public function CreateLabels($id, Request $request)
    {
        /**
         * @var CreateShipmentLabelBatchModel $data
         */
        $data = $this->getJson($request, CreateShipmentLabelBatchModel::class);
        $batch = new \PPLBatch($id);
        if (!$batch->id)
        {
            return new Response(null, 404);
        }

        $print = $data->getPrintSetting();

        if ($print)
        {
            \Configuration::set("PPLPrintSetting", $print);
        }

        $shipmentIds = $data->getShipmentId();
        $batchShipmentIds = array_map(function (PPLShipment $shipment) {
            return $shipment->id;
        }, PPLShipment::findShipmentsByLocalBatchId($batch->id));

        if (array_diff($shipmentIds, $batchShipmentIds) || array_diff($batchShipmentIds, $shipmentIds))
            return new \WP_REST_Response(null, 400);


        $cpl = new CPLOperation();

        try {
            $batch_id = $cpl->createPackages($batch->id);
            $resp =  new Response("", 201);
            $resp->headers->set('x-entity-id', $batch_id );
        }
        catch (\Exception $exception)
        {
            return new Response(null, 500);
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
            }
            $output[] = pplcz_normalize($shipmentModel);
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

        $shipment = PPLShipment::findShipmentsByLocalBatchId($id);

        if (!$shipment)
            return new Response("", 404);

        $shipment = $shipment[0];

        $operations = new CPLOperation();
        $operations->loadingShipmentNumbers([$shipment->batch_id]);

        $refresh = new RefreshShipmentBatchReturnModel();

        $status = false;
        /**
         * @var ShipmentModel[] $output
         */
        $output = array_map(function ($item) use (&$status) {
            if ($item->import_state === 'Complete')
                $status = true;
            return pplcz_normalize(pplcz_denormalize($item, ShipmentModel::class));
        }, \PPLShipment::findShipmentsByLocalBatchId($id));

        $refresh->setShipments($output);
        if (isset($output[0]['batchRemoteId']) )
            $refresh->setBatchs([$output[0]['batchRemoteId']]);
        else
            $refresh->setBatchs([]);

        return  new \Symfony\Component\HttpFoundation\JsonResponse(pplcz_normalize($refresh), $status ? 200 : 204);
    }

    public function PrepareLabels($id, Request $request)
    {

        /**
         * @var PrepareShipmentBatchModel $data
         */
        $data = $this->getJson($request, PrepareShipmentBatchModel::class);
        $errors = new \PPLShipping\Errors();

        $batch = new PPLBatch($id);
        if (!$batch->id)
            return new Response(null, 404);


        $shipmentIds = array_filter(array_map(function ($item) {
            return $item->getShipmentId();
        }, $data->getItems()));

        $batchShipmentIds = array_map(function (PPLShipment $shipment) {
            return $shipment->id;
        }, PPLShipment::findShipmentsByLocalBatchId($batch->id));

        if (array_diff($shipmentIds, $batchShipmentIds) || array_diff($batchShipmentIds, $shipmentIds))
            return new \WP_REST_Response(null, 400);

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
                    $hasError = !!pplcz_validate($shipmentModel, "items.$key")->errors;
                }
            }
            else if ($item->getOrderId())
            {
                $hasError = false;
                $errors->add("item.$key", "Nelze automaticky vytvořit zásilku z objednávky");
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
        }

        $model = new PrepareShipmentBatchReturnModel();
        $model->setShipmentId($output);

        return $this->sendJsonModel($model);
    }

    public function GetBatches(Request $request)
    {
        if ($this->getToken() !== $request->query->get("_token"))
            return $this->send403();

        $free = $request->query->get("free");
        $batches = PPLBatch::findBatches($free);

        foreach ( $batches as $key => $val)
        {
            $val = pplcz_denormalize($val, BatchModel::class);
            $batches[$key] = pplcz_normalize($val);
        }

        return $this->sendJsonModel($batches);
    }

    public function CreateBatch(Request $request)
    {
        if ($this->getToken() !== $request->query->get("_token"))
            return $this->send403();

        $batch = new PPLBatch();
        $batch->save();
        $resp = new Response("", 201);
        $resp->headers->set('x-entity-id', $batch->id );
        return $resp;
    }

    public function GetBatchShipments($batchId, Request $request)
    {
        if ($this->getToken() !== $request->query->get("_token"))
            return $this->send403();


        $shipments = PPLShipment::findShipmentsByLocalBatchId($batchId);
        foreach ($shipments as $key => $shipment)
        {
            $shipment = pplcz_denormalize($shipment, ShipmentWithAdditionalModel::class);
            $shipments[$key] = pplcz_normalize($shipment);
        }

        return $this->sendJsonModel($shipments);
    }

    public function RemoveBatchShipment($batchId, $shipmentId, Request  $request)
    {
        if ($this->getToken() !== $request->query->get("_token"))
            return $this->send403();

        $shipment = new PPLShipment($shipmentId);
        $shipment->id_batch_local = null;
        $shipment->save();

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->setStatusCode(204);
        return $response;
    }

    public function ReorderBatchShipment($batchId, Request  $request)
    {
        if ($this->getToken() !== $request->query->get("_token"))
            return $this->send403();

        $shipments = $this->getJson($request);

        PPLShipment::reorderBatchShipment($batchId, $shipments);

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->setStatusCode(204);
        return $response;
    }

    public function AddBatchShipment($batchId, Request  $request)
    {
        if ($this->getToken() !== $request->query->get("_token"))
            return $this->send403();
        /**
         * @var PrepareShipmentBatchModel $data
         */
        $data = $this->getJson($request, PrepareShipmentBatchModel::class);

        $batchData = new PPLBatch($batchId);

        if (!$batchData->id)
        {
            $response = new Response(null, 404);
            return $response;
        }

        $shipments = PPLShipment::findShipmentsByLocalBatchId($batchId);
        foreach ($data->getItems() as $key => $item)
        {
            if ($item->getShipmentId()) {
                $shipmentData = new PPLShipment($item->getShipmentId());
                if ($shipmentData->id_batch_local == $batchData->id)
                    continue;
                if (!$shipmentData->import_state || $shipmentData->import_state === "None") {
                    $shipmentData->id_batch_local = $batchData->id;
                    $shipmentData->save();
                    $shipments[] = $shipmentData;
                }
            }
            else if ($item->getOrderId())
            {
                $finded = PPLShipment::findShipmentsByOrderID($item->getShipmentId());
                if ($finded)
                    continue;

                $order = new Order($item->getOrderId());
                $carrier = new Carrier($order->id_carrier);
                $hasError = true;
                if ($carrier->is_module && $carrier->external_module_name === "pplshipping")
                {
                    if (Configuration::getGlobalValue("PPLCarrier{$carrier->id_reference}"))
                    {
                        $shipmentModel = pplcz_denormalize($order, ShipmentModel::class);
                        $shipmentData = pplcz_denormalize($shipmentModel, PPLShipment::class);
                        $shipmentData->id_batch_local = $batchData->id;
                        $shipmentData->save();
                        $shipments[] = $shipmentData;
                    }
                }
            }
        }
        PPLShipment::reorderBatchShipment($batchData->id, array_map(function ($item) {
            return $item->id;
        }, $shipments));

        $response = new Response( null, 204);
        $response->headers->set('x-entinty-id', $batchData->id);
        return $response;

    }

}