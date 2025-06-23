<?php

class PPLShipment extends ObjectModel {

    public $id;

    public $id_order;

    public $import_errors;

    public $import_state;

    public $service_code;

    public $service_name;

    public $reference_id;

    public $id_recipient_address;

    public $id_sender_address;

    public $cod_value;

    public $cod_value_currency;

    public $cod_variable_number;

    public $has_parcel;

    public $id_parcel;

    public $batch_id;

    public $batch_label_group;

    public $note;

    public $age;

    public $package_ids;

    public $lock;

    public $print_state;

    public static $definition = [
        'table' => 'ppl_shipment',
        'primary' => 'id_ppl_shipment',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => [
            'id_order' => ['type'=>self::TYPE_INT, 'allow_null' => true],
            'import_errors' => ['type'=> self::TYPE_STRING, 'allow_null' => true],
            'import_state'=>['type'=>self::TYPE_STRING, 'allow_null' => true],
            'reference_id'=>['type'=>self::TYPE_STRING, 'allow_null' => true],
            'id_recipient_address' =>[ 'type'=>self::TYPE_INT, 'allow_null' => true],
            'service_code'=>['type'=>self::TYPE_STRING, 'allow_null' => true],
            'service_name'=>['type'=>self::TYPE_STRING, 'allow_null' => true],
            'batch_label_group'=>['type'=>self::TYPE_STRING, 'allow_null' => true],
            'id_sender_address' =>['type'=>self::TYPE_INT, 'allow_null' => true],
            'cod_value'=> ['type'=>self::TYPE_FLOAT, 'allow_null' => true],
            'cod_value_currency'=>['type'=>self::TYPE_STRING, 'allow_null' => true],
            'cod_variable_number'=>['type'=>self::TYPE_STRING, 'allow_null' => true],
            'has_parcel'=> ['type'=>self::TYPE_BOOL, 'allow_null' => true],
            'id_parcel'=>['type'=>self::TYPE_INT, 'allow_null' => true],
            'batch_id' =>['type'=>self::TYPE_STRING, 'allow_null' => true],
            'note' => ['type'=>self::TYPE_STRING, 'allow_null' => true],
            'age'=>['type'=>self::TYPE_STRING, 'allow_null' => true],
            'lock'=>['type'=>self::TYPE_BOOL],
            'package_ids'=>['type'=>self::TYPE_STRING, 'allow_null' => true],
            'print_state' => ['type'=>self::TYPE_STRING, 'allow_null' => true]
        ],
    ];

    public function deleteTree()
    {
        $address = new PPLAddress($this->id_recipient_address);
        if ($address->id)
        {
            $address->delete();
        }

        foreach ($this->get_package_ids() as $id)
        {
            $package = new PPLPackage($id);
            if ($package->id){
                $package->delete();
            }
        }
        $this->delete();
    }

    public function lock()
    {
        $this->lock = true;
        $this->save();

        $address = new PPLAddress($this->id_sender_address);
        if ($address->id) {
            $address->lock = true;
            $address->save();
        }

        $address = new PPLAddress($this->id_recipient_address);
        if ($address->id)
        {
            $address->lock= true;
            $address->save();
        }

        foreach ($this->get_package_ids() as $id)
        {
            $package = new PPLPackage($id);
            $package->id_order = $this->id;
            $package->lock = true;
            $package->save();
        }

    }

    public function unlock()
    {
        $address = new PPLAddress($this->id_recipient_address);
        if ($address->id)
        {
            $address->lock= false;
            $address->save();
        }

        foreach ($this->get_package_ids() as $id)
        {
            $package = new PPLPackage($id);
            $package->lock = false;
            $package->save();
        }
        $this->lock = false;
        $this->save();
    }


    public function get_package_ids()
    {
        $ids = $this->package_ids;
        if (!trim($ids))
        {
            return [];
        }
        return array_filter(array_map("trim", explode(',', $ids)),"ctype_digit");
    }

    public function set_package_ids($value)
    {
        $this->package_ids = join(",", array_filter(array_map("trim", $value), "ctype_digit"));
    }

    /**
     * @param $batch_id
     * @return \PPLShipment[]
     * @throws PrestaShopDatabaseException
     */
    public static function findBatchShipments($batch_id)
    {
        $prefix = _DB_PREFIX_;
        $output = [];
        $db = \Db::getInstance();
        $query = "select * from {$prefix}ppl_shipment where batch_id = '" . $db->escape($batch_id) . "'";
        $result = $db->query($query);
        while ($raworder =  $db->nextRow($result))
        {
            $order = new static();
            $order->hydrate($raworder);
            $output[] = $order;
        }
        return $output;
    }

    public static function findShipmentsByOrderID($order_id)
    {
        $prefix = _DB_PREFIX_;
        $output = [];
        if (!is_array($order_id))
        {
            $order_id = [$order_id];
        }
        $ids = join(",", array_map(function ($item) {
            return (int)$item;
        },$order_id));

        $query = "select * from {$prefix}ppl_shipment where id_order in ($ids)";
        $db = \Db::getInstance();
        $result = $db->query($query);
        while ($raworder = $db->nextRow($result))
        {
            $order = new static();
            $order->hydrate($raworder);
            $output[] = $order;
        }
        return $output;
    }

    public static function findProgressShipment() {
        $prefix = _DB_PREFIX_;
        $output = [];

        foreach (Db::getInstance()->query("select * from {$prefix}ppl_shipment where batch_id is not null and import_state = 'InProgress'") as $progress)
        {
            $shipment = new PPLShipment();
            $shipment->hydrate($progress);
            $output[] = $shipment;
        }

        return $output;
    }

    public static function set_print_state($id, $print_state)
    {
        $id = intval($id);
        $db = \Db::getInstance();
        $db->update("ppl_shipment", [
            "print_state" => $print_state
        ], "id_ppl_shipment = $id");
    }

};