<?php

namespace App\DTO\Mapper;

use App\DTO\Model\PbsUserDTO;

class PbsUserMapper
{
    /**
     * @param array $user
     * @return PbsUserDTO
     */
    public static function createFromArray(array $user): PbsUserDTO
    {
        $pbsUser = new PbsUserDTO(
            $user['id'],
            $user['email'],
            $user['first_name'],
            $user['last_name'],
            $user['nickname'] ?? '' // Apparently field doesn't exist if user has no nickname
        );

        $pbsUser->setGender($user['gender'] ?? '');
        $pbsUser->setBirthday($user['birthday'] ?? '');
        $pbsUser->setCountry($user['country'] ?? '');
        $pbsUser->setAddress($user['address'] ?? '');
        $pbsUser->setTown($user['town'] ?? '');
        $pbsUser->setZipCode($user['zip_code'] ?? '');
        $pbsUser->setCorrespondenceLanguage($user['correspondence_language'] ?? '');

        foreach ($user['roles'] ?? [] as $role) {
            $pbsUser->addPersonRole(PbsRoleMapper::createFromArray($role));
        }

        return $pbsUser;
    }
}
