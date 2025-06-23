<?php
namespace PPLShipping\Validator;

use PPLShipping\Model\Model\ShipmentModel;
use PPLShipping\Model\Model\UpdateShipmentModel;
use PPLShipping\Validator;

class ShipmentValidator extends ModelValidator
{

    public function canValidate($model)
    {
        return $model instanceof ShipmentModel
            || $model instanceof UpdateShipmentModel;
    }



    public function validate($model, $errors, $path)
    {
        if ($model instanceof UpdateShipmentModel) {
            foreach (["referenceId" => "Reference pro objednávku zásilky nesmí zůstat prázdná", "serviceCode" => "Není vybraná služba"] as $item => $message ) {
                if (!$this->getValue($model, $item)) {
                    $errors->add("$path.{$item}", $message);
                }
            }


            if (!$model->getPackages())
            {
                $errors->add("$path.packages", "Přidejte aspoň jednu zásilku");
            }

            foreach ($model->getPackages() as $key=>$package) {
                Validator::getInstance()->validate($package, $errors, "{$path}.packages.{$key}");
            }


        }


        if ($model instanceof ShipmentModel) {
            /**
             * @var ShipmentModel $model
             */
            foreach (["referenceId" => "Je nutné vyplnit referenci zásilky", "serviceCode" => "Je nutné vybrat službu", "sender" => "Je nutné určit odesílatele pro etiketu", "recipient" => "Není určen příjemce zásilky"] as $item => $message) {
                if (!$this->getValue($model, $item)) {
                    $errors->add("$path.{$item}", $message);
                }
                else if ($item === "sender" || $item === "recipient") {
                    Validator::getInstance()->validate($this->getValue($model, $item), $errors, "$path.$item");
                }
            }

            $code = $this->getValue($model, 'serviceCode');
            if ($code) {
                if (in_array($code, ["SMEU", "CONN", "SMED", "COND"])
                    && count($model->getPackages()) > 1) {
                    $errors->add("$path.packages", "Počet balíčku může být pouze 1");
                }
            }
        }
    }
}