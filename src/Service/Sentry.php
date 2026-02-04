<?php

namespace App\Service;

use App\DTO\Model\PbsUserDTO;
use Psr\Log\LoggerInterface;
use Sentry\UserDataBag;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;

class Sentry
{
    private Security $security;

    private LoggerInterface $logger;

    private bool $rerouteToFile;

    public function __construct(Security $security, LoggerInterface $logger, string $sentryDSN)
    {
        $this->security = $security;
        $this->logger = $logger;
        $this->rerouteToFile = empty($sentryDSN);
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
                $event->setUser($userBag);
            } elseif (!is_null($user)) {
                $userBag = new UserDataBag(null, null, null, $user->getUsername(), null);
                $userBag->setMetadata('Is PbsUserDTO', false);
                $event->setUser($userBag);
            }



            if ($this->rerouteToFile) {
                $this->logger->error($hint->exception->getMessage(), [
                    "file" => $hint->exception->getFile(),
                    "line" => $hint->exception->getLine(),
                ]);

                return null;
            }

            return $event;
        };
    }
}
