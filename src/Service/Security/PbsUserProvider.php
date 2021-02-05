<?php

namespace App\Service\Security;

use App\DTO\Model\PbsUserDTO;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class PbsUserProvider implements UserProviderInterface
{
    /**
     * @inheritDoc
     */
    public function loadUserByUsername(string $username)
    {
        // TODO: Implement loadUserByUsername() method.
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function supportsClass(string $class)
    {
        return $class === PbsUserDTO::class;
    }
}
