<?php
namespace PPLShipping\Setting;

use PPLShipping\Model\Model\OrderStateModel;
use PPLShipping\Model\Model\ShipmentPhaseModel;
use PPLShipping\Model\Model\SyncPhasesModel;
use OrderState;
use Configuration;

class PhaseSetting
{
    public static function setPhase($key, $watch, $orderState)
    {
        if ($watch || $orderState) {
            Configuration::updateGlobalValue("PPLWatchPhase{$key}States", !!$watch);
            Configuration::updateGlobalValue("PPLWatchPhase{$key}OrderState", $orderState);
        }
        else {
            Configuration::deleteByName("PPLWatchPhase{$key}States");
            Configuration::deleteByName("PPLWatchPhase{$key}OrderState");
        }
    }

    public static function setMaxSync($value)
    {
        Configuration::updateGlobalValue("PPLWatchPhaseMaxSync", intval($value) ?: 200);
    }


    public static function getPhases()
    {
        $phases = include __DIR__ . '/../config/shipment_phases.php';
        unset($phases['Deleted']);

        $phases = array_map(function ($item, $key) {
            $output = new ShipmentPhaseModel();
            $output->setCode($key);
            $output->setTitle($item);
            $output->setWatch(!!Configuration::getGlobalValue("PPLWatchPhase{$key}States"));

            $orderState = Configuration::getGlobalValue("PPLWatchPhase{$key}OrderState");
            $output->setOrderState($orderState);

            return $output;
        }, $phases, array_keys($phases));

        $maxSync = Configuration::getGlobalValue("PPLWatchPhaseMaxSync");
        $maxSync = intval($maxSync) ?: 200;

        $sync = new SyncPhasesModel();

        $sync->setPhases($phases);
        $sync->setMaxSync($maxSync);

        return $sync;
    }
}