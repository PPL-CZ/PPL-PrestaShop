<?php
namespace PPLShipping\tmodule;

use PPLShipping\CPLOperation;
use PPLShipping\Model\Model\PackageModel;
use PPLShipping\Model\Model\ShipmentModel;

use PPLShipping\Symfony\Component\Yaml\Yaml;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\Routing\Router;

trait TAdminSetMedia
{

    public function hookActionAdminControllerSetMedia($params)
    {
        $assetGeneratedName = "-1.0.4";

        /**
         * @var \pplshipping $this
         */
        $ver = filemtime(__FILE__);
        if (!$ver)
            $ver = time();
        $this->context->controller->addJS($this->_path . 'muiadmin/static/js/bundle.js?' . $ver, false);
        $this->context->controller->addJs($this->_path . "assets/js/admin-order{$assetGeneratedName}.js?" . $ver, false);
        $this->context->controller->addJs($this->_path . "assets/js/admin-orders{$assetGeneratedName}.js?" . $ver, false);
        $this->context->controller->addCSS($this->_path . "assets/css/admin-order{$assetGeneratedName}.css?" . $ver, "all", null, false);
        $this->context->controller->addJS($this->_path . "assets/js/ppl-map{$assetGeneratedName}.js?" . $ver, false);
        $this->context->controller->addCSS($this->_path . "assets/css/ppl-map{$assetGeneratedName}.css?" . $ver, "all", null, false);
    }

    private function getTMediaSetMediaPathForGenerate($name)
    {
        static $router;
        if (!$router && $router !== false)
        {
            try {
                $controller = \Context::getContext()->controller;
                if (method_exists($controller, 'get'))
                    $router = $controller->get("router");
                else if (method_exists($controller, 'getContainer'))
                    $router = $controller->getContainer()->get("router");

            } catch (ServiceNotFoundException $ex)
            {
                $router = false;
            }
        }

        if ($router)
        {
            return $router->generate($name, [], Router::ABSOLUTE_URL);
        }

        static $routes;
        if (!$routes)
        {
            $routes = Yaml::parseFile(__DIR__ . '/../../config/routes.yml');
        }
        $link =  \Context::getContext()->link;

        if (isset($routes[$name])) {
            $path = $routes[$name]['path'];
        }
        return $link->getAdminLink("AdminConfigurationPPL", true, [] ,[
            "pplpath" => $path,
            "_token" => \Tools::getAdminToken("AdminConfigurationPPL")
        ]);
    }

    public function hookDisplayBackOfficeHeader($params)
    {

        /**
         * @var \pplshipping $this
         */

        $api = [];
        $api['setting'] = $this->getTMediaSetMediaPathForGenerate("pplshipping_setting_replacement");
        $api['codelist'] = $this->getTMediaSetMediaPathForGenerate("pplshipping_codelist_replacement");
        $api['shipment'] = $this->getTMediaSetMediaPathForGenerate("pplshipping_shipment_replacement");
        $api['shipmentBatch'] = $this->getTMediaSetMediaPathForGenerate("pplshipping_shipmentbatch_replacement");
        $api['order'] = $this->getTMediaSetMediaPathForGenerate("pplshipping_order_replacement");
        $api['collection'] = $this->getTMediaSetMediaPathForGenerate("pplshipping_collection_replacement");
        $api['log'] = $this->getTMediaSetMediaPathForGenerate("pplshipping_log_replacement");

        $url = $this->context->link->getModuleLink('pplshipping', 'FrontMapPPL');
        $secure_key = \Configuration::getGlobalValue("PPLShipmentCronKey");
        if (!$secure_key) {
            $bytes = openssl_random_pseudo_bytes(16);  // 16 bytů = 128 bitů
            $secure_key = bin2hex($bytes);
            \Configuration::updateGlobalValue("PPLShipmentCronKey", $secure_key);
        }
        $cronurl = $this->context->link->getModuleLink('pplshipping', 'CronPPL', ["secure_key"=>$secure_key]);

        \Media::addJsDef([
            "FrontMapPPLController"=> $url,
            "CronPPLController"=>$cronurl,
        ]);
        ob_start();
?>
<script type="text/javascript">
    window.pplPlugin = window.pplPlugin || [];
    window.pplPlugin.api = <?php echo json_encode($api) ?>;
</script>
<?php
    return ob_get_clean();
    }

}