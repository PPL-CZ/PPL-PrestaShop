<?php
class PPLPackage extends ObjectModel
{
    public $id;

    public $id_ppl_shipment;

    public $reference_id;

    public $phase;

    public $phase_label;

    public $last_update_phase;

    public $last_test_phase;

    public $ignore_phase;

    public $shipment_number;

    public $weight;

    public $insurance;

    public $import_error;

    public $import_error_code;

    public $label_id;

    public $lock;

    public $status;

    public $status_label;



    public static $definition = [
        'table' => 'ppl_package',
        'primary' => 'id_ppl_package',
        'multilang' => false,
        'multilang_shop' => true,
        'fields' => [
            'id_ppl_shipment' => ['type' => self::TYPE_INT, 'allow_null' => true],
            'reference_id' => ['type'=> self::TYPE_STRING, 'allow_null' => true],
            'phase' => ['type'=> self::TYPE_STRING, 'allow_null' => true],
            'phase_label'=>['type'=> self::TYPE_STRING, 'allow_null' => true],
            'last_update_phase' => ['type' => self::TYPE_DATE, 'allow_null' => true],
            'last_test_phase' => ['type' => self::TYPE_DATE, 'allow_null' => true],
            'ignore_phase' => ['type' => self::TYPE_BOOL, 'allow_null' => true],
            'shipment_number' => ['type'=> self::TYPE_STRING, 'allow_null' => true],
            'weight'=> ['type'=> self::TYPE_FLOAT, 'allow_null' => true],
            'insurance'=> ['type'=> self::TYPE_FLOAT, 'allow_null' => true],
            'import_error'=> ['type'=>self::TYPE_STRING, 'allow_null' => true],
            'import_error_code'=> ['type'=> self::TYPE_STRING, 'allow_null' => true],
            'label_id'=>['type'=> self::TYPE_STRING, 'allow_null' => true],
            'lock'=> ['type'=> self::TYPE_BOOL],
            'status'=>['type'=>self::TYPE_INT, 'allow_null' => true],
            'status_label'=>['type'=>self::TYPE_STRING, 'allow_null' => true]
        ],
    ];

    public static function findPackagesByShipmentNumber($packages = [])
    {
        if (!$packages)
            return [];

        $prefix = _DB_PREFIX_;
        $output = [];
        $db = \Db::getInstance();
        $packages = join(", ", array_map(function ($item) use ($db)
        {
            return "'" . $db->escape($item) . "'";
        }, $packages));

        $query = "select * from {$prefix}ppl_package where shipment_number in (" . $packages . ")";
        $result = $db->query($query);
        while ($raworder =  $db->nextRow($result))
        {
            $package = new static();
            $package->hydrate($raworder);
            $output[] = $package;
        }
        return $output;
    }

    public static function checkStates(array $phases, \DateTime $from_last_test_phase, \DateTime $last_change_phase, int $limit)
    {
        $prefix = _DB_PREFIX_;
        $output = [];

        $last_change_phase = $last_change_phase->format('Y-m-d H:i:s');
        $from_last_test_phase = $from_last_test_phase->format('Y-m-d H:i:s');

        $sPhases = join(', ', array_map(function ($item) {
            return "'".\Db::getInstance()->escape($item) . "'";
        }, $phases));
        $query = <<<MULTILINE
select * from {$prefix}ppl_package where phase in ($sPhases) 
                                        and shipment_number is not null 
                                        and shipment_number <> ''
                                        and (last_update_phase >= '$last_change_phase' or last_update_phase is null) 
                                        and (last_test_phase < '$from_last_test_phase' or last_test_phase is null)
                                    order by last_test_phase asc
                                    limit $limit
MULTILINE;

        foreach (\Db::getInstance()->query($query) as $key => $item)
        {
            $package = new PPLPackage();
            $package->hydrate($item);
            $output[] = $package;
        }

        return $output;
    }
}