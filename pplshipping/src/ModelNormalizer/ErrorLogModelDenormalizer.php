<?php
namespace PPLShipping\ModelNormalizer;

use PPLShipping\Model\Model\CategoryRulesModel;
use PPLShipping\Model\Model\ErrorLogCategorySettingModel;
use PPLShipping\Model\Model\ErrorLogItemModel;
use PPLShipping\Model\Model\ErrorLogModel;
use PPLShipping\Model\Model\ErrorLogProductSettingModel;
use PPLShipping\Model\Model\ErrorLogShipmentSettingModel;
use PPLShipping\Model\Model\ProductRulesModel;
use PPLShipping\Model\Model\ShipmentMethodSettingModel;
use PPLShipping\Model\Model\ShipmentModel;
use PPLShipping\CPLOperation;
use PPLShipping\Setting\MethodSetting;
use PPLShipping\Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ErrorLogModelDenormalizer implements DenormalizerInterface
{
    public function supportsDenormalization($data, string $type, ?string $format = null)
    {
        return $data instanceof ErrorLogModel && $type === ErrorLogModel::class;
    }

    private function getOrder($orderId)
    {
        $order = new \Order($orderId);
        if (!$order->id)
            return null;

        $export = [];
        $export['id'] = $order->id;
        $export['reference'] = $order->reference;
        $export['id_cart'] = $order->id_cart;
        $export['current_state'] = $order->current_state;
        $export['payment'] = $order->payment;
        $export['module'] = $order->module;
        $export['total_paid'] = $order->total_paid;
        $export['total_shipping'] = $order->total_shipping;
        $export['total_discounts'] = $order->total_discounts;
        $export['total_products'] = $order->total_products;
        $export['id_currency'] = $order->id_currency;
        $export['id_carrier'] = $order->id_carrier;
        $export['date_add'] = $order->date_add;
        $export['date_upd'] = $order->date_upd;

        $currency = new \Currency($order->id_currency);
        $export['currency_iso'] = $currency->iso_code;

        // Dodací adresa
        $deliveryAddress = new \Address($order->id_address_delivery);
        $deliveryCountry = new \Country($deliveryAddress->id_country);
        $export['shipping'] = [
            'firstname' => $deliveryAddress->firstname,
            'lastname' => $deliveryAddress->lastname,
            'company' => $deliveryAddress->company,
            'address1' => $deliveryAddress->address1,
            'address2' => $deliveryAddress->address2,
            'city' => $deliveryAddress->city,
            'postcode' => $deliveryAddress->postcode,
            'country' => $deliveryCountry->iso_code,
            'phone' => $deliveryAddress->phone,
            'phone_mobile' => $deliveryAddress->phone_mobile,
        ];

        // Fakturační adresa
        $invoiceAddress = new \Address($order->id_address_invoice);
        $invoiceCountry = new \Country($invoiceAddress->id_country);
        $export['billing'] = [
            'firstname' => $invoiceAddress->firstname,
            'lastname' => $invoiceAddress->lastname,
            'company' => $invoiceAddress->company,
            'address1' => $invoiceAddress->address1,
            'address2' => $invoiceAddress->address2,
            'city' => $invoiceAddress->city,
            'postcode' => $invoiceAddress->postcode,
            'country' => $invoiceCountry->iso_code,
            'phone' => $invoiceAddress->phone,
        ];

        // Položky objednávky
        $export['items'] = [];
        foreach ($order->getProducts() as $item) {
            $export['items'][] = [
                'product_id' => $item['product_id'],
                'product_attribute_id' => $item['product_attribute_id'],
                'name' => $item['product_name'],
                'quantity' => $item['product_quantity'],
                'price' => $item['product_price'],
                'total' => $item['total_price_tax_incl'],
                'weight' => $item['weight'],
                'reference' => $item['product_reference'],
            ];
        }

        // Carrier
        $carrier = new \Carrier($order->id_carrier);
        $export['carrier'] = [
            'name' => $carrier->name,
            'id_reference' => $carrier->id_reference,
            'ppl_code' => \Configuration::getGlobalValue("PPLCarrier{$carrier->id_reference}") ?: null,
        ];

        // PPL zásilky
        $shipments = null;
        try {
            $shipments = \PPLShipment::findShipmentsByOrderID($order->id);
            if ($shipments) {
                foreach ($shipments as $k => $shipment) {
                    $shipments[$k] = pplcz_normalize(
                        pplcz_denormalize($shipment, ShipmentModel::class)
                    );
                }
            }
        } catch (\Throwable $ex) {
            $shipments = "Error: " . $ex->getMessage()
                . "\n" . $ex->getFile() . ":" . $ex->getLine();
        }

        $export['pplshipments'] = $shipments;

        return $export;
    }

    public function denormalize($data, string $type, ?string $format = null, array $context = [])
    {
        /**
         * @var ErrorLogModel $data
         */

        $client_id = null;
        $client_secret = null;
        $accessToken = null;
        try {
            $client_secret = \Configuration::getGlobalValue("PPLClientSecret");
            $client_id = \Configuration::getGlobalValue("PPLClientId");

            if ($client_id && $client_secret) {
                $accessToken = (new CPLOperation())->getAccessToken();
            }
        } catch (\Error $e) {
        }

        // === Shipments Setting ===
        $shipmentSettings = [];
        $carriers = \Carrier::getCarriers(
            (int)\Configuration::get('PS_LANG_DEFAULT'),
            false, false, false, null, \Carrier::ALL_CARRIERS
        );

        foreach ($carriers as $carrierData) {
            $carrier = new \Carrier($carrierData['id_carrier']);
            $code = \Configuration::getGlobalValue("PPLCarrier{$carrier->id_reference}");
            if (!$code)
                continue;

            $shipmentSetting = new ErrorLogShipmentSettingModel();
            $shipmentSetting->setName($carrier->name . ' (id_reference: ' . $carrier->id_reference . ', code: ' . $code . ')');

            $method = MethodSetting::getMethod($code);
            if ($method) {
                $methodSetting = new ShipmentMethodSettingModel();
                $methodSetting->setCode($method->getCode());
                $methodSetting->setTitle($method->getTitle());
                $shipmentSetting->setShipmentSetting($methodSetting);
            }

            $zones = $carrier->getZones();
            $zoneNames = array_map(function ($z) {
                $zone = new \Zone($z['id_zone']);
                return $zone->name;
            }, $zones);
            $shipmentSetting->setZones(join(', ', $zoneNames));

            $shipmentSettings[] = $shipmentSetting;
        }
        $data->setShipmentsSetting($shipmentSettings);

        // === Category Setting ===
        $categories = \Category::getCategories(
            (int)\Configuration::get('PS_LANG_DEFAULT'), true, false
        );
        $categoryOutput = [];
        foreach ($categories as $cat) {
            $id = (int)$cat['id_category'];
            $rules = pplcz_denormalize(
                \PPLBaseDisabledRule::getByCagetory($id),
                CategoryRulesModel::class
            );
            if ($rules) {
                $setting = new ErrorLogCategorySettingModel();
                $setting->setName($cat['name']);
                $setting->setId($id);
                $setting->setSetting($rules);
                if (isset($cat['id_parent']) && $cat['id_parent'])
                    $setting->setParent((float)$cat['id_parent']);
                $categoryOutput[] = $setting;
            }
        }
        $data->setCategorySetting($categoryOutput);

        // === Global Parcel Setting ===
        $data->setGlobalParcelSetting(MethodSetting::getGlobalParcelboxesSetting());

        // === Orders ===
        $data->setOrders([]);

        if (isset($context['order_ids']) && $context['order_ids']) {
            $orders = [];
            $product_ids = [];
            foreach ($context['order_ids'] as $orderId) {
                $order = $this->getOrder($orderId);
                if ($order) {
                    $product_ids = array_merge($product_ids, array_map(function ($item) {
                        return $item['product_id'];
                    }, $order['items']));
                    $orders[] = $order;
                }
            }
            if (!isset($context['product_ids']))
                $context['product_ids'] = [];
            $context['product_ids'] = array_unique(
                array_merge($context['product_ids'], $product_ids)
            );
            $data->setOrders($orders);
        }

        // === Products Setting ===
        $products = [];
        if (isset($context['product_ids'])) {
            foreach ($context['product_ids'] as $product_id) {
                $product = new \Product($product_id, false,
                    (int)\Configuration::get('PS_LANG_DEFAULT'));
                if (!$product->id)
                    continue;

                $productSetting = new ErrorLogProductSettingModel();
                $productSetting->setId($product->id);
                $productSetting->setName($product->name);
                $productSetting->setWeight($product->weight ? (float)$product->weight : null);

                $rules = pplcz_denormalize(
                    \PPLBaseDisabledRule::getByProduct($product->id),
                    ProductRulesModel::class
                );
                $productSetting->setSetting($rules);

                $categoryIds = \Product::getProductCategories($product->id);
                if ($categoryIds) {
                    $productSetting->setCategoryIds(array_map('floatval', $categoryIds));
                }

                $products[] = $productSetting;
            }
        }
        $data->setProductsSetting($products);

        // === Info (summary) ===
        $modules = array_filter(array_map(function ($item) {
            if ($item->active) {
                return $item->name . ' - ' . $item->version;
            }
            return null;
        }, \Module::getModulesOnDisk()));

        $presta = _PS_VERSION_;
        $php = phpversion();

        if ($accessToken)
            $accessToken = "ano";
        else
            $accessToken = "ne";

        $summary = [
            "### Přístup",
            "Client ID: $client_id",
            "Získá accessToken: {$accessToken}",
            "***",
            "### Verze",
            "PrestaShop: $presta",
            "PHP: $php",
            "***",
            "### Plugins",
            join("\n", $modules)
        ];
        $data->setMail(\Configuration::get('PS_SHOP_EMAIL'));
        $data->setInfo(join("\n", $summary));

        // === Errors (logs) ===
        $items = [];
        $logs = \PPLLog::GetLogs();
        foreach ($logs as $log) {
            $item = new ErrorLogItemModel();
            $item->setId($log->id);
            $item->setTrace($log->message);
            $items[] = $item;
        }
        $data->setErrors($items);

        return $data;
    }
}
