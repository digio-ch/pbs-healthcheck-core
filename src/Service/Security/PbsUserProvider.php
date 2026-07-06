<?php

namespace App\Service\Security;

use App\DTO\Model\PbsUserDTO;
use Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class PbsUserProvider implements UserProviderInterface
{
    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function supportsClass(string $class): bool
    {
        return $class === PbsUserDTO::class;
    }

    /**
     * This function can be ignored because we set the user in the Authenticator
     * @throws Exception
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        throw new Exception('not needed');
    }
}
