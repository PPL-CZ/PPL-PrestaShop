<?php
class PPLLog extends ObjectModel {
    public $id;

    public $timestamp;

    public $message;

    public $errorhash;

    public static $definition = [
        'table' => 'ppl_log',
        'primary' => 'id_ppl_log',
        'multilang' => false,
        'multilang_shop' => false,
        'fields' => [
            'id_ppl_log' => ['type' => self::TYPE_INT, 'allow_null' => true],
            'timestamp' => ['type' => self::TYPE_DATE, 'allow_null' => false],
            'message'=> ['type'=> self::TYPE_STRING, 'allow_null' => false],
            'errorhash'=> ['type'=>self::TYPE_STRING, 'allow_null' => false]
        ],
    ];

    /**
     * @return PPLLog[]
     * @throws PrestaShopDatabaseException
     */
    public static function GetLogs()
    {
        $prefix = _DB_PREFIX_;
        $logs = [];
        foreach (\Db::getInstance()->query("select * from {$prefix}ppl_log order by id_ppl_log desc limit 30") as $arr)
        {
            $collection = new PPLLog();
            $collection->hydrate($arr);
            $logs[] = $collection;
        }
        return $logs;
    }

    public static function deleteLogs()
    {
        $prefix = _DB_PREFIX_;
        \Db::getInstance()->query("delete from {$prefix}ppl_log where id_ppl_log not in (select id_ppl_log from (select id_ppl_log from {$prefix}ppl_log order by id_ppl_log desc limit 100) as temp)");
    }
}