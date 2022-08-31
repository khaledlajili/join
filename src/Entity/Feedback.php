<?php

namespace App\Entity;

use App\Repository\FeedbackRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FeedbackRepository::class)]
class Feedback
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float', nullable: true)]
    private $preRegistrationRating;

    #[ORM\Column(type: 'text', nullable: true)]
    private $preRegistrationRemark;

    #[ORM\Column(type: 'float', nullable: true)]
    private $technicalTestRating;

    #[ORM\Column(type: 'text', nullable: true)]
    private $technicalTestRemark;

    #[ORM\Column(type: 'float', nullable: true)]
    private $collectiveInterviewsRating;

    #[ORM\Column(type: 'text', nullable: true)]
    private $collectiveInterviewsRemark;

    #[ORM\Column(type: 'float', nullable: true)]
    private $individualInterviewsRating;

    #[ORM\Column(type: 'text', nullable: true)]
    private $individualInterviewsRemark;

    #[ORM\Column(type: 'float', nullable: true)]
    private $trialPeriodRating;

    #[ORM\Column(type: 'text', nullable: true)]
    private $trialPeriodRemark;

    #[ORM\OneToOne(inversedBy: 'feedback', cascade: ['persist', 'remove'])]
    private ?User $user;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPreRegistrationRating(): ?float
    {
        return $this->preRegistrationRating;
    }

    public function setPreRegistrationRating(?float $preRegistrationRating): self
    {
        $this->preRegistrationRating = $preRegistrationRating;

        return $this;
    }

    public function getPreRegistrationRemark(): ?string
    {
        return $this->preRegistrationRemark;
    }

    public function setPreRegistrationRemark(?string $preRegistrationRemark): self
    {
        $this->preRegistrationRemark = $preRegistrationRemark;

        return $this;
    }

    public function getTechnicalTestRating(): ?float
    {
        return $this->technicalTestRating;
    }

    public function setTechnicalTestRating(?float $technicalTestRating): self
    {
        $this->technicalTestRating = $technicalTestRating;

        return $this;
    }

    public function getTechnicalTestRemark(): ?string
    {
        return $this->technicalTestRemark;
    }

    public function setTechnicalTestRemark(?string $technicalTestRemark): self
    {
        $this->technicalTestRemark = $technicalTestRemark;

        return $this;
    }

    public function getCollectiveInterviewsRating(): ?float
    {
        return $this->collectiveInterviewsRating;
    }

    public function setCollectiveInterviewsRating(?float $collectiveInterviewsRating): self
    {
        $this->collectiveInterviewsRating = $collectiveInterviewsRating;

        return $this;
    }

    public function getCollectiveInterviewsRemark(): ?string
    {
        return $this->collectiveInterviewsRemark;
    }

    public function setCollectiveInterviewsRemark(?string $collectiveInterviewsRemark): self
    {
        $this->collectiveInterviewsRemark = $collectiveInterviewsRemark;

        return $this;
    }

    public function getIndividualInterviewsRating(): ?float
    {
        return $this->individualInterviewsRating;
    }

    public function setIndividualInterviewsRating(?float $individualInterviewsRating): self
    {
        $this->individualInterviewsRating = $individualInterviewsRating;

        return $this;
    }

    public function getIndividualInterviewsRemark(): ?string
    {
        return $this->individualInterviewsRemark;
    }

    public function setIndividualInterviewsRemark(?string $individualInterviewsRemark): self
    {
        $this->individualInterviewsRemark = $individualInterviewsRemark;

        return $this;
    }

    public function getTrialPeriodRating(): ?float
    {
        return $this->trialPeriodRating;
    }

    public function setTrialPeriodRating(?float $trialPeriodRating): self
    {
        $this->trialPeriodRating = $trialPeriodRating;

        return $this;
    }

    public function getTrialPeriodRemark(): ?string
    {
        return $this->trialPeriodRemark;
    }

    public function setTrialPeriodRemark(?string $trialPeriodRemark): self
    {
        $this->trialPeriodRemark = $trialPeriodRemark;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
