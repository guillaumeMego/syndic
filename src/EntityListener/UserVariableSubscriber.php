<?php

namespace App\EntityListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserVariableSubscriber implements EventSubscriberInterface
{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        // On récupère le service TokenStorage
        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => 'addUserVariable',
        ];
    }

    public function addUserVariable(ViewEvent $event)
    {
        $user = null;
        $token = $this->tokenStorage->getToken();

        if ($token) {
            $user = $token->getUser();
        }

        // On ajoute la variable user à la vue si elle est connectée
        $event->getControllerResult()->user = $user;
        
    }
}
