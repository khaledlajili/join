<?php

namespace App\Entity;

use App\Repository\InterviewsAvailabilityRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterviewsAvailabilityRepository::class)]
class InterviewsAvailability
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'boolean')]
    private $available;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'interviewsAvailabilities')]
    #[ORM\JoinColumn(nullable: false)]
    private $recruiter;

    #[ORM\ManyToOne(targetEntity: Interview::class, inversedBy: 'interviewsAvailabilities')]
    #[ORM\JoinColumn(nullable: false)]
    private $interview;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): self
    {
        $this->available = $available;

        return $this;
    }

    public function getRecruiter(): ?User
    {
        return $this->recruiter;
    }

    public function setRecruiter(?User $recruiter): self
    {
        $this->recruiter = $recruiter;

        return $this;
    }

    public function getInterview(): ?Interview
    {
        return $this->interview;
    }

    public function setInterview(?Interview $interview): self
    {
        $this->interview = $interview;

        return $this;
    }
}
