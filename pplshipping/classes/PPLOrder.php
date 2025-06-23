<?php

class PPLOrder extends ObjectModel
{
    public $id;

    public $id_order;

    public $id_ppl_parcel;



    public static function getPPLOrderId($order_id)
    {
        $prefix = _DB_PREFIX_;
        $row = \Db::getInstance()->getRow("select id_ppl_order, id_order, id_ppl_parcel from {$prefix}ppl_order where id_order = " . (int)$order_id);
        $pplcart = null;
        if ($row) {
            $pplcart = new PPLOrder();
            $pplcart->hydrate($row);
        }
        return $pplcart;
    }

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'ppl_order',
        'primary' => 'id_ppl_order',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => [
            'id_order' => ['type' => self::TYPE_INT],
            'id_ppl_parcel' => ['type' => self::TYPE_INT],
        ],
    ];
}