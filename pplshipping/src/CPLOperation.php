<?php
namespace PPLShipping;

use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentResultChildItemModel;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentResultItemModel;
use PPLPackage;
use PPLShipping\GuzzleHttp\HandlerStack;
use PluginPpl\MyApi2\Api\AccessPointApi;
use PluginPpl\MyApi2\Api\AddressWhisperApi;
use PluginPpl\MyApi2\Api\CodelistApi;
use PluginPpl\MyApi2\Api\CustomerApi;
use PluginPpl\MyApi2\Api\DataApi;
use PluginPpl\MyApi2\Api\OrderBatchApi;
use PluginPpl\MyApi2\Api\OrderEventApi;
use PluginPpl\MyApi2\Api\ShipmentApi;
use PluginPpl\MyApi2\Api\ShipmentBatchApi;
use PluginPpl\MyApi2\Api\ShipmentEventApi;
use PluginPpl\MyApi2\ApiException;
use PluginPpl\MyApi2\Configuration;
use PluginPpl\MyApi2\Model\EpsApiInfrastructureWebApiModelProblemJsonModel;
use PluginPpl\MyApi2\Model\EpsApiMyApi2BusinessEnumsConstPageSize;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebConstantsConstLabelFormat;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsEnumOrderType;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsOrderBatchCreateOrderBatchModel;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsOrderBatchOrderModel;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsOrderBatchOrderModelSender;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsOrderEventCancelOrderEventModel;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModel;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModelLabelSettings;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchExternalNumberModel;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchLabelSettingsModelCompleteLabelSettings;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchRecipientAddressModel;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModel;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelCashOnDelivery;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelInsurance;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelSender;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelShipmentSet;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentModelSpecificDelivery;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModel;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentBatchShipmentSetItemModelWeighedShipmentInfo;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentShipmentModel;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentShipmentStates;
use PluginPpl\MyApi2\Model\EpsApiMyApi2WebModelsShipmentTrackAndTraceItemModel;
use PluginPpl\MyApi2\ObjectSerializer;
use PPLShipping\Psr\Http\Message\RequestInterface;
/*
use PPLShipping\Data\AddressData;
use WoocommercePpl\Data\CodBankAccountData;
use WoocommercePpl\Data\CollectionData;
use WoocommercePpl\Data\PackageData;
use WoocommercePpl\Data\ParcelData;
use WoocommercePpl\Data\ShipmentData;
*/
use PPLShipping\Model\Model\LabelPrintModel;
use PPLShipping\Model\Model\WhisperAddressModel;
use PPLShipping\Serializer;


class CPLOperation
{

    public const PROD_VERSION = true;

    public const BASE_URL = "https://api.dhl.com/ecs/ppl/myapi2";
    public const ACCESS_TOKEN_URL = "https://api.dhl.com/ecs/ppl/myapi2/login/getAccessToken";

    public const INTEGRATOR = "4546462";

    public function getAvailableLabelPrinters()
    {
        $available = [
            [
                "title" => "1x etiketa na stránku, tisk do PDF souboru",
                "code" => "1/PDF"
            ],
            [
                "title" => "A4 4x (začíná od 1. pozice) etiketa na stránku, tisk do PDF souboru",
                "code" => "4/PDF"
            ],
            [
                "title" => "A4 4x  (začíná od 2. pozice) etiketa na stránku, tisk do PDF souboru",
                "code" => "4.2/PDF"
            ],
            [
                "title" => "A4 4x  (začíná od 3. pozice) etiketa na stránku, tisk do PDF souboru",
                "code" => "4.3/PDF"
            ],
            [
                "title" => "A4 4x  (začíná od 4. pozice) etiketa na stránku, tisk do PDF souboru",
                "code" => "4.4/PDF"
            ]
        ];

        return array_map(function($item) {
            return Serializer::getInstance()->denormalize($item, LabelPrintModel::class);
        }, $available);
    }

