<?php

namespace App\Entity;

use App\Repository\UsersRepository;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use App\Service\ItemsValidator;

/**
 * @ORM\Entity(repositoryClass=UsersRepository::class)
 */
class Users
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="uuid")
     */
    private $user_group;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=1, nullable=true)
     */
    private $gender;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dob;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $active;

    public function getId()
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUserGroup()
    {
        return $this->user_group;
    }

    public function setUserGroup($user_group): self
    {
        $this->user_group = $user_group;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getDob(): ?\DateTimeInterface
    {
        return $this->dob;
    }

    public function setDob(?\DateTimeInterface $dob): self
    {
        $this->dob = $dob;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function toArray() :array
    {
        return [
            'id' => $this->getId(),
            'email' => $this->getEmail(),
            'group' => $this->getUserGroup(),
            'firstName' => $this->getFirstName(),
            'lastName' => $this->getLastName(),
            'gender' => $this->getGender(),
            'dob' => is_null($this->getDob()) ? Null : $this->getDob()->format('d.m.Y'),
            'active' => $this->getActive()
        ];
    }

    public function fromArray(array $arr)
    {
        if (isset($arr['email'])) {
            $this->setEmail($arr['email']);
        }
        if (isset($arr['dob'])) {
            $this->setDob(empty($arr['dob']) ? Null : \DateTime::createFromFormat('d.m.Y', $arr['dob']));
        }
        if (isset($arr['group'])) {
            $this->setUserGroup(empty($arr['group']) ? ItemsValidator::DEFAULT_GROUPID : $arr['group']);
        }
        if (isset($arr['firstName'])) {
            $this->setFirstName($arr['firstName']);
        }
        if (isset($arr['lastName'])) {
            $this->setLastName($arr['lastName']);
        }
        if (isset($arr['gender'])) {
            $this->setGender($arr['gender']);
        }
        if (isset($arr['active'])) {
            $this->setActive($arr['active']);
        }
        return true;
    }
}
