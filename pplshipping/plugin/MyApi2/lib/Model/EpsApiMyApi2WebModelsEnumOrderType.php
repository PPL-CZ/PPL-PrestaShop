<?php
/**
 * EpsApiMyApi2WebModelsEnumOrderType
 *
 * PHP version 7.4
 *
 * @category Class
 * @package  PluginPpl\MyApi2
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */

/**
 * CPL
 *
 * **Changelog**    * 2024-08-22 - SPRJ-13791 - validace PRTE / PRBC externích čísel - nelze použít najednou    - /shipment/batch    * 2024-07-01 - SPRJ-13838 - přidání    - /customer/address    * 2023-11-23 - SPRJ-12703 - CPL - /shipment - timestamp    - /shipment - Rozšíření výstupu o LastUpdateDate.    * 2023-07-13 - SPRJ-11888 - přidání    - /codelist/status - číselník statusů    * 2023-07-13 - SPRJ-11953 - přidání    - /order/cancel - storno objednávky
 *
 * The version of the OpenAPI document: v1
 * Generated by: https://openapi-generator.tech
 * OpenAPI Generator version: 6.0.1
 */

/**
 * NOTE: This class is auto generated by OpenAPI Generator (https://openapi-generator.tech).
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace PluginPpl\MyApi2\Model;
use \PluginPpl\MyApi2\ObjectSerializer;

/**
 * EpsApiMyApi2WebModelsEnumOrderType Class Doc Comment
 *
 * @category Class
 * @description Order type
 * @package  PluginPpl\MyApi2
 * @author   OpenAPI Generator team
 * @link     https://openapi-generator.tech
 */
class EpsApiMyApi2WebModelsEnumOrderType
{
    /**
     * Possible values of this enum
     */
    public const COLLECTION_ORDER = 'CollectionOrder';

    public const TRANSPORT_ORDER = 'TransportOrder';

    /**
     * Gets allowable values of the enum
     * @return string[]
     */
    public static function getAllowableEnumValues()
    {
        return [
            self::COLLECTION_ORDER,
            self::TRANSPORT_ORDER
        ];
    }
}


