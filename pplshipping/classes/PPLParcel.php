<?php

class PPLParcel extends ObjectModel
{
    public $id;

    public $type;

    public $name;

    public $name2;

    public $street;

    public $street2;

    public $city;

    public $zip;

    public $code;

    public $remote_id;

    public $country;

    public $lat;

    public $lng;

    public static function getParcelByRemoteId($code)
    {
        $prefix = _DB_PREFIX_;
        $row = \Db::getInstance()->getRow("select parcel.* from {$prefix}ppl_parcel parcel where parcel.remote_id = '" . \Db::getInstance()->escape($code) . "'");
        $parcel = null;
        if ($row)
        {
            $parcel = new PPLParcel();
            $parcel->hydrate($row);
        }

        return $parcel;
    }

    public static function getParcelByCartId($cart_id)
    {
        $prefix = _DB_PREFIX_;
        $row = \Db::getInstance()->getRow("select parcel.* from {$prefix}ppl_parcel parcel join {$prefix}ppl_cart cart on cart.id_ppl_parcel = parcel.id_ppl_parcel where cart.id_cart = " . (int)$cart_id);
        $parcel = null;
        if ($row)
        {
            $parcel = new PPLParcel();
            $parcel->hydrate($row);
        }

        return $parcel;
    }

    public static function getParcelByOrderId($order_id)
    {
        $prefix = _DB_PREFIX_;
        $row = \Db::getInstance()->getRow("select parcel.* from {$prefix}ppl_parcel parcel join {$prefix}ppl_order orders on orders.id_ppl_parcel = parcel.id_ppl_parcel where orders.id_order = " . (int)$order_id);
        $parcel = null;
        if ($row)
        {
            $parcel = new PPLParcel();
            $parcel->hydrate($row);
        }

        return $parcel;
    }

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'ppl_parcel',
        'primary' => 'id_ppl_parcel',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => [
            'type' => ["type"=>self::TYPE_STRING],
            'name' => ["type"=>self::TYPE_STRING],
            'name2' => ["type"=>self::TYPE_STRING],
            'street' => ["type"=>self::TYPE_STRING],
            'street2' => ["type"=>self::TYPE_STRING],
            'city' => ["type"=>self::TYPE_STRING],
            'zip' => ["type"=>self::TYPE_STRING],
            'code' =>["type"=>self::TYPE_STRING],
            'remote_id' =>["type"=>self::TYPE_STRING],
            'country' =>["type"=>self::TYPE_STRING],
            'lat' =>["type"=>self::TYPE_FLOAT],
            'lng'=>["type"=>self::TYPE_FLOAT]
        ],
    ];
}