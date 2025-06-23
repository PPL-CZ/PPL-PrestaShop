<?php

function smarty_pplfilterpayments($payment_options, $cart = null)
{
    /**
     * @var \pplshipping $module
     */
    $module = \Module::getInstanceByName("pplshipping");
    $options = [
       'paymentOptions' => &$payment_options,
        "cart" => $cart && $cart instanceof \Cart ? $cart : \Context::getContext()->cart
    ] ;
    $module->hookActionPresentPaymentOptions($options);
    return $payment_options;
}

function smarty_pplfiltershipping($delivery_options, $cart = null)
{
    /**
     * @var \pplshipping $module
     */
    $module = \Module::getInstanceByName("pplshipping");

    $delivery_options_hook = [
        "delivery_option_list_presta17" => &$delivery_options,
        "cart" => $cart && $cart instanceof \Cart ? $cart : Context::getContext()->cart
    ];
    $module->hookActionFilterDeliveryOptionList($delivery_options_hook);

    return $delivery_options_hook['delivery_option_list_presta17'];
}

function smarty_pplprefilter($template, $smarty = null)
{
    $data = '';
    $data2 = '';
    if ($smarty->template_resource && strpos($smarty->template_resource, "shipping.tpl"))
    {
        ob_start();
        ?>
        {if isset($hookDisplayBeforeCarrier) and isset($hookDisplayAfterCarrier) and isset($delivery_options) and isset($cart)}
        {assign var="delivery_options" value=$delivery_options|pplfiltershipping:$cart}
        {/if}
        <?php
        $data = ob_get_clean();
    }

    /**
     * 'is_free' => $isFree,
     * 'payment_options' => $paymentOptions,
     * 'conditions_to_approve' => $conditionsToApprove,
     * 'selected_payment_option' => $this->selected_payment_option,
     * 'selected_delivery_option' => $selectedDeliveryOption,
     * 'show_final_summary' => Configuration::get('PS_FINAL_SUMMARY_ENABLED'),
     */
    if ($smarty->template_resource && strpos($smarty->template_resource, "payment.tpl"))
    {
        ob_start();
        ?>
        {if isset($payment_options) and isset($selected_delivery_option)}
        {assign var="payment_options" value=$payment_options|pplfilterpayments:$cart}
        {/if}
        <?php
        $data2 = ob_get_clean();
    }

    return $data . $data2. $template;
}

function smarty_pplfilter($data)
{
    return array_filter($data);
}

function smarty_ppljoin($data, $joiner)
{
    if (is_array($data))
        return join($joiner, $data);
    return join($data, $joiner);
}

function smarty_pplimageurl($shipment)
{
    if ($shipment instanceof \PPLShipping\Model\Model\ShipmentModel)
    {
        if ($shipment->getHasParcel()){
            if ($shipment->getParcel()) {
                $parcel = $shipment->getParcel();
                if (strtolower($parcel->getType()) === "parcelshop") {
                    return pplcz_asset_icon("parcelshop_2609x1033.png");
                }
                else {
                    return pplcz_asset_icon("parcelbox_2625x929.png");
                }
            }
        }
        return pplcz_asset_icon("ppldhl_4084x598.png");

    }
    return null;
}

function smarty_pplfileurl($item, $package=null)
{
    if ($item instanceof \PPLShipping\Model\Model\ShipmentModel)
    {
        $batchId = $item->getBatchId();
        $params =  [
            "batchId"=>$batchId,
            "shipmentId" => $item->getId(),
        ];

        if ($package instanceof  \PPLShipping\Model\Model\PackageModel)
            $params['packageId'] = $package->getId();
        if ($item->getPrintState())
        {
            $params['print'] = $item->getPrintState();
        }

        try {
            $context = \Context::getContext();
            $controller = $context->controller;
            $router = $controller->get("router");
            return $router->generate("pplshipping_shipmentbatch_download", $params);
        } catch (\Throwable $ex) {
            $url = "/admin/pplshipping/shipmentBatch/{$params['batchId']}/download";
            unset($params['batchId']);
            if (!$params['shipmentId'])
                unset($params['shipmentId']);
            return \Context::getContext()->link->getAdminLink("AdminConfigurationPPL", true, [], ['pplpath' => $url] + $params);
        }
    }
    return "#";
}

function smarty_pplpackageerrors($data) {
    if (is_array($data))
        return array_filter($data, function ($item) {
            return $item->getImportError();
        });
    else
        return $data->getImportError();
}

function smarty_ppllabelprint(\PPLShipping\Model\Model\ShipmentModel  $data)
{
    $query = [
        "ppl_download" => $data->getBatchLabelGroup(),
        "ppl_reference" => $data->getReferenceId(),
    ];

    $url = \Context::getContext()->link->getAdminLink("AdminSettingPPL", true);
    $parsed_url = parse_url($url);
    $urlQuery = [];
    if (isset($parsed_url['query']) && $parsed_url['query']) {
        parse_str($parsed_url['query'], $urlQuery);
        $urlQuery += $query;
    }
    return $parsed_url["scheme"] . '//' . $parsed_url["host"] . (@$parsed_url["path"] ? $parsed_url["path"] : '' ) . "?" . http_build_query($urlQuery);
}

function smarty_pplprintlabel($code) {
    $availablePrinters = (new \PPLShipping\CPLOperation())->getAvailableLabelPrinters();
    foreach ($availablePrinters as $printer)
    {
        if ($printer->getCode() === $code) {
            return $printer->getTitle();
        }
    }
    return null;
}

function smarty_ppljser($data)
{
    if (is_array($data))
    {
        foreach ($data as $key => $value)
        {
            $data[$key] = pplcz_normalize($value);
        }
    }
    else {
        $data = pplcz_normalize($data);
    }
    $data = json_encode($data);
    return $data;
}

function smarty_pplattr($data) {
    return "\"" .  htmlspecialchars($data) . "\"";
}

function smarty_pplisset($data, $key) {

    if (is_array($data)) {
        return isset($data[$key]);
    }
    else if (is_object($data)) {
        return isset($data->$key);
    }
    return false;
}

function smarty_pplarraykeylast($data)
{
    end($data);
    return key($data);
}

function smarty_pplinarray($value, $array)
{
    return in_array($value, $array, true);
}

