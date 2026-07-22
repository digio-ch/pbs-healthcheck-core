<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleListener
{
    #[AsEventListener(event: KernelEvents::REQUEST, priority: 1024)]
    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->getRequest()->headers->has('X-Locale')) {
            return;
        }

        $locale = $event->getRequest()->headers->get('X-Locale');

        if (in_array($locale, ['it', 'fr', 'de'])) {
            $event->getRequest()->setLocale($locale);
            return;
        }

        $event->getRequest()->setLocale('de');
    }
}
