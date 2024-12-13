<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Assert\Email(message: "Invalid email format.")]
    private ?string $email = null;

    #[ORM\Column(type: Types::TEXT, length: 255)]
    private ?string $password = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $roles = [];

    #[ORM\Column]
    #[Assert\Regex(
        pattern: "/^0[1-9][0-9]{8}$/",
        message: "Invalid phone number format."
    )]
    private ?string $phoneNumber = null;

    #[ORM\OneToMany(mappedBy: 'Relations', targetEntity: Reservation::class)]
    private Collection $Relations;

    public function __construct()
    {
        $this->Relations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPhoneNumber(): ?int
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(int $phoneNumber): static
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getRelations(): Collection
    {
        return $this->Relations;
    }

    public function addRelation(Reservation $reservation): static
    {
        if (!$this->Relations->contains($reservation)) {
            $this->Relations[] = $reservation;
            $reservation->setRelations($this);
        }

        return $this;
    }

    public function removeRelation(Reservation $reservation): static
    {
        if ($this->Relations->removeElement($reservation)) {
            if ($reservation->getRelations() === $this) {
                $reservation->setRelations(null);
            }
        }

        return $this;
    }
}
