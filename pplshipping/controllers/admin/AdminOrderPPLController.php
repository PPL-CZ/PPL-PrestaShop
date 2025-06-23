<?php

class AdminOrderPPLController extends AdminPPLController {


    public function RedirectOrder($id, \Symfony\Component\HttpFoundation\Request $request)
    {
        $token = $request->query->get("_token");
        if ($token !== $this->getToken()) {
            return $this->send403();
        }

        $link = Context::getContext()->link->getAdminLink('AdminOrders', true, [], ['vieworder' => '', 'id_order' => $id]);
        return new \Symfony\Component\HttpFoundation\RedirectResponse($link . "#pplshippingTab");


    }

    public function RenderOrder($id, \Symfony\Component\HttpFoundation\Request $request)
    {
        $token = $request->query->get("_token");
        if ($token !== $this->getToken()) {
            return $this->send403();
        }
        $module = Module::getInstanceByName("pplshipping");

        /**
         * @var \pplshipping $module
         */
        $content = $module->hookDisplayAdminOrderTabContent([
            'id_order' => $id
        ]);
        return new \Symfony\Component\HttpFoundation\Response($content);
    }

    public function CreateShipment($id, \Symfony\Component\HttpFoundation\Request $request)
    {
        $token = $request->query->get("_token");
        if ($token !== $this->getToken()) {
            return $this->send403();
        }

        $order = new Order($id);
        if (!$order->id) {
           return new \Symfony\Component\HttpFoundation\Response("", 404);
        }

        $shipmentModel = pplcz_denormalize($order, \PPLShipping\Model\Model\ShipmentModel::class);
        $pplshipment = pplcz_denormalize($shipmentModel, PPLShipment::class);

        $pplshipment->save();

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('x-entity-id', $pplshipment->id);
        $response->setStatusCode(201);
        return $response;
    }

}