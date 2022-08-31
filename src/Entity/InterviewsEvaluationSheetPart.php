<?php

namespace App\Entity;

use App\Repository\InterviewsEvaluationSheetPartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InterviewsEvaluationSheetPartRepository::class)]
class InterviewsEvaluationSheetPart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private $name;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank]
    private $coefficient;

    #[ORM\ManyToOne(targetEntity: RecruitmentSession::class, inversedBy: 'interviewsEvaluationSheetParts')]
    #[ORM\JoinColumn(nullable: false)]
    private $recruitmentSession;

    #[ORM\ManyToMany(targetEntity: Department::class, inversedBy: 'interviewsEvaluationSheetParts')]
    private $departments;

    #[ORM\OneToMany(mappedBy: 'sheetPart', targetEntity: InterviewsEvaluationCriterion::class, cascade: ["persist"])]
    #[Assert\Valid]
    private $interviewsEvaluationCriteria;

    public function __construct()
    {
        $this->departments = new ArrayCollection();
        $this->interviewsEvaluationCriteria = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getCoefficient(): ?float
    {
        return $this->coefficient;
    }

    public function setCoefficient(float $coefficient): self
    {
        $this->coefficient = $coefficient;

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

    /**
     * @return Collection<int, Department>
     */
    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function addDepartment(Department $department): self
    {
        if (!$this->departments->contains($department)) {
            $this->departments[] = $department;
        }

        return $this;
    }

    public function removeDepartment(Department $department): self
    {
        $this->departments->removeElement($department);

        return $this;
    }

    /**
     * @return Collection<int, InterviewsEvaluationCriterion>
     */
    public function getInterviewsEvaluationCriteria(): Collection
    {
        return $this->interviewsEvaluationCriteria;
    }

    public function addInterviewsEvaluationCriterion(InterviewsEvaluationCriterion $interviewsEvaluationCriterion): self
    {
        if (!$this->interviewsEvaluationCriteria->contains($interviewsEvaluationCriterion)) {
            $this->interviewsEvaluationCriteria[] = $interviewsEvaluationCriterion;
            $interviewsEvaluationCriterion->setSheetPart($this);
        }

        return $this;
    }

    public function removeInterviewsEvaluationCriterion(InterviewsEvaluationCriterion $interviewsEvaluationCriterion): self
    {
        if ($this->interviewsEvaluationCriteria->removeElement($interviewsEvaluationCriterion)) {
            // set the owning side to null (unless already changed)
            if ($interviewsEvaluationCriterion->getSheetPart() === $this) {
                $interviewsEvaluationCriterion->setSheetPart(null);
            }
        }

        return $this;
    }
}
