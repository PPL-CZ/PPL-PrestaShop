<?php
class PPLCollection extends ObjectModel {
    public $id;

    public $remote_collection_id;

    public $created_date;

    public $send_date;

    public $send_to_api_date;

    public $reference_id;

    public $state;

    public $shipment_count;

    public $estimated_shipment_count;

    public $contact;

    public $email;

    public $telephone;

    public $note;

    public static $definition = [
        'table' => 'ppl_collection',
        'primary' => 'id_ppl_collection',
        'multilang' => false,
        'multilang_shop' => true,
        'fields' => [
            'created_date' => ['type' => self::TYPE_DATE, 'allow_null' => true],
            'send_date' => ['type'=> self::TYPE_DATE, 'allow_null' => true],
            'send_to_api_date' => ['type'=> self::TYPE_DATE, 'allow_null' => true],
            'reference_id'=> ['type'=> self::TYPE_STRING, 'allow_null' => true],
            'state'=> ['type'=>self::TYPE_STRING, 'allow_null' => false],
            'shipment_count'=>['type'=>self::TYPE_INT, 'allow_null' => true],
            'estimated_shipment_count'=>['type'=>self::TYPE_INT, 'allow_null' => true],
            'contact' => ['type'=> self::TYPE_STRING, 'allow_null' => true],
            'email' => ['type'=> self::TYPE_STRING, 'allow_null' => true],
            'telephone' => ['type'=> self::TYPE_STRING, 'allow_null' => true],
            'note'=> ['type'=>self::TYPE_STRING, 'allow_null' => true],

        ],
    ];

    public static function GetLastCollection()
    {
        $prefix = _DB_PREFIX_;

        foreach (\Db::getInstance()->query("select * from {$prefix}ppl_collection order by send_date desc limit 1") as $arr)
        {
            $collection = new PPLCollection();
            $collection->hydrate($arr);
            return $collection;
        }
        return null;
    }

    public static function GetCollections()
    {
        $prefix = _DB_PREFIX_;
        $output = [];

        foreach (\Db::getInstance()->query("select * from {$prefix}ppl_collection order by send_date desc") as $arr)
        {
            $collection = new PPLCollection();
            $collection->hydrate($arr);
            $output[] = $collection;
        }

        return $output;

    }
}