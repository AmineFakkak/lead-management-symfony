<?php

namespace App\Entity;

use App\Repository\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
#[ORM\HasLifecycleCallbacks] 
#[ORM\Entity(repositoryClass: EntityRepository::class)]
class Entity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $logo = null;

    #[ORM\Column(length: 7, nullable: true)]
    private ?string $color = null;

    #[ORM\Column]
    private ?bool $isActive = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Lead>
     */
    #[ORM\OneToMany(targetEntity: Lead::class, mappedBy: 'entity')]
    private Collection $leads;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'entity')]
    private Collection $users;

    public function __construct()
    {
        $this->leads = new ArrayCollection();
        $this->users = new ArrayCollection();
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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): static
    {
        $this->logo = $logo;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): static
    {
        $this->color = $color;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
   #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }
    /**
     * @return Collection<int, Lead>
     */
    public function getLeads(): Collection
    {
        return $this->leads;
    }

    public function addLead(Lead $lead): static
    {
        if (!$this->leads->contains($lead)) {
            $this->leads->add($lead);
            $lead->setEntity($this);
        }

        return $this;
    }

    public function removeLead(Lead $lead): static
    {
        if ($this->leads->removeElement($lead)) {
            // set the owning side to null (unless already changed)
            if ($lead->getEntity() === $this) {
                $lead->setEntity(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setEntity($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getEntity() === $this) {
                $user->setEntity(null);
            }
        }

        return $this;
    }
    public function __toString(): string
{
    return $this->getName() ?? '';   // ou $this->name si la propriété est public, mais on utilise le getter
}
}
