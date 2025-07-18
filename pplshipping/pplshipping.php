<?php

use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\ButtonBulkAction;
use PrestaShop\PrestaShop\Core\Grid\Action\Bulk\Type\ModalFormSubmitBulkAction;

if (!defined('_PS_VERSION_')) {
    exit();
}

require_once __DIR__ . '/build/vendor/autoload.php';
require_once __DIR__ . '/src/globals.php';
require_once  __DIR__ . '/src/smarty.php';
require_once __DIR__ . '/src/Error/handler.php';

class pplshipping extends CarrierModule {

    public function __construct($name = null, Context $context = null)
    {
        $this->name = 'pplshipping';
        $this->tab = 'shipping_logistics';
        $this->version = '1.0.0';
        $this->author = 'PPL';
        $this->need_instance = 1;
        parent::__construct();
        $this->displayName = $this->l('PPL');
        $this->description = $this->l('PPL doprava');

        if ($this->context && $this->context->smarty)
            $this->tmediaSetMediaHookActionDispatcherBefore([]);

    }

    private function installTab()
    {
        $tab = new Tab();
        $tab->class_name = 'AdminConfigurationPPL';
        $tab->module = $this->name;
        $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentShipping'); // Zde specifikujeme, že to patří do sekce "Doprava"
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'PPL doprava';
        }
        $tab->active = 1;
        $tab->icon = 'icon-truck'; // Ikona pro záložku
        return $tab->add();
    }

    public function getReflFile()
    {
        return __FILE__;
    }

    private function uninstallTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminConfigurationPPL');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $del = $tab->delete();
        }
        return false;
    }

    use \PPLShipping\tmodule\TInstallDb;
    use \PPLShipping\tmodule\TAdminSetMedia;
    use \PPLShipping\tmodule\TDisplayExtraContent;
    use \PPLShipping\tmodule\TAdminOrder;
    use \PPLShipping\tmodule\TValidateOrder;
    use \PPLShipping\tmodule\TAdminProduct;
    use \PPLShipping\tmodule\TSmarty;

    public function install()
    {
        $success = parent::install();
        $success = $success && $this->installDB();
        $success = $success && $this->installTab();

        $methods = array_filter(get_class_methods($this), function ($item){
            return strpos($item, "hook") === 0;
        });

        $methods = array_map(function ($item) {
            return  lcfirst(preg_replace("~^hook~", "",$item));
        }, $methods);

        foreach ($methods as $class_method) {
            if(!$success)
                return $success;
            $success = $success && $this->registerHook($class_method);
        }

        /** @var AutoClearer $cacheClearer */
        if (method_exists($this, 'get')) {
            try {
                $cacheClearer = $this->get('prestashop.core.cache.clearer.auto_clearer');
                $cacheClearer->clear();
            }
            catch (\Exception $ex)
            {

            }
        }

        // 2) Smarty + XML
        \Tools::clearSmartyCache();
        \Tools::clearXMLCache();
        \Tools::clearAllCache();

        // 3) PHP OPcache (reset bajtkódu)
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        // 4) APCu
        if (function_exists('apcu_clear_cache')) {
            apcu_clear_cache();
        }

        return $success;
    }

    public function getOrderShippingCost($params, $shipping_cost)
    {
        return $shipping_cost;
    }

    public function getOrderShippingCostExternal($params)
    {

    }

    public function update()
    {
        $this->context->smarty->clearAllCache();
        return true;
    }

    public function uninstall()
    {
        $result = parent::uninstall();

        /** @var AutoClearer $cacheClearer */
        if (method_exists($this, 'get')) {
            try {
                $cacheClearer = $this->get('prestashop.core.cache.clearer.auto_clearer');
                $cacheClearer->clear();
            }
            catch (\Exception $ex)
            {

            }
        }

        // 2) Smarty + XML
        \Tools::clearSmartyCache();
        \Tools::clearXMLCache();
        \Tools::clearAllCache();

        // 3) PHP OPcache (reset bajtkódu)
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }
        // 4) APCu
        if (function_exists('apcu_clear_cache')) {
            apcu_clear_cache();
        }
        return $result;
    }
}