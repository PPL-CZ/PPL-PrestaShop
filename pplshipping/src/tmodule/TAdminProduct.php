<?php
namespace PPLShipping\tmodule;

use PPLShipping\Model\Model\CategoryRulesModel;
use PPLShipping\Model\Model\ProductRulesModel;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

trait TAdminProduct {


    public function hookActionProductUpdate($params)
    {
        $postData = $_POST;
        if (isset($postData['pplSizes']) && is_string($postData['pplSizes'])) {
            $decoded = json_decode($postData['pplSizes'], true);
            $postData['pplSizes'] = is_array($decoded) ? $decoded : [];
        }

        $modul = pplcz_denormalize($postData, ProductRulesModel::class);

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
        $postData = $_POST;

        if (isset($postData['pplSize']) && is_string($postData['pplSize'])) {
            $decoded = json_decode($postData['pplSize'], true);
            if (is_array($decoded)) {
                $postData['pplSize'] = $decoded;
            } else {
                unset($postData['pplSize']);
            }
        }

        $modul = pplcz_denormalize($postData, CategoryRulesModel::class);

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
        $formData = $params['form_data']['shipping']['pplcz'];

        if (isset($formData['pplSizes']) && is_string($formData['pplSizes'])) {
            $decoded = json_decode($formData['pplSizes'], true);
            $formData['pplSizes'] = is_array($decoded) ? $decoded : [];
        }

        /**
         * @var \PPLBaseDisabledRule $table
         */
        $table = pplcz_denormalize(pplcz_denormalize($formData, ProductRulesModel::class), \PPLBaseDisabledRule::class, ['data' => $obj]);
        $table->id_product = $params['id'];
        $table->save();
        return;
    }

    public function hookActionAfterUpdateCategoryFormHandler($params)
    {
        $obj = \PPLBaseDisabledRule::getByCagetory($params['id']);
        $formData = $params['form_data']['pplcz'];

        if (isset($formData['pplSize']) && is_string($formData['pplSize'])) {
            $decoded = json_decode($formData['pplSize'], true);
            if (is_array($decoded)) {
                $formData['pplSize'] = $decoded;
            } else {
                unset($formData['pplSize']);
            }
        }

        /**
         * @var \PPLBaseDisabledRule $table
         */
        $table = pplcz_denormalize(pplcz_denormalize($formData, CategoryRulesModel::class), \PPLBaseDisabledRule::class, ['data' => $obj]);
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
        if (!isset($model['pplSize'])) {
            $model['pplSize'] = null;
        }
        if (isset($model['pplSize']) && is_object($model['pplSize'])) {
            $model['pplSize'] = pplcz_normalize($model['pplSize']);
        }

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
            else if ($item['type'] === 'size_category') {
                $params['fields_value'][$item['name']] = json_encode($item['value']);
                $newform['form']['input'][] = [
                    "type" => "html",
                    "name" => $item['name'],
                    "html_content" => '<input type="hidden" class="ppl-size-category-data" name="' . $item['name'] . '" value="' . htmlspecialchars(json_encode($item['value']), ENT_QUOTES) . '" data-label="' . htmlspecialchars($item['label'], ENT_QUOTES) . '" data-description="' . htmlspecialchars($item['description'] ?? '', ENT_QUOTES) . '">'
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
        $obj->id_category = $params['id'];
        $model = pplcz_normalize(pplcz_denormalize($obj, CategoryRulesModel::class));

        if (!isset($model['pplSize'])) {
            $model['pplSize'] = null;
        }

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
        $model = pplcz_denormalize($obj, ProductRulesModel::class); // FIX: CategoryRulesModel → ProductRulesModel
        $model = pplcz_normalize($model);

        if (!isset($model['pplSizes'])) {
            $model['pplSizes'] = [];
        }

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

        if (!isset($model['pplSizes'])) {
            $model['pplSizes'] = [];
        }

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
                case 'pplSizes':
                    $value['label'] = "Produkt lze případně rozdělit na menší balíčky";
                    $value['position'] = 7;
                    $value['type'] = 'sizes';
                    break;
                case 'pplSize':
                    $value['label'] = "Velikost balíku";
                    $value['description'] = "Určuje, s jakou velikostí pracovat pro zjištění, zda je možná doprava (rozměry v cm)";
                    $value['position'] = 7;
                    $value['type'] = 'size_category';
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
                case 'sizes':
                    // Hidden field s JSON daty + JavaScript vytváří UI dynamicky
                    $currentSizes = $value['value'] ?? [];

                    $info->add('pplSizes', HiddenType::class, [
                        'label' => $this->l($value['label']),
                        'required' => false,
                        'data' => json_encode($currentSizes),
                        'attr' => [
                            'class' => 'ppl-sizes-data',
                            'data-label' => $this->l($value['label'])
                        ]
                    ]);
                    break;
                case 'size_category':
                    // Hidden field s JSON object (ne array!) + JavaScript vytváří UI pro single size
                    $currentSize = $value['value'] ?? null;

                    $info->add('pplSize', HiddenType::class, [
                        'label' => $this->l($value['label']),
                        'required' => false,
                        'data' => json_encode($currentSize),
                        'attr' => [
                            'class' => 'ppl-size-category-data',
                            'data-label' => $this->l($value['label']),
                            'data-description' => $this->l($value['description'])
                        ]
                    ]);
                    break;
            }
        }

    }

}