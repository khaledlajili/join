<?php

namespace App\Entity;

use App\Repository\InterviewsEvaluationCriterionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterviewsEvaluationCriterionRepository::class)]
class InterviewsEvaluationCriterion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $criterion;

    #[ORM\ManyToOne(targetEntity: InterviewsEvaluationSheetPart::class, inversedBy: 'interviewsEvaluationCriteria')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private $sheetPart;

    #[ORM\OneToMany(mappedBy: 'criterion', targetEntity: InterviewCriterionResult::class)]
    private $interviewCriterionResults;

    public function __construct()
    {
        $this->interviewCriterionResults = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCriterion(): ?string
    {
        return $this->criterion;
    }

    public function setCriterion(string $criterion): self
    {
        $this->criterion = $criterion;

        return $this;
    }

    public function getSheetPart(): ?InterviewsEvaluationSheetPart
    {
        return $this->sheetPart;
    }

    public function setSheetPart(?InterviewsEvaluationSheetPart $sheetPart): self
    {
        $this->sheetPart = $sheetPart;

        return $this;
    }

    /**
     * @return Collection<int, InterviewCriterionResult>
     */
    public function getInterviewCriterionResults(): Collection
    {
        return $this->interviewCriterionResults;
    }

    public function addInterviewCriterionResult(InterviewCriterionResult $interviewCriterionResult): self
    {
        if (!$this->interviewCriterionResults->contains($interviewCriterionResult)) {
            $this->interviewCriterionResults[] = $interviewCriterionResult;
            $interviewCriterionResult->setCriterion($this);
        }

        return $this;
    }

    public function removeInterviewCriterionResult(InterviewCriterionResult $interviewCriterionResult): self
    {
        if ($this->interviewCriterionResults->removeElement($interviewCriterionResult)) {
            // set the owning side to null (unless already changed)
            if ($interviewCriterionResult->getCriterion() === $this) {
                $interviewCriterionResult->setCriterion(null);
            }
        }

        return $this;
    }

}
