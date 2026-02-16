<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
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
    private ?user $assignedTo = null;

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
        throw new \InvalidArgumentException("Invalid priority");
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
        throw new \InvalidArgumentException("Invalid status");
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

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getlead(): ?Lead
    {
        return $this->lead;
    }

    public function setlead(?Lead $lead): static
    {
        $this->lead = $lead;

        return $this;
    }

    public function getAssignedTo(): ?user
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?user $assignedTo): static
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }
}
