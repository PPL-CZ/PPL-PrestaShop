<?php

namespace PPLShipping\Validator;


use PPLShipping\Model\Model\PackageModel;

class PackageValidator extends ModelValidator {
    public function canValidate($model)
    {
        return $model instanceof PackageModel;
    }

    public function validate($model, $errors, $path)
    {
        /**
         * @var PackageModel $model
         */
        if ($model->getWeight()) {
            if ($model->getWeight() <= 0)
            {
                $errors->add("$path.weight", 'Váha u zásilky musí být kladné číslo');
            }
        }
    }
}