    public function getFormat($format)
    {
        switch($format) {
            case '1/PDF':
            case '4/PDF':
            case '4.2/PDF':
            case '4.3/PDF':
            case '4.4/PDF':
                return $format;
        }
        return "4/PDF";
    }

    public function reset()
    {
        \Configuration::deleteByName("PPLAccessToken");

    }

    public function clearAccessToken()
    {
        \Configuration::deleteByName("PPLAccessToken");
    }

    public function getAccessToken()
    {
        $content = \Configuration::getGlobalValue("PPLAccessToken");

        if ($content) {

            list($a, $b, $c) = explode(".", $content);
            if ($b) {
                $b = json_decode(base64_decode($b), true);
                if ($b["exp"] > time() - 20) {
                    return $content;
                }
            }
        }
        $client_secret = \Configuration::getGlobalValue("PPLClientSecret");
        $client_id = \Configuration::getGlobalValue("PPLClientId");

        if (!$client_secret || !$client_secret)
            return null;

        $auth = "Basic " . base64_encode("$client_id:$client_secret");

        $headers = ["Content-Type: application/x-www-form-urlencoded"];
        if (strpos(self::ACCESS_TOKEN_URL, "getAccessToken") === false) {
            $headers[] = "Authorization: $auth";
        }

        $content = ["grant_type" => "client_credentials"];
        if (strpos(self::ACCESS_TOKEN_URL, "getAccessToken") !== false) {
            $content["client_id"] = $client_id;
            $content["client_secret"] = $client_secret;
        }

        $opts = array('http' =>
            array(
                'ignore_errors' => true,
                'timeout' => 5,
                'method' => 'POST',
                'header' => join("\r\n", $headers),
                'content' => http_build_query($content),
            ));

        $context = stream_context_create($opts);
        $url = self::ACCESS_TOKEN_URL;
        $content = @file_get_contents("{$url}", false, $context);

        if (strpos($http_response_header[0], "200 OK")) {
            if ($content) {
                $tokens = json_decode($content, true);
                \Configuration::updateGlobalValue("PPLAccessToken", $tokens["access_token"]);
                \Configuration::deleteByName("PPLAccessTokenError");
                return $tokens["access_token"];
            }
        } else {
            $errorMaker = "Url: {$url}\n";
            $errorMaker .= join("\n", $http_response_header);
            if ($content)
                $errorMaker .= "\n" . $content;
            else
                $errorMaker .= "\nno content";
            \Configuration::updateGlobalValue("PPLAccessTokenError", $errorMaker);
        }

        return null;
    }

    public function createClientAndConfiguration()
    {
        $handler = HandlerStack::create();
        $handler->push(function ( $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                if ($request->getMethod() === "GET" || $request->getMethod() === "OPTIONS" || $request->getMethod() === "HEAD") {
                    $request = $request->withoutHeader("Content-Type");
                }
                else if ($request->getMethod() === "POST" || $request->getMethod() === "PUT" || $request->getMethod() === "PATCH") {
                    if (!$request->hasHeader("Content-Length")) {
                        $request = $request->withAddedHeader("Content-Length", $request->getBody()->getSize());
                        if (!$request->getBody()->getSize())
                            $request = $request->withoutHeader("Content-Type");
                    }
                }
                return $handler($request, $options);
            };
        });


        $client = new \PPLShipping\GuzzleHttp\Client([
            "handler" => $handler
        ]);

        $configuration = new Configuration();
        $configuration->setAccessToken($this->getAccessToken());
        $url = self::BASE_URL;
        $configuration->setHost($url);

