<?php
namespace PPLShipping\tmodule;

use Doctrine\DBAL\Query\QueryBuilder;
use PPLShipping\Column\PPLShipmentColumn;
use PPLShipping\CPLOperation;
use PPLShipping\Errors;
use PPLShipping\Listener\ArgumentResolverListener;
use PPLShipping\Model\Model\ShipmentModel;
use PPLShipping\Validator;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\ButtonBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Column\ColumnCollection;
use PrestaShop\PrestaShop\Core\Grid\Filter\Filter;
use PrestaShop\PrestaShop\Core\Grid\Filter\HiddenFilter;
use PrestaShop\PrestaShop\Core\Search\Filters\OrderFilters;
use Doctrine\DBAL\Connection;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\HttpFoundation\Request;

trait TAdminOrder {

    public function hookActionAdminOrdersListingFieldsModifier($params)
    {

        if (isset($params['select']))
        {
            $prefix = _DB_PREFIX_;

            $smtp = \Db::getInstance()->query("select `name` from {$prefix}configuration where `name` like 'PPLCarrier%' ");
            $refs = [];
            foreach ($smtp as $item)
            {
                $refs[] = str_replace("PPLCarrier", "", $item['name']);
            }

            $id_carriers = [];

            if ($refs) {
                $smtp = \Db::getInstance()->query("select `id_carrier` from {$prefix}carrier where `id_reference` in  (" . join(",", $refs) . ")");
                foreach ($smtp as $item) {
                    $id_carriers[] = intval($item['id_carrier']);
                }
            }

            $id_carriers = join(', ', $id_carriers);

            $subquery = [
                "SELECT min(id_ppl_shipment)  as has_ppl, pplcz_ship1.id_order as id_order_ppl FROM {$prefix}ppl_shipment  pplcz_ship1 JOIN {$prefix}orders pplorder on pplorder.id_order = pplcz_ship1.id_order"
            ];

            if ($id_carriers)
            {
                $subquery[] = "SELECT 1, id_order as id_order_ppl FROM {$prefix}orders pplorder1 where pplorder1.id_carrier in ($id_carriers)";

            }

            $subquery = "LEFT JOIN (
                    select min(has_ppl) as has_ppl, id_order_ppl from (" . join (" UNION ", $subquery). ") pplcz2 group by id_order_ppl) pplcz ON pplcz.id_order_ppl = a.id_order ";
            $params['join'] .= "\n{$subquery}";
        }

