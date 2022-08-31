<?php

namespace App\Entity;

use App\Repository\CollectiveInterviewsEvaluationCriterionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollectiveInterviewsEvaluationCriterionRepository::class)]
class CollectiveInterviewsEvaluationCriterion
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $criterion;

    #[ORM\OneToMany(mappedBy: 'criterion', targetEntity: CollectiveInterviewsCriterionResult::class,orphanRemoval: true)]
    private $collectiveInterviewsCriterionResults;

    #[ORM\ManyToOne(targetEntity: RecruitmentSession::class, inversedBy: 'collectiveInterviewsEvaluationCriteria')]
    #[ORM\JoinColumn(nullable: false)]
    private $recruitmentSession;

    public function __construct()
    {
        $this->collectiveInterviewsCriterionResults = new ArrayCollection();
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

    /**
     * @return Collection<int, CollectiveInterviewsCriterionResult>
     */
    public function getCollectiveInterviewsCriterionResults(): Collection
    {
        return $this->collectiveInterviewsCriterionResults;
    }

    public function addCollectiveInterviewsCriterionResult(CollectiveInterviewsCriterionResult $collectiveInterviewsCriterionResult): self
    {
        if (!$this->collectiveInterviewsCriterionResults->contains($collectiveInterviewsCriterionResult)) {
            $this->collectiveInterviewsCriterionResults[] = $collectiveInterviewsCriterionResult;
            $collectiveInterviewsCriterionResult->setCriterion($this);
        }

        return $this;
    }

    public function removeCollectiveInterviewsCriterionResult(CollectiveInterviewsCriterionResult $collectiveInterviewsCriterionResult): self
    {
        if ($this->collectiveInterviewsCriterionResults->removeElement($collectiveInterviewsCriterionResult)) {
            // set the owning side to null (unless already changed)
            if ($collectiveInterviewsCriterionResult->getCriterion() === $this) {
                $collectiveInterviewsCriterionResult->setCriterion(null);
            }
        }

        return $this;
    }

    public function getRecruitmentSession(): ?RecruitmentSession
    {
        return $this->recruitmentSession;
    }

    public function setRecruitmentSession(?RecruitmentSession $recruitmentSession): self
    {
        $this->recruitmentSession = $recruitmentSession;

        return $this;
    }
}
