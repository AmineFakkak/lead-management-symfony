<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
#[ORM\HasLifecycleCallbacks] // ← AJOUTEZ CETTE LIGNE
class Task
{
    public const PRIORITIES = ['Basse', 'Normale', 'Haute', 'Urgente'];
    public const STATUSES = ['À faire', 'En cours', 'Terminée', 'Annulée'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dueDate = null;

    #[ORM\Column(length: 20)]
    private ?string $priority = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $completedAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?Lead $lead = null;

    #[ORM\ManyToOne(inversedBy: 'tasks')]
    private ?User $assignedTo = null; // ← CORRIGEZ : 'user' → 'User' (majuscule)

    // LIFECYCLE CALLBACK - AJOUTEZ CETTE MÉTHODE
    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // OPTIONNEL : Mettre à jour completedAt automatiquement
    #[ORM\PreUpdate]
    public function checkCompletion(): void
    {
        if ($this->status === 'Terminée' && $this->completedAt === null) {
            $this->completedAt = new \DateTimeImmutable();
        }
        
        if ($this->status !== 'Terminée' && $this->completedAt !== null) {
            $this->completedAt = null;
        }
    }

    // Getters et Setters...

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getDueDate(): ?\DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTime $dueDate): static
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): static
    {
        if (!in_array($priority, self::PRIORITIES, true)) {
            throw new \InvalidArgumentException("Priorité invalide");
        }
        $this->priority = $priority;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        if (!in_array($status, self::STATUSES, true)) {
            throw new \InvalidArgumentException("Statut invalide");
        }
        $this->status = $status;
        return $this;
    }

    public function getCompletedAt(): ?\DateTimeImmutable
    {
        return $this->completedAt;
    }

    public function setCompletedAt(?\DateTimeImmutable $completedAt): static
    {
        $this->completedAt = $completedAt;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    // On garde le setter mais il ne sera pas utilisé par le formulaire
    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getLead(): ?Lead
    {
        return $this->lead;
    }

    public function setLead(?Lead $lead): static
    {
        $this->lead = $lead;
        return $this;
    }

    public function getAssignedTo(): ?User // ← CORRIGEZ LE TYPE DE RETOUR
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $assignedTo): static // ← CORRIGEZ LE TYPE DU PARAMÈTRE
    {
        $this->assignedTo = $assignedTo;
        return $this;
    }
}