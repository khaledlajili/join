<?php

namespace App\Entity;

use App\Repository\ResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResultRepository::class)]
class Result
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $preRegistration;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $collectiveInterviews;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $technicalTestResult;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $interview;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $trialPeriod;

    #[ORM\OneToOne(inversedBy: 'result', targetEntity: User::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private $_user;

    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'results')]
    private $departmentChosen;

    #[ORM\Column(nullable: true)]
    private ?bool $preRegistrationEmail = null;

    #[ORM\Column(nullable: true)]
    private ?bool $collectiveInterviewsEmail = null;

    #[ORM\Column(nullable: true)]
    private ?bool $technicalTestEmail = null;

    #[ORM\Column(nullable: true)]
    private ?bool $interviewEmail = null;

    #[ORM\Column(nullable: true)]
    private ?bool $trialPeriodEmail = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPreRegistration(): ?bool
    {
        return $this->preRegistration;
    }

    public function setPreRegistration(?bool $preRegistration): self
    {
        $this->preRegistration = $preRegistration;

        return $this;
    }

    public function getCollectiveInterviews(): ?bool
    {
        return $this->collectiveInterviews;
    }

    public function setCollectiveInterviews(?bool $collectiveInterviews): self
    {
        $this->collectiveInterviews = $collectiveInterviews;

        return $this;
    }

    public function getTechnicalTestResult(): ?bool
    {
        return $this->technicalTestResult;
    }

    public function setTechnicalTestResult(?bool $technicalTestResult): self
    {
        $this->technicalTestResult = $technicalTestResult;

        return $this;
    }

    public function getInterview(): ?bool
    {
        return $this->interview;
    }

    public function setInterview(?bool $interview): self
    {
        $this->interview = $interview;

        return $this;
    }

    public function getTrialPeriod(): ?bool
    {
        return $this->trialPeriod;
    }

    public function setTrialPeriod(?bool $trialPeriod): self
    {
        $this->trialPeriod = $trialPeriod;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->_user;
    }

    public function setUser(User $_user): self
    {
        $this->_user = $_user;

        return $this;
    }

    public function getDepartmentChosen(): ?Department
    {
        return $this->departmentChosen;
    }

    public function setDepartmentChosen(?Department $departmentChosen): self
    {
        $this->departmentChosen = $departmentChosen;

        return $this;
    }

    public function isPreRegistrationEmail(): ?bool
    {
        return $this->preRegistrationEmail;
    }

    public function setPreRegistrationEmail(bool $preRegistrationEmail): self
    {
        $this->preRegistrationEmail = $preRegistrationEmail;

        return $this;
    }

    public function isCollectiveInterviewsEmail(): ?bool
    {
        return $this->collectiveInterviewsEmail;
    }

    public function setCollectiveInterviewsEmail(bool $collectiveInterviewsEmail): self
    {
        $this->collectiveInterviewsEmail = $collectiveInterviewsEmail;

        return $this;
    }

    public function isTechnicalTestEmail(): ?bool
    {
        return $this->technicalTestEmail;
    }

    public function setTechnicalTestEmail(?bool $technicalTestEmail): self
    {
        $this->technicalTestEmail = $technicalTestEmail;

        return $this;
    }

    public function isInterviewEmail(): ?bool
    {
        return $this->interviewEmail;
    }

    public function setInterviewEmail(?bool $interviewEmail): self
    {
        $this->interviewEmail = $interviewEmail;

        return $this;
    }

    public function isTrialPeriodEmail(): ?bool
    {
        return $this->trialPeriodEmail;
    }

    public function setTrialPeriodEmail(?bool $trialPeriodEmail): self
    {
        $this->trialPeriodEmail = $trialPeriodEmail;

        return $this;
    }
}
