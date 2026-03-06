<?php
namespace PPLShipping\Setting;

use PPLShipping\CPLOperation;
use Configuration;
use OrderState;
use PPLShipping\Model\Model\PrintSettingModel;

class PrintSetting {
    public static function getPrintSetting()
    {
        $printSetting = new PrintSettingModel();

        $format = Configuration::getGlobalValue("PPLPrintSetting", "1/PDF/A4/4");
        $format = (new CPLOperation())->getFormat($format);

        $printSetting->setFormat($format);

        return $printSetting;
    }

    public static function setFormat($content)
    {
        $printers = (new CPLOperation())->getAvailableLabelPrinters();

        foreach ($printers as $v) {
            if ($v->getCode() === $content) {
                Configuration::updateGlobalValue("PPLPrintSetting", $content);
                return true;
            }
        }
        return false;
    }

}
