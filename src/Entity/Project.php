<?php

namespace App\Entity;

use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
#[ORM\HasLifecycleCallbacks] 
#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    public const STATUSES = ['En cours', 'Terminé', 'Annulé'];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $endDate = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, nullable: true)]
    private ?string $budget = null;

    #[ORM\Column(nullable: true)]
    private ?int $targetLeads = null;

    #[ORM\Column(length: 30)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    /**
     * @var Collection<int, Lead>
     */
    #[ORM\OneToMany(targetEntity: Lead::class, mappedBy: 'project')]
    private Collection $leads;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?entity $entity = null;

    /**
     * @var Collection<int, Campaign>
     */
    #[ORM\OneToMany(targetEntity: Campaign::class, mappedBy: 'project')]
    private Collection $campaigns;

    public function __construct()
    {
        $this->leads = new ArrayCollection();
        $this->campaigns = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getBudget(): ?string
    {
        return $this->budget;
    }

    public function setBudget(?string $budget): static
    {
        $this->budget = $budget;

        return $this;
    }

    public function getTargetLeads(): ?int
    {
        return $this->targetLeads;
    }

    public function setTargetLeads(?int $targetLeads): static
    {
        $this->targetLeads = $targetLeads;

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


    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
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
            $lead->setProject($this);
        }

        return $this;
    }

    public function removeLead(Lead $lead): static
    {
        if ($this->leads->removeElement($lead)) {
            // set the owning side to null (unless already changed)
            if ($lead->getProject() === $this) {
                $lead->setProject(null);
            }
        }

        return $this;
    }

    public function getEntity(): ?entity
    {
        return $this->entity;
    }

    public function setEntity(?entity $entity): static
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @return Collection<int, Campaign>
     */
    public function getCampaigns(): Collection
    {
        return $this->campaigns;
    }

    public function addCampaign(Campaign $campaign): static
    {
        if (!$this->campaigns->contains($campaign)) {
            $this->campaigns->add($campaign);
            $campaign->setProject($this);
        }

        return $this;
    }

    public function removeCampaign(Campaign $campaign): static
    {
        if ($this->campaigns->removeElement($campaign)) {
            // set the owning side to null (unless already changed)
            if ($campaign->getProject() === $this) {
                $campaign->setProject(null);
            }
        }

        return $this;
    }
    public function __toString(): string
{
    return $this->getName() ?? '';
}
#[ORM\PrePersist]
public function setCreatedAtValue(): void
{
    $this->createdAt = new \DateTimeImmutable();
}
}
