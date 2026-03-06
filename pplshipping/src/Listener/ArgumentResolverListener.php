<?php
namespace  PPLShipping\Listener;

use PrestaShop\PrestaShop\Core\Search\Filters\OrderFilters;
use PrestaShopBundle\Controller\Admin\Sell\Order\OrderController;
use Symfony\Component\HttpKernel\Event\ControllerArgumentsEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ArgumentResolverListener {

    /**
     * @var OrderFilters
     */
    public $orderFilters;

    public function onKernelControllerArguments( $event)
    {
        /**
         * @var ControllerArgumentsEvent $event
         */
        $arguments = $event->getArguments();
        foreach ($arguments as $val)
        {
            if ($val instanceof OrderFilters) {
                $this->orderFilters = $val;
                break;
            }
        }

        return null;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'onKernelControllerArguments',
        ];
    }
}