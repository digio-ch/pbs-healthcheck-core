<?php

namespace App\DTO\Model;

use Symfony\Component\Security\Core\User\UserInterface;

class PbsUserDTO implements UserInterface
{
    /** @var int */
    private $id;
    /** @var string */
    private $email;
    /** @var string */
    private $firstName;
    /** @var string */
    private $lastName;
    /** @var string */
    private $nickName;
    /** @var string */
    private $birthday;
    /** @var string */
    private $address;
    /** @var string */
    private $zipCode;
    /** @var string */
    private $town;
    /** @var string */
    private $country;
    /** @var string */
    private $correspondenceLanguage;
    /** @var string */
    private $gender;
    /** @var array|PbsRoleDTO[] */
    private $roles;
    /** @var array|GroupDTO[] */
    private $groups;

    /**
     * PbsUserDTO constructor.
     * @param int $id
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param string $nickName
     */
    public function __construct(int $id, string $email, string $firstName, string $lastName, string $nickName)
    {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->nickName = $nickName;
        $this->roles = [];
        $this->groups = [];
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getNickName(): string
    {
        return $this->nickName;
    }

    /**
     * @return string
     */
    public function getBirthday(): string
    {
        return $this->birthday;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    /**
     * @return string
     */
    public function getTown(): string
    {
        return $this->town;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getCorrespondenceLanguage(): string
    {
        return $this->correspondenceLanguage;
    }

    /**
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * @return PbsRoleDTO[]|array
     */
    public function getPersonRoles()
    {
        return $this->roles;
    }

    /**
     * @param string $birthday
     */
    public function setBirthday(string $birthday): void
    {
        $this->birthday = $birthday;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @param string $zipCode
     */
    public function setZipCode(string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @param string $town
     */
    public function setTown(string $town): void
    {
        $this->town = $town;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    /**
     * @param string $correspondence_language
     */
    public function setCorrespondenceLanguage(string $correspondence_language): void
    {
        $this->correspondenceLanguage = $correspondence_language;
    }

    /**
     * @param string $gender
     */
    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    public function addPersonRole(PbsRoleDTO $role)
    {
        $this->roles[] = $role;
    }

    /**
     * @return GroupDTO[]|array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param GroupDTO[]|array $groups
     */
    public function setGroups($groups): void
    {
        $this->groups = $groups;
    }

    public function addGroup(GroupDTO $group)
    {
        $this->groups[] = $group;
    }

    /**
     * @inheritDoc
     */
    public function getPassword()
    {
        // TODO: Implement getPassword() method.
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * @inheritDoc
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @inheritDoc
     */
    public function getRoles()
    {
        return ['ROLE_USER'];
    }
}
