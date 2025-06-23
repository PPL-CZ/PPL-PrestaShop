<?php
class pplshippingCronPPLModuleFrontController extends ModuleFrontController
{

    private function getPath()
    {
        return _PS_ROOT_DIR_ . '/var/pplshipping_cron_ppl.locker';
    }

    private function clearLogs()
    {
        $prefix = _DB_PREFIX_;
        \Db::getInstance()->execute("delete from {$prefix}pplcz_log where ppl_log_id not in (select ppl_log_id from (select ppl_log_id from {$prefix}pplcz_log order by ppl_log_id desc limit 100) as temp)");

    }
    
    private function phases()
    {
        $phases = pplcz_get_phases();
        $phases = array_map(function ($item) {
            return $item['code'];
        }, array_filter($phases, function ($item) {
            return $item['watch'];
        }));

        $from = (new \DateTime())->sub(new \DateInterval("PT120M"));
        $lastUpdate = (new \DateTime())->sub(new \DateInterval("P16D"));
        $max = pplcz_get_phase_max_sync();

        $packages = PPLPackage::checkStates(array_merge(["None"], $phases), $from, $lastUpdate, $max + 1);
        $count = count($packages);
        $next = $max < $count;

        $this->context->smarty->assign([
            "count" => $count,
            "next" => $next
        ]);

        while ($packages) {
            $operation = new \PPLShipping\CPLOperation();
            if ($operation->getAccessToken()) {
                $packages = array_values($packages);
                $update = array_slice($packages, 0, 40);
                if ($update) {
                    $operation->testPackageStates(array_map(function (PPLPackage $item) {
                        return $item->shipment_number;
                    }, $update));
                }
            }
        }

        return $next;
    }

    public function tryLock($counter = 0)
    {
        $path = $this->getPath();

        $open = @fopen($path, "c+");

        if ($open && !flock($open, LOCK_EX |LOCK_NB )) {
            if ($counter === 0)
            {
                $lastUpdate = filemtime($path);
                if ($lastUpdate + 600 > time())
                {
                    @unlink($this->getPath());
                    return $this->tryLock(1);
                }
            }
            return false;
        }

        return $open;
    }

    public function initContent()
    {
        $secure_key = \Configuration::getGlobalValue("PPLShipmentCronKey");
        if (!$secure_key || @$_GET['secure_key'] !== $secure_key)
        {
            http_response_code(403);
            die();
        }

        ignore_user_abort(true);
        parent::initContent();

        $file = $this->tryLock();
        $this->context->smarty->assign([
            "next" => 0,
            "count" => 0,
        ]);

        $this->setTemplate("module:pplshipping/views/templates/front/cron.tpl");


        if (!$file)
            return;
        try {
            $next = $this->phases();
        } finally {
            fwrite($file, mt_rand());
            fflush($file);
            fclose($file);
        }

        if ($next)
            $this->clearLogs();

        if ($next)
        {
            $url = $this->context->link->getModuleLink('pplshipping', 'CronPPL');
            if ($next) {
                $ctx = stream_context_create([
                    "http" => [
                        "timeout" => 5
                    ],
                    "ssl" => [
                        "verify_peer" => false,
                        "verify_peer_name" => false,
                    ]
                ]);
                file_get_contents($url, false, $ctx);
            }
        }
    }
}