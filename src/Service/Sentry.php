<?php

namespace App\Service;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class Sentry
{
    public function sentryFilter(): callable
    {
        return function (\Sentry\Event $event, ?\Sentry\EventHint $hint): ?\Sentry\Event {
            // Ignore the event if it is an AccessDeniedException (Probably the session has run out.)
            if ($hint !== null && $hint->exception instanceof AccessDeniedException) {
                return null;
            }

            return $event;
        };
    }
}
