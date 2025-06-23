<?php
namespace PPLShipping\Validator;

use PPLShipping\Model\Model\NewCollectionModel;

class CollectionValidator extends ModelValidator
{

    public function canValidate($model)
    {
        return $model instanceof NewCollectionModel;
    }

    public function validate($model, $errors, $path)
    {

        /**
         * @var NewCollectionModel $model
         */
        if (!$model->isInitialized("sendDate") || !$model->getSendDate())
            $errors->add("$path.sendDate", "Datum svozu nesmí být prázdné");
        else {
            $datum = new \DateTime($model->getSendDate());
            $datum = new \DateTime($datum->format("Y-m-d"));

            $today = new \DateTime();
            $today = new \DateTime($today->format('Y-m-d'));
            $today9hour = (new \DateTime($today->format('Y-m-d')));
            $today9hour->setTime(9, 0, 0);
            if ($today > $datum
                || $today == $datum && new \DateTime() >= $today9hour)
            {
                $errors->add("$path.sendDate", "Svoz je příliš brzy");
            }
        }

        if ($model->isInitialized("estimatedShipmentCount") && $model->getEstimatedShipmentCount() > 100)
        {
            $errors->add("$path.estimatedShipmentCount", "Příliš mnoho zásilek pro svoz");
        } else if (!$model->isInitialized("estimatedShipmentCount") || $model->getEstimatedShipmentCount() <= 0)
        {
            $errors->add("$path.estimatedShipmentCount", "Příliš málo zásilek pro svoz");
        }

        foreach (["contact" => "Kontakt musí být vyplněn", "telephone" => "Telefon musí být vyplněn", "email" => "Email musí být vyplněn"] as $item => $message)
        {
            if (!$this->getValue($model, $item)) {
                $errors->add("$path.$item", $message);
            }
        }

    }
}