<?php

namespace App\Service;

use App\DTO\Model\PbsUserDTO;
use Sentry\UserDataBag;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class Sentry
{

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    /**
     * Filter out acceptable Excepitons (HTTP 401) and sensitive data.
     * @return callable
     */
    public function sentryFilter(): callable
    {
        return function (\Sentry\Event $event, ?\Sentry\EventHint $hint): ?\Sentry\Event {
            // Ignore the event if it is an AccessDeniedException (Probably the session has run out.)
            if ($hint !== null && $hint->exception instanceof AccessDeniedException) {
                return null;
            }

            $user = $this->security->getUser();
            if ($user instanceof PbsUserDTO) {
                $userBag = new UserDataBag($user->getId(), null, null, null, null);
                $userBag->setMetadata('Nickname', $user->getNickName());
            } else {
                $userBag = new UserDataBag(null, null, null, $user->getUsername(), null);
                $userBag->setMetadata('Is PbsUserDTO', false);
            }
            $event->setUser($userBag);
            return $event;
        };
    }
}
