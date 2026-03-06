<?php

namespace PPLShipping\tmodule;


use PrestaShopBundle\Security\Admin\UserTokenManager;

trait TToken
{
    public function isTokenValid($token)
    {
        if (version_compare(_PS_VERSION_, '9.0.0', '>=')) {
            $container = \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance();
            $tokenStorage = $container->get(UserTokenManager::class);
            return $tokenStorage->isTokenValid();
        }
        else if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            $employee = \Context::getContext()->employee;
            if ($employee && $employee->id && $employee->isLoggedBack()) {
                return true;
            }
            return false;
        } else {
            $employee = \Context::getContext()->employee;
            if ($employee && $employee->id && $employee->isLoggedBack()) {
                return true;
            }
            if (!$this->token)
                $this->token = \Tools::getAdminToken("AdminConfigurationPPL");
            return $this->token === $token;
        }
    }

    public function createToken()
    {
        if (version_compare(_PS_VERSION_, '8.0.0', '>=')) {
            $container = \PrestaShop\PrestaShop\Adapter\SymfonyContainer::getInstance();
            return $container->get('security.token_storage')->getToken();
        } else {
            return \Tools::getAdminToken("AdminConfigurationPPL");
        }
    }

}