        return [$client, $configuration];
    }

    /**
     * @param int[] $shipments
     * @return void
     * @throws ApiException
     *
     * Vytvoření zásilek
     */
    public function createPackages($shipments = [])
    {

        $shipments = array_map(function ($item)
        {
            $shipment = new \PPLShipment($item);
            $shipment->lock();
            return $shipment;
        }, $shipments);

        /**
         * @var EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModel $creator
         */
        $creator = pplcz_denormalize($shipments, EpsApiMyApi2WebModelsShipmentBatchCreateShipmentBatchModel::class);

        list($client, $configuration) = $this->createClientAndConfiguration();
        $shipmentBatchApi = new ShipmentBatchApi($client, $configuration);

        try {
            $output = $shipmentBatchApi->createShipmentsWithHttpInfo($creator, "cs-CZ");
            $location = reset($output[2]["Location"]);
            $location = explode("/", $location);
            $batch_id = end($location);

            foreach ($shipments as $shipment) {
                $shipment->import_state = "InProgress";
                $shipment->batch_id = $batch_id;
                $shipment->import_errors = null;
                $shipment->lock = true;
                $shipment->save();
            }
            return $batch_id;
        }
        catch (\Exception $ex) {

            foreach ($shipments as $position => $shipment) {
                $shipment->unlock();
                if ($ex instanceof  ApiException && $ex->getResponseObject() instanceof  EpsApiInfrastructureWebApiModelProblemJsonModel) {
                    /**
                     * @var array<string,string[]> $error
                     */
                    $errors = [];
                    $responseErrors = $ex->getResponseObject()->getErrors();
                    foreach ($responseErrors as $errorKey =>$error )
                    {
                        $arguments = [];
                        if (preg_match('/^shipments\[([0-9]+)]($|\.)/i', $errorKey, $arguments )){
                            if ("{$arguments[1]}" === "$position") {
                                foreach ($error as $err) {
                                    $errors[] = "{$err}";
                                }
                            }

                        }
                    }
                    if ($errors) {
                        $errors = join("\n", $errors);
                        $shipment->import_errors = $errors;
                        $shipment->import_state = "None";
                        $shipment->save();
                    }
                } else {

                    $shipment->import_errors = $ex->getMessage();
                    $shipment->import_state = "None";
                    $shipment->save();
                }
            }

            throw $ex;
        }

    }

    /**
     * @param $packageId
     * @return void
     * @throws ApiException
     *
     * Zrušení zásilky
     */
    public function cancelPackage($packageId)
    {
        $package = new PPLPackage($packageId);
        list($client, $configuration) = $this->createClientAndConfiguration();
        $shipmentApi = new ShipmentEventApi($client, $configuration);
        $shipmentNumber = $package->shipment_number;
        $shipmentApi->shipmentShipmentNumberCancelPost($shipmentNumber);
        $package->phase = "Canceled";
        $package->lock = true;
        $package->save();
    }

    /**
     * @param $batchId
     * @return void
     * @throws ApiException
     *
     * Stažení etiket pro zásilky, které byly vytvořeny v rámci jednoho /shipment/batch
     */
    public function getLabelContents($batchId, $referenceId = null, $shipmentNumber = null, $printFormat = null)
    {
        list($client, $configuration) = $this->createClientAndConfiguration();

        $shipmentApi = new ShipmentBatchApi($client, $configuration);

        $format = ($printFormat ?: \Configuration::getGlobalValue("PPLPrintSetting") ?: "");
        $format = $this->getFormat($format);

        switch($format) {
            case '1/PDF':
                $position = 1;
                $format = 'default';
                break;
            case "4.2/PDF":
                $position = 2;
                $format = 'A4';
                break;
            case "4.3/PDF":
                $position = 3;
                $format = 'A4';
                break;
            case "4.4/PDF":
                $position = 4;
                $format = 'A4';
                break;
            default:
                $position = 1;
                $format = 'A4';
                break;
        }

        if (!$referenceId) {
            $httpData = $shipmentApi->getShipmentBatchLabelWithHttpInfo($batchId, 100, 0, $format, $position);
            if (!$httpData) {
                return;
            }
            header("Content-Type: " . $httpData[2]["Content-Type"][0]);
            /**
             * @var \SplFileInfo $file
             */
            $file = $httpData[0];
            $path = $file->getPathname();
            $content = file_get_contents($path);
            exit($content);
        }
        else {
            // načtu si info kolem batch
            $data = $shipmentApi->getShipmentBatch($batchId);
            $items = $data->getItems();
            usort($items, function (EpsApiMyApi2WebModelsShipmentBatchShipmentResultItemModel $first, EpsApiMyApi2WebModelsShipmentBatchShipmentResultItemModel $second) {
                return strcmp($first->getReferenceId(), $second->getReferenceId());
            });

            $offset = 0;
            $founded = false;

            foreach ($items as $item) {
                $isReference = $item->getReferenceId() === $referenceId;
                if ($isReference && $shipmentNumber && $item->getShipmentNumber() === $shipmentNumber) {
                    $founded = $item;
                    break;
                }

                if (!$shipmentNumber && $isReference) {
                    $founded = $item;
                    break;
                }

                $offset++;
                $items2 = $item->getRelatedItems() ?? [];

                usort($items2, function (EpsApiMyApi2WebModelsShipmentBatchShipmentResultChildItemModel $a, EpsApiMyApi2WebModelsShipmentBatchShipmentResultChildItemModel $b) {
                    return strcmp($a->getShipmentNumber(), $b->getShipmentNumber());
                });

                foreach ($items2 as $item2) {
                    if ($isReference && $item2->getShipmentNumber() === $shipmentNumber) {
                        $founded = $item;
                        break 2;
                    }
                    $offset++;
                }

                if ($isReference)
                    throw new \Exception("Problem s nalezením zásilky k tisku");
            }

            if (!$founded)
                throw new \Exception("Problem s nalezením zásilky k tisku");

            $items = $founded->getRelatedItems() ?? [];
            $max = $shipmentNumber ? 1: (count($items) + 1);


            $httpData = $shipmentApi->getShipmentBatchLabelWithHttpInfo($batchId, $max, $offset, $format, $position, null, null, null, "ReferenceId,ShipmentNumber");
            if (!$httpData) {
                return;
            }
            header("Content-Type: " . $httpData[2]["Content-Type"][0]);
            $file = $httpData[0];

            $path = $file->getPathname();
            $content = file_get_contents($path);
            exit($content);
        }
    }


    /**
     * @param $batchIds
     * @return void
     * @throws ApiException
     *
     * Otestování vytvořených zásilek /shipment/batch, zjištění následných chyb a nebo uložení čísla balíku a uložení url na stažení etikety
     *
     */
    public function loadingShipmentNumbers($batchIds = [])
    {
        $batch_label_group = date("Y-m-d H:i:s");
        foreach ($batchIds as $item) {

            list($client, $configuration) = $this->createClientAndConfiguration();

            $shipmentBatchApi = new ShipmentBatchApi($client, $configuration);


            $batchData = $shipmentBatchApi->getShipmentBatchWithHttpInfo($item);

            $batchData = $batchData[0];
            $shipments = \PPLShipment::findBatchShipments($item);


            foreach ($batchData->getItems() as $batchItem) {
                $referenceId = $batchItem->getReferenceId();
                $referenceShipments = array_filter($shipments, function ($item) use ($referenceId) {
                    return $item->reference_id == $referenceId;
                });
                $baseShipmentNumber = $batchItem->getShipmentNumber();
                $errorCode = $batchItem->getErrorCode();
                $errorMessage = $batchItem->getErrorMessage();


                foreach ($referenceShipments as $shipment) {
                    $packages = $shipment->get_package_ids();
                    foreach ($packages as $key => $package)
                    {
                        $packages[$key] = new PPLPackage($package);
                    }

                    /**
                     * @var PPLPackage[] $packages
                     * @var PPLPackage $package
                     */

                    $package = array_filter($packages, function (PPLPackage $item) use ($baseShipmentNumber) {
                        return $item->shipment_number && $item->shipment_number === $baseShipmentNumber;
                    });

                    if (!$package) {
                        $package = array_filter($packages, function (PPLPackage $item) use ($baseShipmentNumber) {
                            return !$item->shipment_number;
                        });
                    }
                    if ($package) {
                        $package = reset($package);
                        /**
                         * @var PPLPackage $package
                         */
                        if ($batchItem->getLabelUrl()) {
                            $label_id = explode("/", $batchItem->getLabelUrl());
                            $label_id = end($label_id);
                            $package->label_id = $label_id;
                        }
                        $package->shipment_number = ($baseShipmentNumber);
                        $package->import_error = ($errorMessage);
                        $package->import_error_code = ($errorCode);
                        $package->save();
                    }

                    $packages = array_filter($packages, function (PPLPackage $item) use($baseShipmentNumber) {
                        return $item->shipment_number !== $baseShipmentNumber;
                    });

                    foreach ($batchItem->getRelatedItems() as $relatedItem) {
                        $shipmentNumber = $relatedItem->getShipmentNumber();

                        $package = array_filter($packages, function (PPLPackage $item) use ($shipmentNumber) {
                            return $item->shipment_number && $item->shipment_number === $shipmentNumber;
                        });

                        if (!$package) {
                            $package = array_filter($packages, function ($item) use ($shipmentNumber) {
                                return !$item->shipment_number;
                            });
                        }

                        if ($package) {
                            $package = reset($package);

                            if ($relatedItem->getLabelUrl()) {
                                $label_id = explode("/", $relatedItem->getLabelUrl());
                                $label_id = end($label_id);
                                $package->label_id = $label_id;
                            }
                            $package->shipment_number = $relatedItem->getShipmentNumber();
                            $package->import_error = $relatedItem->getErrorMessage();
                            $package->import_error_code = $relatedItem->getErrorCode();
                            $package->save();
                        }
                    }
                    if ($batchData->getCompleteLabel()) {
                        $shipment->batch_label_group = $batch_label_group;
                    }
                    else
                        $shipment->batch_label_group = null;

                    $shipment->import_state = $batchItem->getImportState();
                    $shipment->save();
                }
            }
        }
    }

    public function cancelCollection($idcoll)
    {
        $collection = new \PPLCollection($idcoll);
        list($client, $configuration) = $this->createClientAndConfiguration();
        $order = new OrderEventApi($client, $configuration);
        $ev = new EpsApiMyApi2WebModelsOrderEventCancelOrderEventModel();
        $ev->setNote("Zrušeno na vyžádání");
        try {
            $remoteId = $collection->remote_collection_id;
            $order->orderCancelPost(null, $remoteId, null, null, null, $ev);
            $collection->state = "Canceled";
            $collection->save();
        }
        catch (\Exception $ex)
        {
            throw $ex;
        }

    }

    public function createCollection($idcoll)
    {
        $collection = new \PPLCollection($idcoll);
        list($client, $configuration) = $this->createClientAndConfiguration();

        $order = new OrderBatchApi($client, $configuration);
        $modelBatch = new EpsApiMyApi2WebModelsOrderBatchCreateOrderBatchModel();

        $model = new EpsApiMyApi2WebModelsOrderBatchOrderModel();
        $model->setOrderType(EpsApiMyApi2WebModelsEnumOrderType::COLLECTION_ORDER);
        $model->setSendDate(new \DateTime($collection->send_date));
        $model->setProductType("BUSS");
        $model->setReferenceId($collection->reference_id);

        $sender = new EpsApiMyApi2WebModelsOrderBatchOrderModelSender();
        $sender->setEmail($collection->email);
        $sender->setPhone($collection->telephone);
        $sender->setContact($collection->contact);

        $address = require_once  __DIR__ . '/config/collection_address.php';

        $sender->setCity($address['city']);
        $sender->setZipCode($address['zip']);
        $sender->setCountry($address['country']);
        $sender->setStreet($address['street']);

        $model->setSender($sender);

        $model->setShipmentCount($collection->estimated_shipment_count);
        $model->setNote($collection->note);
        $model->setEmail($collection->email);
        $modelBatch->setOrders([$model]);

        $output = $order->createOrdersWithHttpInfo($modelBatch);

        $location = reset($output[2]["Location"]);
        $location = explode("/", $location);
        $batch_id = end($location);

        $collection->remote_collection_id = $batch_id;
        $collection->state = "Created";
        $collection->send_to_api_date = date("Y-m-d");
        $collection->save();

    }



    public function testPackageStates(array $shipmentsNumbers)
    {
        if (!$shipmentsNumbers) {
            return [];
        }

        $statuses = require_once __DIR__ . '/config/statuses.php';

        list($client, $configuration) = $this->createClientAndConfiguration();

        $accessPointApi = new ShipmentApi($client, $configuration);

        $min = count($shipmentsNumbers);

        $content = $accessPointApi->shipmentGetWithHttpInfo($min, 0, $shipmentsNumbers);

        $data = $content[0];

        $returnData = [];

        /**
         * @var EpsApiMyApi2WebModelsShipmentShipmentModel[] $data
         */
        foreach ($data as $item) {
            $trackAndTrace = $item->getTrackAndTrace();
            $shipmentNumber = $item->getShipmentNumber();
            $url = $trackAndTrace->getPartnerUrl();
            $events = $trackAndTrace->getEvents();


            /**
             * @var EpsApiMyApi2WebModelsShipmentTrackAndTraceItemModel $lastEvent
             */

            $lastEvent = end($events);

            $codPayed = array_filter($events, function ($item) {
                return $item->getPhase() === "CodPaidDate";
            });

            if ($lastEvent) {
                $returnData[$shipmentNumber] = [
                    'phase' => $lastEvent->getPhase() === null ? "Canceled" : $lastEvent->getPhase(),
                    'name' => $lastEvent->getName(),
                    "code" => $lastEvent->getCode(),
                    "status"=> $lastEvent->getStatusId(),
                    'url' => $url,
                    'payed' => $codPayed
                ];
            }
        }

        $db = \PPLPackage::findPackagesByShipmentNumber($shipmentsNumbers);


        foreach ($returnData as $shipmentNumber => $data) {

            foreach (array_filter($db,function (\PPLPackage $package) use ($shipmentNumber) {
                return "{$package->shipment_number}" === "$shipmentNumber";
            }) as $key => $package) {
                unset($db[$key]);

                if ($package->phase !== $data['phase']
                    || $package->status !== $data['status'] ) {

                    if ($data['phase'] === null)
                        $data['phase'] = 'Canceled';

                    $package->status = $data["status"];
                    $package->status_label = @$statuses[$data['status']];
                    $package->phase = $data["phase"];
                    $package->phase_label = $data['name'];
                    $package->last_update_phase = date("Y-m-d H:i:s");
                    $package->last_test_phase = date("Y-m-d H:i:s");
                    $package->import_error = null;
                    $package->import_error_code = null;

                    $package->save();

                    if ($data["payed"]) {
                        $shipmentId = $package->id_ppl_shipment;
                        $shipment = new \PPLShipment($shipmentId);
                        $order = $shipment->id_order;
                        if ($order) {
                            $order = new \Order($order);
                            /*
                            $hasCodStatus = $order->get_meta("_" . create_ppl_name("_cod_change_status"));
                            if (!$hasCodStatus) {
                                $order->set_meta_data(["_" . create_ppl_name("_cod_change_status") => true]);
                                $order->set_status("Completed");
                                $order->save();
                            }
                            */
                        }
                    }
                } else {
                    $package->import_error = null;
                    $package->set_import_error_code = null;
                    if (!$package->last_update_phase)
                        $package->last_update_phase = date("Y-m-d H:i:s");
                    $package->set_last_test_phase = date("Y-m-d H:i:s");
                    $package->save();
                }
            }
        }

        foreach ($db as $package)
        {
            $package->import_error = "NotFound";
            $package->import_error_code = "NotFound";
            if (!$package->last_update_phase)
                $package->last_update_phase = date("Y-m-d H:i:s");
            $package->last_test_phase = date("Y-m-d H:i:s");
            $package->save();
        }

    }

    public function findParcel($code)
    {

        list($client, $configuration) = $this->createClientAndConfiguration();

        $accessPointApi = new AccessPointApi($client, $configuration);
        $founded = $accessPointApi->accessPointGet(100,0, $code);
        if (is_array($founded)) {
            return $founded[0];
        }
        return null;
    }

    public function getShipmentPhases()
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();

        $codelistApi = new CodelistApi($client, $configuration);
        $limitApi = $codelistApi->codelistShipmentPhaseGet(300,0);

        $output = [];

        foreach ($limitApi as $key => $val) {
            $output[$val->getCode()] = $val->getName();
        }

        return $output;
    }


    public function getStatuses()
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();

        $codelistApi = new CodelistApi($client, $configuration);
        $limitApi = $codelistApi->codelistStatusGet(300,0);

        $output = [];

        foreach ($limitApi as $key => $val) {
            $output[$val->getCode()] = $val->getName();
        }

        return $output;
    }

    public function getCountries()
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();

        $codelistApi = new CodelistApi($client, $configuration);
        $limitApi = $codelistApi->codelistCountryGet(300,0);

        $output = [];

        foreach ($limitApi as $key => $val) {
            $output[$val->getCode()] = $val->getCashOnDelivery();
        }

        return $output;
    }

    public function getCollectionAddresses()
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();

        $codelistApi = new CustomerApi($client, $configuration);
        $addresses = $codelistApi->customerAddressGet();

        $output = [];

        return $addresses;
    }


    public function getLimits()
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();
        $codelistApi = new CodelistApi($client, $configuration);
        $limitApi = $codelistApi->codelistServicePriceLimitGet(300, 0);
        $insrs = [];
        $cods = [];

        foreach ($limitApi as $item) {
            if ($item->getService() === "INSR") {
                $insrs[] = [

                    "product" => $item->getProduct(),
                    "min" => $item->getMinPrice(),
                    "max" => $item->getMaxPrice(),
                    "currency" => $item->getCurrency()
                ];
            }
            else if ($item->getService() === "COD")
            {
                $cods[] = [
                    "product" => $item->getProduct(),
                    "min" => $item->getMinPrice(),
                    "max" => $item->getMaxPrice(),
                    "currency" => $item->getCurrency()
                ];
            }
        }
        return [
            'COD' => $cods,
            "INSURANCE" => $insrs
        ];
    }

    public function getCodCurrencies()
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken)
            return false;

        list($client, $configuration) = $this->createClientAndConfiguration();

        $customerApi = new CustomerApi($client, $configuration);
        $content = $customerApi->customerGet();
        $currencies = [];
        foreach ($content->getAccounts() as $item) {
            $currencies[] = [
                'country' => $item->getCountry(),
                'currency' => $item->getCurrency(),
            ];
        }
        return $currencies;
    }

    public function whisper($street = null, $city =null, $zip = null)
    {
        $accessToken = $this->getAccessToken();
        if ($accessToken && ($street || $city || $zip)) {
            list($client, $configuration) = $this->createClientAndConfiguration();

            $whisper = new AddressWhisperApi($client, $configuration);
            $founded = $whisper->addressWhisperGet($street, $zip ? trim($zip): null, $city ? trim($city) : null, trim($city) ? 'City' : 'Street');
            $output = [];
            foreach ($founded as $key => $item) {
                $wp = new WhisperAddressModel();
                if ($item->getCity())
                    $wp->setCity($item->getCity());
                if ($item->getStreet())
                    $wp->setStreet($item->getStreet());
                if ($item->getZipCode())
                    $wp->setZipCode($item->getZipCode());
                if ($item->getEvidenceNumber())
                    $wp->setEvidenceNumber($item->getEvidenceNumber());
                if ($item->getHouseNumber())
                    $wp->setHouseNumber($item->getHouseNumber());
                $output[] = $wp;
            }
            return $output;
        }
        return [];
    }
}