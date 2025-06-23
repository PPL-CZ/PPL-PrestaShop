<?php

class PPLCart extends ObjectModel
{
    public $id;

    public $id_cart;

    public $id_order;

    public $id_ppl_parcel;



    public static function getParcelByCartId($cart_id)
    {
        $prefix = _DB_PREFIX_;
        $row = \Db::getInstance()->getRow("select id_ppl_cart, id_cart, id_ppl_parcel from {$prefix}ppl_cart where id_cart = " . (int)$cart_id);
        $pplcart = null;
        if ($row) {
            $pplcart = new PPLCart();
            $pplcart->hydrate($row);
        }
        return $pplcart;
    }

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'ppl_cart',
        'primary' => 'id_ppl_cart',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => [
            'id_cart' => ['type' => self::TYPE_INT],
            'id_ppl_parcel' => ['type' => self::TYPE_INT],
        ],
    ];
}