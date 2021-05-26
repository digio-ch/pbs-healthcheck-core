<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class LocaleListener
{
    public function onKernelRequest(RequestEvent $event)
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
