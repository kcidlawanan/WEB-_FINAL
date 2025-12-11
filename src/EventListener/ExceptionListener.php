<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;

class ExceptionListener implements EventSubscriberInterface
{
    public function __construct(private Environment $twig)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        // Handle AccessDeniedException with 403 response
        if ($exception instanceof AccessDeniedException) {
            $statusCode = Response::HTTP_FORBIDDEN;
            $message = $exception->getMessage() ?: 'Access Denied';

            try {
                $content = $this->twig->render('error/403.html.twig', [
                    'message' => $message,
                ]);
            } catch (\Exception) {
                // Fallback to simple HTML if template rendering fails
                $content = '<html><body><h1>403 Forbidden</h1><p>' . htmlspecialchars($message) . '</p></body></html>';
            }

            $response = new Response($content, $statusCode, [
                'Content-Type' => 'text/html',
            ]);

            $event->setResponse($response);
        }
    }
}
