<?php

namespace App\Entity;

use App\Repository\InterviewCriterionResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterviewCriterionResultRepository::class)]
class InterviewCriterionResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float')]
    private $result;

    #[ORM\ManyToOne(targetEntity: InterviewsEvaluationCriterion::class, inversedBy: 'interviewCriterionResults')]
    #[ORM\JoinColumn(nullable: false)]
    private $criterion;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'interviewCriterionResultsRecruiter')]
    #[ORM\JoinColumn(nullable: false)]
    private $recruiter;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'interviewCriterionResults')]
    #[ORM\JoinColumn(nullable: false)]
    private $candidate;

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

    public function getCriterion(): ?InterviewsEvaluationCriterion
    {
        return $this->criterion;
    }

    public function setCriterion(?InterviewsEvaluationCriterion $criterion): self
    {
        $this->criterion = $criterion;

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

    public function getCandidate(): ?User
    {
        return $this->candidate;
    }

    public function setCandidate(?User $candidate): self
    {
        $this->candidate = $candidate;

        return $this;
    }
}
