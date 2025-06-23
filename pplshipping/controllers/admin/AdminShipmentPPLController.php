<?php

use PPLShipping\Model\Model\UpdateShipmentModel;
use PPLShipping\Model\Model\UpdateShipmentSenderModel;
use PPLShipping\Model\Model\ShipmentModel;
use PPLShipping\Model\Model\RecipientAddressModel;
use PPLShipping\Model\Model\UpdateShipmentParcelModel;
use PPLShipping\CPLOperation;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class AdminShipmentPPLController extends AdminPPLController
{
    public $ajax = true;

    public function loadShipment($id, $edit = false)
    {
        $shipmentData = new \PPLShipment($id);
        if ($shipmentData->lock && $edit)
            return $this->send403();

        return $shipmentData;
    }

    public function GetShipment($id, Request $request)
    {
        if ($request->query->get("_token") !== $this->getToken())
            return $this->send403();

        $shipmentModel = $this->loadShipment($id);
        if ($shipmentModel instanceof Response)
            return $shipmentModel;

        return $this->sendJsonModel(pplcz_denormalize($shipmentModel, ShipmentModel::class));
    }


    public function CreateShipment(Request $request)
    {
        /**
         * @var UpdateShipmentModel $updateShipmentModel
         */
        $json = $this->getJson($request, UpdateShipmentModel::class);

        $errors = pplcz_validate($json, "");

        if ($errors->errors)
            return $this->send400($errors);

        $pplshipment = pplcz_denormalize($json, PPLShipment::class);
        $pplshipment->save();

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set("x-entity-id",  $pplshipment->id);
        $response->setStatusCode(201);
        return $response;

    }

    public function UpdateShipment($id, Request $request)
    {
        /**
         * @var UpdateShipmentModel $updateShipmentModel
         */
        $json = $this->getJson($request, UpdateShipmentModel::class);
        /**
         * @var \PPLShipment $shipment
         */
        $shipment = $this->loadShipment($id, true);
        if ($shipment instanceof Response)
            return $shipment;


        $errors = pplcz_validate($json, "");
        if ($errors->errors)
            return $this->send400($errors);


        pplcz_denormalize($json, \PPLShipment::class, ["data" => $shipment]);
        $shipment->import_errors = null;
        $shipment->save();
        return new Response("", 204);
        die();
    }

    public function UpdateRecipientAddress($id, Request $request)
    {
        $data = $this->getJson($request, RecipientAddressModel::class);
        $errors = pplcz_validate($data, "");
        if ($errors->errors)
            return $this->send400($errors);

        $shipment = $this->loadShipment($id, true);
        if ($shipment instanceof Response)
            return $shipment;


        $shipment = pplcz_denormalize($data, PPLShipment::class, ["data" => $shipment]);
        $shipment->import_errors = (null);
        $shipment->save();
        return new Response("", 204);
    }

    public function UpdateSenderAddress($id, Request $request)
    {
        $data = $this->getJson($request, UpdateShipmentSenderModel::class);

        $shipment = $this->loadShipment($id, true);
        if ($shipment instanceof Response)
            return $shipment;

        $shipment->id_sender_address = $data->getSenderId();
        $shipment->import_errors = null;
        $shipment->save();
        http_response_code(204);
        die();
    }

    public function UpdateShipmentParcel($id, Request $request)
    {
        /**
         * @var UpdateShipmentParcelModel $sender
         */
        $sender = $this->getJson($request, UpdateShipmentParcelModel::class);
        $shipment = $this->loadShipment($id, true);
        if ($shipment instanceof Response)
            return $shipment;



        $founded = \PPLParcel::getParcelByRemoteId($sender->getParcelCode());
        if (!$founded) {
            $find = new CPLOperation();
            $esp = $find->findParcel($sender->getParcelCode());
            if (!$esp) {
                return new Response("", 404);
            }
            $model = pplcz_denormalize($esp, \PPLShipping\Model\Model\ParcelAddressModel::class);
            $founded = pplcz_denormalize($model, PPLParcel::class, ["data" => $founded]);
            $founded->save();
        }
        $shipment->id_parcel = $founded->id;
        $shipment->import_errors = null;
        $shipment->save();
        return new Response("", 204);
    }

    public function ShipmentRefreshLabels($id, Request $request)
    {
        if ($request->query->get("_token") !== $this->getToken())
            return $this->send403();

        $shipment = $this->loadShipment($id);
        if ($shipment instanceof Response)
            return $shipment;

        if (!$shipment->id) {
            return new JsonResponse("", 404);
        }
        try {
            (new CPLOperation())->loadingShipmentNumbers([$shipment->batch_id]);
            return new JsonResponse("", 204);
        } catch (\Exception $exception) {

        }
        return new JsonResponse("", 404);
    }

    public function ShipmentRefreshStates($id, Request $request)
    {
        if ($request->query->get("_token") !== $this->getToken())
            return $this->send403();

        $shipment = $this->loadShipment($id);

        if ($shipment instanceof Response)
            return $shipment;

        if (!$shipment->id) {
            return new JsonResponse("", 404);
        }
        try {
            $shipmentModel = pplcz_denormalize($shipment, ShipmentModel::class);
            $ids = array_map(function ($item) {
                return $item->getShipmentNumber();
            },$shipmentModel->getPackages());

            (new CPLOperation())->testPackageStates($ids);
            $output = pplcz_normalize(pplcz_denormalize($shipment, ShipmentModel::class));
            return new JsonResponse($output, 200);
        } catch (\Exception $ex) {

        }
        return new JsonResponse("", 400);
    }


    public function ShipmentCancelPackage($id, $packageId, Request $request)
    {
        if ($request->query->get("_token") !== $this->getToken())
            return $this->send403();

        $package = new \PPLPackage($packageId);
        if (!$package->shipment_number) {
            return new JsonResponse("", 204);
        }
        try {
            (new CPLOperation())->cancelPackage($package->id);
            return new JsonResponse("", 204);
        } catch (\Exception $ex) {
            return new JsonResponse("", 500);
        }
    }

    public function ShipmentRemovePackage($id, Request $request)
    {
        if ($request->query->get("_token") !== $this->getToken())
            return $this->send403();


        $pplshipment = $this->loadShipment($id, true);
        if ($pplshipment instanceof Response)
            return $pplshipment;

        if ($pplshipment->id)
            $shipment = pplcz_denormalize($pplshipment, ShipmentModel::class);

        if (!$shipment) {
            return new JsonResponse("", 404);
        }


        /**
         * @var ShipmentModel $shipment
         */
        $packages = $shipment->getPackages();
        array_pop($packages);
        if ($packages) {
            $shipment->setPackages($packages);
            $pplshipment = pplcz_denormalize($shipment, PPLShipment::class, ["data" => @$pplshipment]);
            $pplshipment->import_errors = null;
            $pplshipment->save();
        } else if (@$pplshipment) {
            $pplshipment->deleteTree();
        }
        return new JsonResponse("", 204);
    }

    public function ShipmentAddPackage($id, Request $request)
    {
        if ($request->query->get("_token") !== $this->getToken())
            return $this->send403();

        $pplshipment = $this->loadShipment($id, true);
        if ($pplshipment instanceof Response)
            return $pplshipment;

        $shipment = pplcz_denormalize($pplshipment, ShipmentModel::class);

        if (!$shipment) {
            return new Response("", 404);
        }

        /**
         * @var ShipmentModel $shipment
         */
        $packages = $shipment->getPackages();
        $packages[] = new \PPLShipping\Model\Model\PackageModel();
        $shipment->setPackages($packages);

        $pplshipment = pplcz_denormalize($shipment, PPLShipment::class, ["data" => @$pplshipment]);
        $pplshipment->import_errors = null;
        $pplshipment->save();
        return new Response("", 204);
    }

    public function ShipmentRemove($id, Request $request)
    {
        if ($this->getToken() !== $request->query->get("_token"))
            return $this->send403();

        $shipment = $this->loadShipment($id, true);
        if ($shipment instanceof Response)
            return $shipment;

        $shipment->deleteTree();
        return new Response("", 204);
    }

}