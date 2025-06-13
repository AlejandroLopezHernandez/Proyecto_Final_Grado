<?php

namespace App\EventSubscriber;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;


class RequestLoggerSubscriber implements EventSubscriberInterface
{
    private $logger;
    private $security;

    public function __construct(LoggerInterface $logger, Security $security)
    {
        $this->logger = $logger;
        $this->security = $security;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event)
    {
        $request = $event->getRequest();
        $route = $request->attributes->get('_route');
        $user = $this->security->getUser();

        $userId = $user ? $user->getUserIdentifier() : 'anonimo';
        $ip = $request->getClientIp();

        $this->logger->info('Se ha visitado la ruta', [
            'ruta' => $route,
            'user_id' => $userId,
            'ip' => $ip,
            'method' => $request->getMethod(),
            'uri' => $request->getRequestUri(),
        ]);
    }
}
