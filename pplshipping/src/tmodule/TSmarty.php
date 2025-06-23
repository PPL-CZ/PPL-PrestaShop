<?php
namespace PPLShipping\tmodule;

trait TSmarty
{

    public function tmediaSetMediaHookActionDispatcherBefore ($params)
    {
        try {

            $this->context->smarty->registerPlugin("modifier", "pplfilter", "smarty_pplfilter");

            $this->context->smarty->registerPlugin("modifier", "ppljoin", "smarty_ppljoin");

            $this->context->smarty->registerPlugin("modifier", "pplfileurl", "smarty_pplfileurl");

            $this->context->smarty->registerPlugin("modifier", "pplimageurl", "smarty_pplimageurl");

            $this->context->smarty->registerPlugin("modifier", "pplpackageerrors", "smarty_pplpackageerrors");

            $this->context->smarty->registerPlugin("modifier", "ppllabelprint", "smarty_ppllabelprint");

            $this->context->smarty->registerPlugin("modifier", "pplprintlabel", "smarty_pplprintlabel");

            $this->context->smarty->registerPlugin("modifier", "ppljser", "smarty_ppljser");

            $this->context->smarty->registerPlugin("modifier", "pplattr", "smarty_pplattr");

            $this->context->smarty->registerPlugin("modifier", "pplisset", "smarty_pplisset");

            $this->context->smarty->registerPlugin("modifier", "pplarray_key_last", "smarty_pplarraykeylast");

            $this->context->smarty->registerPlugin("modifier", "pplinarray", "smarty_pplinarray");

            if (strpos(_PS_VERSION_, "1.7") === 0) {
                $this->context->smarty->registerFilter("pre", "smarty_pplprefilter");
                $this->context->smarty->registerPlugin("modifier", "pplfiltershipping", "smarty_pplfiltershipping");
                $this->context->smarty->registerPlugin("modifier", "pplfilterpayments", "smarty_pplfilterpayments");
            }
        }
        catch (\Exception $ex)
        {

        }
    }
}