        $params['fields']['has_ppl'] = [
            'title' => $this->l('PPL'),
            'align' => 'text-center',
            'type' => 'select',
            'filter_key' => 'pplcz!has_ppl',
            'callback' => 'renderPPLOrderTd',
            'callback_object' => $this,
            'list' => [
                1 => $this->l('Ano'),
            ]
        ];
    }

    public function renderPPLOrderTd ($params)
    {

        if ($params) {
            $shipment_or_order = \PPLShipment::findShipmentsByOrderID($params);
            if (!$shipment_or_order)
                $shipment_or_order = [new \Order($params)];
            /**
             * @var ShipmentModel $shipment
            */


            $shipments = array_map(function ($shipment) {
                return pplcz_denormalize($shipment, ShipmentModel::class);
            }, $shipment_or_order);

            if (array_filter($shipments, function ($item) {
                $error = pplcz_validate($item, "");
                return !!$error->errors ||  strpos("err", $item->getImportState()) !== false;
            }))
            {
                return '<span class="badge badge-warning">error</span>';
            } else if (array_filter($shipments, function($item ){
                return $item->getPackages();
            })) {

                $output = [];
                foreach ($shipments as $shipment) {
                    foreach ($shipment->getPackages() as $key => $v) {
                        $output[] = $v->getShipmentNumber();
                    }
                    $output = array_filter($output);
                }
                if (!$output)
                    return "Ano";
                return join(', ', $output);
            }
            return "Ano";
        }
        return "Ne";


    }

    private $orders = null;

    public function hookActionAdminOrdersListingResultsModifier($params)
    {

        if (isset($params['list']) && $params['list'])
        {
            $this->orders = [];

            foreach ($params['list'] as &$item)
            {
                $this->orders[] = $item['id_order'];
                if ($item['has_ppl']) {
                    $item['has_ppl'] = $item['id_order'];
                } else {
                    $item['has_ppl'] = null;
                }
            }
        }

    }


    public function hookActionOrderGridQueryBuilderModifier($params)
    {
        $filter = $params['search_query_builder'];
        $count = $params['count_query_builder'];
        $searchCriteria = $params['search_criteria'];

        /**
         * @var QueryBuilder $filter
         * @var QueryBuilder $count
         * @var OrderFilters $searchCriteria
         * @var \DbPDO $instance
         */


        if (@$searchCriteria->getFilters()['ppl_filter'] === "1")
        {
            $builder = new QueryBuilder($filter->getConnection());
            $prefix = _DB_PREFIX_;
            $builder = $builder->select("id_order id_shipment_order")->from("{$prefix}ppl_shipment")->groupBy("id_order");

            $queryParts = $filter->getQueryParts();

            $alias = $queryParts["from"][0]['alias'];

            $filter->leftJoin("$alias", "(" . $builder->getSQL() . ")", "pplship", "pplship.id_shipment_order = $alias.id_order");
            $count->leftJoin("$alias", "(" . $builder->getSQL() . ")", "pplship", "pplship.id_shipment_order = $alias.id_order");
            $smtp = \Db::getInstance()->query("select `name` from {$prefix}configuration where `name` like 'PPLCarrier%' ");
            foreach ($smtp as $item)
            {
                $refs[] = str_replace("PPLCarrier", "", $item['name']);
            }
            if ($refs) {
                $smtp = \Db::getInstance()->query("select `id_carrier` from {$prefix}carrier where `id_reference` in  (" . join(",", $refs) . ")");
                $id_carriers = [];
                foreach ($smtp as $item) {
                    $id_carriers[] = $item['id_carrier'];
                }
            }

            $query = ["pplship.id_shipment_order is not null"];
            if ($id_carriers) {
                $query[] = "$alias.id_carrier in (:id_carriers_ppl)";
                $count->setParameter("id_carriers_ppl", $id_carriers, Connection::PARAM_INT_ARRAY);
                $filter->setParameter("id_carriers_ppl", $id_carriers, Connection::PARAM_INT_ARRAY);
            }
            $filter->andWhere(join(" or ", $query));
            $count->andWhere(join(" or ", $query));

        }
    }

    public function hookDisplayAdminGridTableBefore($params)
    {
        if (isset($params['grid']['id']) && $params['grid']['id'] === 'order')
        {
            /**
             * @var \pplshipping $this
             */
            if (isset($params['grid']['filters']['ppl_filter']))
            {
                $this->smarty->assign([
                    "ppl_filter_state" =>@$params['grid']['filters']['ppl_filter'] === "1"
                ]);
            } else {
                $this->smarty->assign([
                    "ppl_filter_state" => false
                ]);
            }

            return $this->display($this->getReflFile(), "views/templates/admin/order_table_top.tpl");
        }
        return "";
    }


    public function hookActionOrderGridDefinitionModifier($params)
    {
        /**
         * @var \PrestaShop\PrestaShop\Core\Grid\Definition\GridDefinition $definition
         * @var ColumnCollection $columns
         * @var Request $request
         * @var \pplshipping $this
         */
        $definition = $params['definition'];
        $request = $params["request"];
        $filter = $definition->getFilters();

        /**
         * @var ArgumentResolverListener $arguments
         */
        $arguments = $this->get("PPLArguments");

        if ($arguments->orderFilters
            && @$arguments->orderFilters->getFilters()["ppl_filter"] === "1")
         {
            $columns = $definition->getColumns();
            $columns->remove("new");
            $filter->remove("new");
            $columns->remove("shop_name");
            $filter->remove('shop_name');
            $columns->remove("new");
            $filter->remove("new");
            $columns->remove("reference");
            $filter->remove('reference');
            $columns->addAfter("id_order", (new PPLShipmentColumn("pplshipment"))->setName("PPL"));

            $opts = (new ButtonBulkAction('ppl_order_print'))
                ->setName("Tisk etiket PPL")->setOptions([
                    'class' => 'ppl_print_labels',
                    'attributes' => [
                        'data-route' => 'admin_orders_view',
                        'data-route-param-name' => 'orderId',
                        'data-tabs-blocked-message' => $this->trans(
                            'It looks like you have exceeded the number of tabs allowed. Check your browser settings to open multiple tabs.',
                            [],
                            'Admin.Orderscustomers.Feature'
                        )
                    ]
                ]);

            $definition->getBulkActions()->add(
                $opts
            );

        }
        if (class_exists(HiddenFilter::class))
            $filterPPl = new HiddenFilter("ppl_filter" );
        else
            $filterPPl = new Filter("ppl_filter", HiddenType::class );

        $filterPPl->setAssociatedColumn("ppl_carrier");
        $filterPPl->setTypeOptions([
            "required"=> false,
        ]);
        $filterPPl->setAssociatedColumn("ppl_filter");
        $filter->add($filterPPl);

    }

    public function hookDisplayAdminOrderTabShip($params)
    {
        return "<li><a href='#pplshipping'>PPL</a></li>";
    }

    public function hookDisplayAdminOrderContentShip($params)
    {
        $content = $this->hookDisplayAdminOrderTabContent(['id_order' => $params['order']->id]);
        $content = "<div id=\"pplshipping\" class='tab-pane'>{$content}</div>";
        return $content;
    }

    public function hookDisplayAdminListAfter($params)
    {

        $controller = \Context::getContext()->controller ;

        if (isset($params['route']) && $params['route'] === 'admin_orders_index')
        {
            $shipments = [];
            $orders = [];

            foreach ($params['grid']['data']['records'] as $record)
            {
                $orders[$record['id_order']] = $record['id_order'];
            }

            if (!$orders)
                return "";

            $shipmentdata = \PPLShipment::findShipmentsByOrderID($orders);

            $shipments = array_merge($shipments, array_filter($shipmentdata, function (\PPLShipment $item) use (&$orders) {
                unset($orders[$item->id_order]);
                return in_array($item->import_state, ["None", "Error", ""], true);
            }));

            $shipments = array_merge($shipments, array_values(array_map(function($item) {
                return new \Order($item);
            }, $orders)));

            $shipments = array_filter(array_map(function($item) {
                $model = pplcz_denormalize($item, ShipmentModel::class);
                /**
                 * @var ShipmentModel $model
                 */
                if (!$model->getServiceCode())
                    return null;
                $error = pplcz_validate($model, "")->errors;
                if ($error)
                    return null;
                return [
                    "shipment" => pplcz_normalize($model),
                ];
            }, $shipments));

            ob_start();
            ?><script type="text/javascript">
            var pplShipments = <?php echo json_encode(array_values($shipments)); ?>
        </script>
            <?php
            return ob_get_clean();
        }
        else if ($controller instanceof \AdminOrdersControllerCore && $this->orders)
        {
            $shipments = [];
            $orders = [];

            foreach ($this->orders as $record)
            {
                $orders[$record] = $record;
            }

            if (!$orders)
                return "";

            $shipmentdata = \PPLShipment::findShipmentsByOrderID($orders);

            $shipments = array_merge($shipments, array_filter($shipmentdata, function (\PPLShipment $item) use (&$orders) {
                unset($orders[$item->id_order]);
                return in_array($item->import_state, ["None", "Error", ""], true);
            }));

            $shipments = array_merge($shipments, array_values(array_map(function($item) {
                return new \Order($item);
            }, $orders)));

            $shipments = array_filter(array_map(function($item) {
                $model = pplcz_denormalize($item, ShipmentModel::class);
                /**
                 * @var ShipmentModel $model
                 */
                if (!$model->getServiceCode())
                    return null;
                $error = pplcz_validate($model, "")->errors;
                if ($error)
                    return null;
                return [
                    "shipment" => pplcz_normalize($model),
                ];
            }, $shipments));
            ob_start();
            ?><script type="text/javascript">
            var pplShipments = <?php echo json_encode(array_values($shipments)); ?>;

            jQuery(".btn-group.bulk-actions.dropup").parent().append(jQuery("<div class='bulk-actions'><button class='btn btn-default ppl_print_labels'>Objednání PPL</button></div>"));

        </script>
            <?php
            return ob_get_clean();
        }

    }




    public function hookDisplayAdminOrderTabLink($params)
    {
        return '<li class="nav-item">
          <a class="nav-link" id="pplshippingTab" data-toggle="tab" href="#pplshippingTab" role="tab" aria-controls="hpplshippingTabContent" aria-expanded="true" aria-selected="false">
            <i class="material-icons">
              </i> PPL doprava 
          </a>
    </li>';
    }

    public function hookDisplayAdminOrderTabContent($params = []) {
        /**
         * @var \pplshipping $this
         */
        $order = $params['id_order'];

        $orders = \PPLShipment::findShipmentsByOrderID($order);
        $errors = [];
        $newShipment = null;
        if (!array_filter($orders, function(\PPLShipment $item){
            return $item->import_state !== "Complete";
        }))
        {
            // potřebuji iniciovat objednávku?
            $finded = \PPLOrder::getPPLOrderId($order);
            if ($finded)
            {
                $orders[] = new \Order($order);
            }
            else
            {
                $newShipment = pplcz_denormalize(new \Order($order), ShipmentModel::class);
            }
        }


        foreach ($orders as $key => $value)
        {

            $orders[$key] = pplcz_denormalize($value, ShipmentModel::class);
            $batchs[$key] = false;
            if ($orders[$key]->getBatchId())
            {
                $link =  \Context::getContext()->link;
                $printurl = $link->getAdminLink("AdminConfigurationPPL", true, [] ,[
                    "token" => \Tools::getAdminToken("AdminConfigurationPPL"),

                ]) . "#/batch/" . $orders[$key]->getBatchId();;
                $batchs[$key] = $printurl;
            }
            $tests = new Errors();
            Validator::getInstance()->validate($orders[$key], $tests, "");
            $errors[$key] = $tests->errors;
        }


        $availablePrinters = (new CPLOperation())->getAvailableLabelPrinters();
        $selectedPrint = \Configuration::getGlobalValue("PPLPrintSetting") ?: "1/PDF";
        $selectedPrint = array_filter($availablePrinters ,function($item) use($selectedPrint) {
            return ($item->getCode() === $selectedPrint);
        });

        $selectedPrint = reset($selectedPrint);

        $this->smarty->assign([
            "name" => $this->name,
            "orderId"=>  $params['id_order'],
            "shipmentsErrors" => $errors,
            "shipments" => $orders,
            "availablePrinters" => (new CPLOperation())->getAvailableLabelPrinters(),
            "selectedPrint" => $selectedPrint,
            "newShipment" => $newShipment,
            "batchs" => $batchs

        ]);

        return $this->display($this->getReflFile(), "views/templates/admin/ordershipping.tpl");
    }
}