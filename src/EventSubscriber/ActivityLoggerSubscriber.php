<?php

namespace App\EventSubscriber;

use App\Entity\ActivityLog;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ActivityLoggerSubscriber implements EventSubscriber
{
    public function __construct(private TokenStorageInterface $tokenStorage, private RequestStack $requestStack)
    {
    }

    public function getSubscribedEvents(): array
    {
        return [Events::onFlush];
    }

    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getObjectManager();
        $uow = $em->getUnitOfWork();

        $user = null;
        $roles = [];
        $username = null;
        $token = $this->tokenStorage->getToken();
        if ($token && is_object($token->getUser())) {
            $user = $token->getUser();
            $username = method_exists($user, 'getUserIdentifier') ? $user->getUserIdentifier() : (method_exists($user, 'getUsername') ? $user->getUsername() : null);
            $roles = method_exists($user, 'getRoles') ? $user->getRoles() : [];
        }

        $ipAddress = null;
        $request = $this->requestStack->getCurrentRequest();
        if ($request) {
            $ipAddress = $request->getClientIp();
        }

        // Avoid logging ActivityLog entity changes
        $inserts = $uow->getScheduledEntityInsertions();
        $updates = $uow->getScheduledEntityUpdates();
        $deletes = $uow->getScheduledEntityDeletions();

        $now = new \DateTimeImmutable();

        // Helper to create and schedule an ActivityLog
        $createLog = function (string $action, string $target) use ($em, $uow, $user, $username, $roles, $now, $ipAddress) {
            $log = new ActivityLog();
            $log->setAction($action);
            $log->setTargetData($target);
            $log->setCreatedAt($now);
            if ($user && method_exists($user, 'getId')) {
                $log->setUserId($user->getId());
            }
            $log->setUsername($username);
            $log->setRole(is_array($roles) ? implode(',', $roles) : (string)$roles);
            $log->setIpAddress($ipAddress);

            $em->persist($log);
            $class = $em->getClassMetadata(ActivityLog::class);
            $uow->computeChangeSet($class, $log);
        };

        foreach ($inserts as $entity) {
            if ($entity instanceof ActivityLog) {
                continue;
            }
            $class = (new \ReflectionClass($entity))->getShortName();
            // Attempt to pick an identifying field
            $id = null;
            if (method_exists($entity, 'getId')) {
                try {
                    $id = $entity->getId();
                } catch (\Throwable $e) {
                    $id = null;
                }
            }
            $target = $class . ($id ? ' (ID: ' . $id . ')' : '');
            $createLog('CREATE', $target);
        }

        foreach ($updates as $entity) {
            if ($entity instanceof ActivityLog) {
                continue;
            }
            $class = (new \ReflectionClass($entity))->getShortName();
            $changeSet = $uow->getEntityChangeSet($entity);
            $changes = [];
            foreach ($changeSet as $field => $values) {
                $old = is_scalar($values[0]) ? $values[0] : json_encode($values[0]);
                $new = is_scalar($values[1]) ? $values[1] : json_encode($values[1]);
                $changes[] = $field . ': ' . $old . ' -> ' . $new;
            }
            $target = $class . ' Changes: ' . implode('; ', $changes);
            $createLog('UPDATE', $target);
        }

        foreach ($deletes as $entity) {
            if ($entity instanceof ActivityLog) {
                continue;
            }
            $class = (new \ReflectionClass($entity))->getShortName();
            $id = null;
            if (method_exists($entity, 'getId')) {
                try {
                    $id = $entity->getId();
                } catch (\Throwable $e) {
                    $id = null;
                }
            }
            $target = $class . ($id ? ' (ID: ' . $id . ')' : '');
            $createLog('DELETE', $target);
        }
    }
}
