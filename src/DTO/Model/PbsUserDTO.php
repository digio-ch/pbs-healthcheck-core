<?php

declare(strict_types=1);

namespace App\DTO\Model;

use Symfony\Component\Security\Core\User\UserInterface;

class PbsUserDTO implements UserInterface
{
    private int $id;
    private string $email;
    private string $firstName;
    private string $lastName;
    private string $nickName;
    private string $birthday;
    private string $address;
    private string $zipCode;
    private string $town;
    private string $country;
    private string $correspondenceLanguage;
    private string $gender;
    /** @var array|PbsRoleDTO[] */
    private array $roles = [];
    /** @var array|GroupDTO[] */
    private array $groups = [];

    /**
     * PbsUserDTO constructor.
     */
    public function __construct(int $id, string $email, string $firstName, string $lastName, string $nickName)
    {
        $this->id = $id;
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->nickName = $nickName;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getNickName(): string
    {
        return $this->nickName;
    }

    public function getBirthday(): string
    {
        return $this->birthday;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function getTown(): string
    {
        return $this->town;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getCorrespondenceLanguage(): string
    {
        return $this->correspondenceLanguage;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * @return PbsRoleDTO[]|array
     */
    public function getPersonRoles(): array
    {
        return $this->roles;
    }

    public function setBirthday(string $birthday): void
    {
        $this->birthday = $birthday;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function setZipCode(string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    public function setTown(string $town): void
    {
        $this->town = $town;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function setCorrespondenceLanguage(string $correspondence_language): void
    {
        $this->correspondenceLanguage = $correspondence_language;
    }

    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    public function addPersonRole(PbsRoleDTO $role): void
    {
        $this->roles[] = $role;
    }

    /**
     * @return GroupDTO[]|array
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * @param GroupDTO[]|array $groups
     */
    public function setGroups(array $groups): void
    {
        $this->groups = $groups;
    }

    public function addGroup(GroupDTO $group): void
    {
        $this->groups[] = $group;
    }

    /**
     * @inheritDoc
     */
    public function getPassword(): ?string
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @deprecated
     * since Symfony 5.3, use getUserIdentifier() instead.
     * Implementation is still needed for conformance to interface.
     * May be removed after upgrading to future version.
     */
    public function getUsername(): string
    {
        return $this->email;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @inheritDoc
     */
    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }
}
