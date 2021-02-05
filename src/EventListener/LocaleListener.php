<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;

class LocaleListener
{
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->getRequest()->headers->has('accept-language')) {
            return;
        }

        $locale = $event->getRequest()->headers->get('accept-language');

        switch ($locale) {
            case str_contains($locale, 'it'):
                $event->getRequest()->setLocale('it');
                break;
            case str_contains($locale, 'fr'):
                $event->getRequest()->setLocale('fr');
                break;
            default:
                $event->getRequest()->setLocale('de');
        }
    }
}
