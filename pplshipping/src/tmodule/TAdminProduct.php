<?php
namespace PPLShipping\tmodule;

use PPLShipping\Model\Model\CategoryRulesModel;
use PPLShipping\Model\Model\ProductRulesModel;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

trait TAdminProduct {


    public function hookActionProductUpdate($params)
    {
        $modul = pplcz_denormalize($_POST, ProductRulesModel::class);

        $table = \PPLBaseDisabledRule::getByProduct($params['id_product']);
        if ($table == null)
            $table = new \PPLBaseDisabledRule();
        $table->id_product = $params['id_product'];
        $table = pplcz_denormalize($modul, \PPLBaseDisabledRule::class, ["data" => $table]);
        $table->save();
        return;
    }

    public function hookActionCategoryUpdate($params)
    {
        $modul = pplcz_denormalize($_POST, CategoryRulesModel::class);

        $table = \PPLBaseDisabledRule::getByCagetory($params['category']->id);
        if ($table == null)
            $table = new \PPLBaseDisabledRule();
        $table->id_category = $params['category']->id;
        $table = pplcz_denormalize($modul, \PPLBaseDisabledRule::class, ["data" => $table]);
        $table->save();
        return;
    }

    public function hookActionAfterUpdateProductFormHandler($params)
    {

        $obj = \PPLBaseDisabledRule::getByProduct($params['id']);
        /**
         * @var \PPLBaseDisabledRule $table
         */
        $table = pplcz_denormalize(pplcz_denormalize($params['form_data']['shipping']['pplcz'], ProductRulesModel::class), \PPLBaseDisabledRule::class, ['data' => $obj]);
        $table->id_product = $params['id'];
        $table->save();
        return;
    }

    public function hookActionAfterUpdateCategoryFormHandler($params)
    {
        $obj = \PPLBaseDisabledRule::getByCagetory($params['id']);
        /**
         * @var \PPLBaseDisabledRule $table
         */
        $table = pplcz_denormalize(pplcz_denormalize($params['form_data']['pplcz'], CategoryRulesModel::class), \PPLBaseDisabledRule::class, ['data' => $obj]);
        $table->id_category = $params['id'];
        $table->save();
        return;
    }


    public function hookActionAdminCategoriesFormModifier($params)
    {
        $id = $params['object']->id;
        $obj = \PPLBaseDisabledRule::getByCagetory($id);
        if ($obj === null) {
            $obj = new \PPLBaseDisabledRule();
            $obj->id_category = $id;
        }

        $model = pplcz_normalize(pplcz_denormalize($obj, CategoryRulesModel::class));
       // $fields = $params['fields'][0]['form']['input'];

        $newform = [
            "form" => [
                "legend" => [
                    "title" => "PPL Omezení"
                ],
                "input" => [],
                "submit" => $params['fields'][0]['form']['submit']
            ]
        ];



        foreach ($this->getTAdminProductRules($model) as $item)
        {
            if ($item['type'] === "choices")
            {
                $params['fields_value'][$item['name'] . "[]"] = $item['value'];
                $newform['form']['input'][] = [
                    "type" => "select",
                    "label" => $item['label'],
                    "name" => $item['name'],
                    "values" => $item['value'],
                    "multiple" => true,
                    "options" =>[ "query" =>  array_map(function ($value, $key) {
                        return [
                          "id" =>  $key, "name" => $value
                        ];
                    }, $item['choices'], array_keys($item['choices'])), "id" => "id", "name" =>"name"]
                ];
            }
            else if ($item['type'] === 'checkbox') {
                $params['fields_value'][$item['name']] = $item['value'];
                $newform['form']['input'][] = [
                    "type" => "switch",
                    "label" => $item['label'],
                    "name" => $item['name'],
                    "required" => false,
                    "is_bool" => true,
                    "values" => [
                        [
                            "value" => 1,
                            "label" => "Ano"
                        ],
                        [
                            "value" => 0,
                            "label" => "Ne"
                        ]
                    ],
                ];
            }
        }
        $params['fields'][] = $newform;
    }

    public function hookActionCategoryFormBuilderModifier($params)
    {
        if (!isset($params['id']) || !(int)$params['id'])
            return;

        $obj = \PPLBaseDisabledRule::getByCagetory($params['id']);
        if ($obj == null)
            $obj = new \PPLBaseDisabledRule();
        $obj->id_product = $params['id'];
        $model = pplcz_normalize(pplcz_denormalize($obj, CategoryRulesModel::class));
        $this->createFormBuildertAdminProduct($model, $params['form_builder'], false);
    }

