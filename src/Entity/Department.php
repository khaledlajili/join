<?php

namespace App\Entity;

use App\Repository\DepartmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
#[UniqueEntity('name')]
class Department
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private $name;

    #[ORM\ManyToMany(targetEntity: PreRegistrationFormField::class, mappedBy: 'departments')]
    private $preRegistrationFormFields;

    #[ORM\ManyToMany(targetEntity: InterviewsEvaluationSheetPart::class, mappedBy: 'departments')]
    private $interviewsEvaluationSheetParts;

    #[ORM\ManyToMany(targetEntity: Interview::class, mappedBy: 'departments')]
    private $interviews;

    #[ORM\OneToMany(mappedBy: 'department', targetEntity: TechnicalTest::class)]
    private $technicalTests;

    #[ORM\OneToMany(mappedBy: 'departmentChosen', targetEntity: Result::class)]
    private $results;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'departments')]
    private Collection $users;

    public function __construct()
    {
        $this->preRegistrationFormFields = new ArrayCollection();
        $this->interviewsEvaluationSheetParts = new ArrayCollection();
        $this->interviews = new ArrayCollection();
        $this->technicalTests = new ArrayCollection();
        $this->results = new ArrayCollection();
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

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }


    /**
     * @return Collection<int, PreRegistrationFormField>
     */
    public function getPreRegistrationFormFields(): Collection
    {
        return $this->preRegistrationFormFields;
    }

    public function addPreRegistrationFormField(PreRegistrationFormField $preRegistrationFormField): self
    {
        if (!$this->preRegistrationFormFields->contains($preRegistrationFormField)) {
            $this->preRegistrationFormFields[] = $preRegistrationFormField;
            $preRegistrationFormField->addDepartment($this);
        }

        return $this;
    }

    public function removePreRegistrationFormField(PreRegistrationFormField $preRegistrationFormField): self
    {
        if ($this->preRegistrationFormFields->removeElement($preRegistrationFormField)) {
            $preRegistrationFormField->removeDepartment($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, InterviewsEvaluationSheetPart>
     */
    public function getInterviewsEvaluationSheetParts(): Collection
    {
        return $this->interviewsEvaluationSheetParts;
    }

    public function addInterviewsEvaluationSheetPart(InterviewsEvaluationSheetPart $interviewsEvaluationSheetPart): self
    {
        if (!$this->interviewsEvaluationSheetParts->contains($interviewsEvaluationSheetPart)) {
            $this->interviewsEvaluationSheetParts[] = $interviewsEvaluationSheetPart;
            $interviewsEvaluationSheetPart->addDepartment($this);
        }

        return $this;
    }

    public function removeInterviewsEvaluationSheetPart(InterviewsEvaluationSheetPart $interviewsEvaluationSheetPart): self
    {
        if ($this->interviewsEvaluationSheetParts->removeElement($interviewsEvaluationSheetPart)) {
            $interviewsEvaluationSheetPart->removeDepartment($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Interview>
     */
    public function getInterviews(): Collection
    {
        return $this->interviews;
    }

    public function addInterview(Interview $interview): self
    {
        if (!$this->interviews->contains($interview)) {
            $this->interviews[] = $interview;
            $interview->addDepartment($this);
        }

        return $this;
    }

    public function removeInterview(Interview $interview): self
    {
        if ($this->interviews->removeElement($interview)) {
            $interview->removeDepartment($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, TechnicalTest>
     */
    public function getTechnicalTests(): Collection
    {
        return $this->technicalTests;
    }

    public function addTechnicalTest(TechnicalTest $technicalTest): self
    {
        if (!$this->technicalTests->contains($technicalTest)) {
            $this->technicalTests[] = $technicalTest;
            $technicalTest->setDepartment($this);
        }

        return $this;
    }

    public function removeTechnicalTest(TechnicalTest $technicalTest): self
    {
        if ($this->technicalTests->removeElement($technicalTest)) {
            // set the owning side to null (unless already changed)
            if ($technicalTest->getDepartment() === $this) {
                $technicalTest->setDepartment(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Result>
     */
    public function getResults(): Collection
    {
        return $this->results;
    }

    public function addResult(Result $result): self
    {
        if (!$this->results->contains($result)) {
            $this->results[] = $result;
            $result->setDepartmentChosen($this);
        }

        return $this;
    }

    public function removeResult(Result $result): self
    {
        if ($this->results->removeElement($result)) {
            // set the owning side to null (unless already changed)
            if ($result->getDepartmentChosen() === $this) {
                $result->setDepartmentChosen(null);
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

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->addDepartment($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            $user->removeDepartment($this);
        }

        return $this;
    }
}
