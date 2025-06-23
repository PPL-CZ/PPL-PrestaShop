<?php

use PPLShipping\Model\Model\CollectionModel;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use PPLShipping\Model\Model\NewCollectionModel;


class AdminCollectionPPLController extends AdminPPLController
{
    public function GetCollections(Request $request)
    {

        if ($request->get("_token") !== $this->getToken())
        {
            return $this->send403();
        }

        $output = array_map(function($item) {
            return pplcz_normalize(pplcz_denormalize($item, CollectionModel::class));

        }, PPLCollection::GetCollections());

        return new JsonResponse($output);
    }

    public function CancelCollection($id, Request $request)
    {
        $cpl = new \PPLShipping\CPLOperation();
        $cpl->cancelCollection($id);

        return new Response("",201);

    }

    public function OrderCollection($id, Request $request)
    {
        $cpl = new \PPLShipping\CPLOperation();
        $cpl->createCollection($id);

        return new Response("", 204);
    }

    public function CreateCollection(Request $request)
    {
        if ($request->get("_token") !== $this->getToken())
        {
            return $this->send403();
        }
        /**
         * @var PPLCollection $collection
         */
        $newcollection = $this->getJson($request, NewCollectionModel::class);
        $error = pplcz_validate($newcollection, "");

        if ($error->errors) {
            return $this->send400($error);
        }

        $collection = pplcz_denormalize($newcollection, PPLCollection::class);
        $collection->reference_id = date("YmdHis");
        $collection->save();
        $collection->reference_id = $collection->id . '#' . date("Ymd");
        $collection->save();

        $cpl = new \PPLShipping\CPLOperation();
        $cpl->createCollection($collection->id);

        return new Response("", 201);

    }

    public function GetLastCollection(Request $request)
    {
        if ($request->get("_token") !== $this->getToken())
        {
            return $this->send403();
        }
        /**
         * @var PPLCollection $collection
         */
        $collection = PPLCollection::GetLastCollection();
        if ($collection) {
            return new JsonResponse(pplcz_normalize(pplcz_denormalize($collection, CollectionModel::class)));
        }
        return new Response("", 404);
    }



    public function GetAddress(Request $request)
    {
        if ($request->get("_token") !== $this->getToken())
        {
            return $this->send403();
        }
        $address = require __DIR__ . '/../../src/config/collection_address.php';

        return new JsonResponse($address);

    }

}