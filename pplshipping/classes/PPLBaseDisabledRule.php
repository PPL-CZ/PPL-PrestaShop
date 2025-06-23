<?php
class PPLBaseDisabledRule extends ObjectModel
{
    public $id;

    public $id_product;

    public $id_category;

    public $required_age15;

    public $required_age18;

    public $disabled_parcelbox;

    public $disabled_parcelshop;

    public $disabled_alzabox;

    public $disabled_methods;

    public static $definition = [
        'table' => 'ppl_base_disabled_rule',
        'primary' => 'id_base_disabled_rule',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => [
            'id_product'=> ['type'=> self::TYPE_INT, 'allow_null'=>true],
            'id_category'=> ['type'=> self::TYPE_INT, 'allow_null'=>true],
            'required_age18' => ['type' => self::TYPE_BOOL, 'allow_null'=>true],
            'required_age15' => ['type' => self::TYPE_BOOL, 'allow_null'=>true],
            'disabled_parcelbox' => ['type' => self::TYPE_BOOL, 'allow_null'=>true],
            'disabled_parcelshop' => ['type' => self::TYPE_BOOL, 'allow_null'=>true],
            'disabled_alzabox' => ['type' => self::TYPE_BOOL, 'allow_null'=>true],
            'disabled_methods' => ['type' => self::TYPE_STRING, 'allow_null'=>true],
        ],
    ];


    public static function getByProduct($id_product)
    {
        $prefix = _DB_PREFIX_;
        $row = \Db::getInstance()->getRow("select * from {$prefix}ppl_base_disabled_rule where id_product = " . (int)$id_product);

        if ($row) {
            $obj = new PPLBaseDisabledRule();
            $obj->hydrate($row);
            return $obj;
        }
        return null;
    }

    public static function getByCagetory($id_category)
    {
        $prefix = _DB_PREFIX_;
        $row = \Db::getInstance()->getRow("select * from {$prefix}ppl_base_disabled_rule where id_category = " . (int)$id_category);

        if ($row) {
            $obj = new PPLBaseDisabledRule();
            $obj->hydrate($row);
            return $obj;
        }
        return null;
    }

}