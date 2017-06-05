<?php
namespace Czesio\NestablePageBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Czesio\NestablePageBundle\Controller\PageController;
use Czesio\NestablePageBundle\Controller\PageMetaController;

class ControllerListener
{
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();

        /*
         * controller must come in an array
         */
        if (!is_array($controller)) {
            return;
        }

        if ($controller[0] instanceof PageController || $controller[0] instanceof PageMetaController) {
            $controller[0]->init();
        }
    }
}
