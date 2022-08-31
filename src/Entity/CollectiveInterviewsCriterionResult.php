<?php

namespace App\Entity;

use App\Repository\CollectiveInterviewsCriterionResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollectiveInterviewsCriterionResultRepository::class)]
class CollectiveInterviewsCriterionResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float')]
    private $result;

    #[ORM\ManyToOne(targetEntity: CollectiveInterviewsEvaluationCriterion::class, inversedBy: 'collectiveInterviewsCriterionResults')]
    #[ORM\JoinColumn(nullable: false)]
    private $criterion;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'collectiveInterviewsCriterionResults')]
    #[ORM\JoinColumn(nullable: false)]
    private $candidate;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'collectiveInterviewsCriterionResultsRecuiter')]
    #[ORM\JoinColumn(nullable: false)]
    private $recruiter;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResult(): ?float
    {
        return $this->result;
    }

    public function setResult(float $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getCriterion(): ?CollectiveInterviewsEvaluationCriterion
    {
        return $this->criterion;
    }

    public function setCriterion(?CollectiveInterviewsEvaluationCriterion $criterion): self
    {
        $this->criterion = $criterion;

        return $this;
    }

    public function getCandidate(): ?User
    {
        return $this->candidate;
    }

    public function setCandidate(?User $candidate): self
    {
        $this->candidate = $candidate;

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
}
