<?php

class PPLBatch extends ObjectModel
{
    public $id;

    public $name;

    public $remote_batch_id;

    public $created_at;

    public $lock;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = [
        'table' => 'ppl_batch',
        'primary' => 'id_ppl_batch',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => [
            'name' => ['type'=> self::TYPE_STRING],
            'remote_batch_id' => ['type'=> self::TYPE_STRING],
            'created_at' => ['type'=> self::TYPE_STRING],
            'lock' => ['type'=> self::TYPE_BOOL],
        ],
    ];

    public function __construct($id = null, $id_lang = null, $id_shop = null, $translator = null)
    {
        parent::__construct($id, $id_lang, $id_shop, $translator);
        if (!$this->created_at && !$this->id)
            $this->created_at = date('Y-m-d H:i:s');
    }

    public static function findBatches($free = false)
    {
        $prefix = _DB_PREFIX_;
        if ($free)
        {
            $rows = \DB::getInstance()->executeS("select * from {$prefix}ppl_batch where `lock` = 0 and id_ppl_batch  in (select id_batch_local from {$prefix}ppl_shipment)  order by id_ppl_batch desc");
        }
        else
        {

            $rows = \DB::getInstance()->executeS("select * from {$prefix}ppl_batch where id_ppl_batch in (select id_batch_local from {$prefix}ppl_shipment) and created_at > now() - INTERVAL 5 DAY  order by id_ppl_batch desc");

            if (count($rows) < 20) {
                $ids = array_map(function ($item){
                    return $item['id_ppl_batch'];
                }, $rows);
                foreach (\DB::getInstance()->executeS("select * from {$prefix}ppl_batch where id_ppl_batch  in (select id_batch_local from {$prefix}ppl_shipment)  order by id_ppl_batch desc limit 20") as $row) {
                    if (count($rows) < 20) {
                        if (!in_array($row['id_ppl_batch'], $ids, true))
                            $rows[] = $row;
                        continue;
                    }
                    break;
                }
            }
        }

        usort($rows, function ($a, $b) {
            return -($a["id_ppl_batch"] - $b["id_ppl_batch"]);
        });

        $output = [];

        foreach ($rows as $row) {

            $batch = new PPLBatch();
            $batch->hydrate($row);

            $output[] = $batch;
        }
        return $output;
    }

}