<?php

namespace App\Entity;

use App\Repository\TechnicalTestRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: TechnicalTestRepository::class)]
#[UniqueEntity(
    fields: ['recruitmentSession', 'department'],
)]
class TechnicalTest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $pdf;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'technicalTests')]
    #[ORM\JoinColumn(nullable: false)]
    private $department;

    #[ORM\ManyToOne(targetEntity: RecruitmentSession::class, inversedBy: 'technicalTests')]
    #[ORM\JoinColumn(nullable: false)]
    private $recruitmentSession;

    #[ORM\OneToMany(mappedBy: 'technicalTest', targetEntity: TechnicalTestResult::class)]
    private Collection $technicalTestResults;

    public function __construct()
    {
        $this->technicalTestResults = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPdf(): ?string
    {
        return $this->pdf;
    }

    public function setPdf(string $pdf): self
    {
        $this->pdf = $pdf;

        return $this;
    }

    public function getDepartment(): ?Department
    {
        return $this->department;
    }

    public function setDepartment(?Department $department): self
    {
        $this->department = $department;

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
     * @return Collection<int, TechnicalTestResult>
     */
    public function getTechnicalTestResults(): Collection
    {
        return $this->technicalTestResults;
    }

    public function addTechnicalTestResult(TechnicalTestResult $technicalTestResult): self
    {
        if (!$this->technicalTestResults->contains($technicalTestResult)) {
            $this->technicalTestResults->add($technicalTestResult);
            $technicalTestResult->setTechnicalTest($this);
        }

        return $this;
    }

    public function removeTechnicalTestResult(TechnicalTestResult $technicalTestResult): self
    {
        if ($this->technicalTestResults->removeElement($technicalTestResult)) {
            // set the owning side to null (unless already changed)
            if ($technicalTestResult->getTechnicalTest() === $this) {
                $technicalTestResult->setTechnicalTest(null);
            }
        }

        return $this;
    }
}
