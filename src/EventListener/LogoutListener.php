<?php

namespace App\EventListener;

use App\Entity\ActivityLog;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutListener implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }

    public function onLogout(LogoutEvent $event): void
    {
        $token = $event->getToken();
        $user = $token?->getUser();
        $request = $event->getRequest();

        try {
            $log = new ActivityLog();
            $log->setUserId(method_exists($user, 'getId') ? $user->getId() : null);
            $log->setUsername(method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : (method_exists($user, 'getUsername') ? $user->getUsername() : null));
            $roles = method_exists($user, 'getRoles') ? $user->getRoles() : [];
            $log->setRole(is_array($roles) ? implode(',', $roles) : (string)$roles);
            $log->setAction('LOGOUT');
            $log->setTargetData(null);
            $log->setIpAddress($request->getClientIp());

            $this->entityManager->persist($log);
            $this->entityManager->flush();
        } catch (\Throwable $e) {
            // don't break logout flow
        }
    }
}