    public function hookDisplayAdminProductsShippingStepBottom($params)
    {
        if (!isset($params['id_product']) || !(int)$params['id_product'])
            return;

        /**
         * @var \pplshipping $this
         */
        $obj = \PPLBaseDisabledRule::getByProduct($params['id_product']);
        if ($obj == null)
            $obj = new \PPLBaseDisabledRule();
        $obj->id_product = $params['id_product'];
        $model = pplcz_denormalize($obj, CategoryRulesModel::class);
        $model = pplcz_normalize($model);
        $rules = $this->getTAdminProductRules($model);

        $this->smarty->assign([
            "pplform" => $rules
        ]);
        $result = $this->display($this->getReflFile(), "views/templates/admin/rules.tpl");
        return $result;
    }

    public function hookActionProductFormBuilderModifier($params)
    {
        if (!isset($params['id']) || !(int)$params['id'])
            return;

        $obj = \PPLBaseDisabledRule::getByProduct($params['id']);
        if ($obj == null)
            $obj = new \PPLBaseDisabledRule();
        $obj->id_product = $params['id'];
        $model = pplcz_normalize(pplcz_denormalize($obj, ProductRulesModel::class));
        $this->createFormBuildertAdminProduct($model, $params['form_builder'], true);
    }


    private function getTAdminProductRules($model)
    {
        $values = array_map(function ($key, $item) {
            $value = [
                "value" => $item,
                "name" => $key,
                "type" => "checkbox"
            ];

            switch ($key)
            {
                case "pplConfirmAge15":
                    $value['label'] = "Kontrola věku (15let)";
                    $value['position'] = 1;
                    break;
                case 'pplConfirmAge18':
                    $value['label'] = "Kontrola věku (18let)";
                    $value['position'] = 2;
                    break;
                case 'pplDisabledParcelBox':
                    $value['label'] = "Omezení ParcelBoxů";
                    $value['position'] = 3;
                    break;
                case 'pplDisabledParcelShop':
                    $value['label'] = "Omezení ParcelShopů";
                    $value['position'] = 4;
                    break;
                case 'pplDisabledAlzaBox':
                    $value['label'] = "Omezení AlzaBoxů";
                    $value['position'] = 5;
                    break;
                case 'pplDisabledTransport':
                    $value['label'] = "Zakázaná doprava";
                    $value['position'] = 6;
                    $value['type'] = 'choices';
                    $value['choices'] = pplcz_get_all_services();
                    break;
            }
            return $value;
        }, array_keys($model), array_values($model));

        usort($values, function ($a, $b){
            return $a['position'] - $b['position'];
        });
        return $values;
    }



    private function createFormBuildertAdminProduct($model, FormBuilderInterface $formBuilder, bool $asProduct) {
        $data = $formBuilder->getData();
        if ($asProduct) {
            $data['shipping']['pplcz'] = $model;
            $formBuilder->setData($data);
            $shipping = $formBuilder->get("shipping");
            $shipping->add('pplcz', FormType::class, [
                'label'    => $this->l('PPL omezení'),
                'required' => false,
            ]);
            $info = $shipping->get('pplcz');
        }
        else {
            $data['pplcz'] = $model;
            $formBuilder->setData($data);
            $formBuilder->add('pplcz', FormType::class, [
                'label'    => $this->l('PPL omezení'),
                'required' => false,
            ]);
            $info = $formBuilder->get('pplcz');
        }

        foreach ($this->getTAdminProductRules($model) as $value)
        {
            switch ($value['type'])
            {
                case 'checkbox':
                    $info->add($value['name'], CheckboxType::class, [
                        "label" => $this->l($value['label']),
                        'required' => false,
                    ]);
                    break;
                case 'choices':
                    $choices = [];
                    foreach ($value['choices'] as $choiceKey => $choiceText)
                    {
                        $choices[] = [
                            $choiceText => $choiceKey
                        ];
                    }

                    $info->add("pplDisabledTransport", ChoiceType::class, [
                        'label' => $this->l("Zakázaná doprava"),
                        'required' => false,
                        "choices" =>  $choices,
                        "multiple" => true,
                        "group_by" => function() {
                            return "Možnosti";
                        }
                    ]);
                    break;
            }
        }

    }

}