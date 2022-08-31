<?php

namespace App\Entity;

use App\Repository\RecruitmentSessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: RecruitmentSessionRepository::class)]
#[UniqueEntity('name')]
class RecruitmentSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private $name;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $start;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    #[Assert\Expression('this.getRegistrationEnd() > this.getStart()', message: 'Registration deadline must be greater than recruitment recruitment_session start date')]
    private $registrationEnd;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    #[Assert\Expression('this.getPreRegistrationSelectionEnd() > this.getRegistrationEnd()', message: 'Pre-registration selection deadline must be greater than registration deadline')]
    private $preRegistrationSelectionEnd;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\Expression('this.getCollectiveInterviewsEnd() > this.getPreRegistrationSelectionEnd() or !this.getCollectiveInterview()', message: 'Collective Interviews deadline must be greater than pre-registration selection deadline')]
    private $collectiveInterviewsEnd;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\Expression('this.getCollectiveInterviewsSelectionEnd() > this.getCollectiveInterviewsEnd() or !this.getCollectiveInterview()', message: 'Collective Interviews selection deadline must be greater than collective Interviews deadline')]
    private $collectiveInterviewsSelectionEnd;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\Expression('this.getTechnicalTestEnd() > this.getCollectiveInterviewsSelectionEnd() or !this.getTechnicalTest()', message: 'Technical test deadline must be greater than collective Interviews selection deadline')]
    private $technicalTestEnd;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\Expression('this.getTechnicalTestSelectionEnd() > this.getTechnicalTestEnd() or !this.getTechnicalTest()', message: 'Technical test selection deadline must be greater than technical test deadline')]
    private $technicalTestSelectionEnd;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    #[Assert\Expression('this.getInterviewsScheduleEnd() > this.getTechnicalTestSelectionEnd() or !this.getTechnicalTest()', message: 'Creating individual interviews schedule deadline must be greater than technical test selection deadline')]
    #[Assert\Expression('this.getInterviewsScheduleEnd() > this.getCollectiveInterviewsSelectionEnd() or !this.getCollectiveInterview()', message: 'Creating individual interviews schedule deadline must be greater than collective Interviews selection deadline')]
    #[Assert\Expression('this.getInterviewsScheduleEnd() > this.getPreRegistrationSelectionEnd()', message: 'Creating individual interviews schedule deadline must be greater than pre-registration selection deadline')]
    private $interviewsScheduleEnd;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    #[Assert\Expression('this.getRecruitersAvailabilityEnd() > this.getInterviewsScheduleEnd()', message: 'Getting recruiters availability deadline must be greater than creating individual interviews schedule deadline')]
    private $recruitersAvailabilityEnd;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\Expression('this.getForBookingInterviewsScheduleEnd() > this.getRecruitersAvailabilityEnd() or !this.getBookingForInterview()', message: 'creating individual interviews schedule for booking deadline must be greater than getting recruiters availability deadline')]
    private $forBookingInterviewsScheduleEnd;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    #[Assert\Expression('this.getValidateInterviewsScheduleEnd() > this.getForBookingInterviewsScheduleEnd() or !this.getBookingForInterview()', message: 'Creating validate individual interviews schedule deadline must be greater than creating individual interviews schedule for booking deadline')]
    #[Assert\Expression('this.getValidateInterviewsScheduleEnd() > this.getRecruitersAvailabilityEnd()', message: 'Creating validate individual interviews schedule deadline must be greater than getting recruiters availability deadline')]
    private $validateInterviewsScheduleEnd;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    private $interviewsStart;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    #[Assert\Expression('this.getInterviewsEnd() > this.getInterviewsStart()', message: 'Individual interviews end date must be greater than individual interviews start date')]
    #[Assert\Expression('this.getInterviewsEnd() > this.getValidateInterviewsScheduleEnd()', message: 'Individual interviews End date must be greater than creating validate individual interviews schedule deadline')]
    private $interviewsEnd;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    #[Assert\Expression('this.getInterviewsSelectionEnd() > this.getInterviewsEnd()', message: 'Individual interviews selection deadline must be greater than individual interviews end date')]
    private $interviewsSelectionEnd;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\Expression('this.getTrialPeriodSelectionEnd() > this.getInterviewsSelectionEnd() or !this.getTrialPeriod()', message: 'Trial period selection deadline must be greater than individual interviews selection deadline')]
    private $trialPeriodSelectionEnd;

    #[ORM\Column(type: 'boolean')]
    private $collectiveInterview;

    #[ORM\Column(type: 'boolean')]
    private $technicalTest;

    #[ORM\Column(type: 'boolean')]
    private $bookingForInterview;

    #[ORM\Column(type: 'boolean')]
    private $trialPeriod;

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\GreaterThanOrEqual(1)]
    private $depChoiceMaxNbre;

    #[ORM\OneToMany(mappedBy: 'recruitmentSession', targetEntity: User::class)]
    private $users;

    #[ORM\OneToMany(mappedBy: 'recruitmentSession', targetEntity: CollectiveInterview::class)]
    private $collectiveInterviews;

    #[ORM\OneToMany(mappedBy: 'recruitmentSession', targetEntity: CollectiveInterviewsEvaluationCriterion::class)]
    private $collectiveInterviewsEvaluationCriteria;

    #[ORM\OneToMany(mappedBy: 'recruitmentSession', targetEntity: InterviewsEvaluationSheetPart::class)]
    private $interviewsEvaluationSheetParts;

    #[ORM\OneToMany(mappedBy: 'recruitmentSession', targetEntity: Interview::class)]
    private $interviews;

    #[ORM\Column(type: 'boolean')]
    private $current=true;

    #[ORM\OneToMany(mappedBy: 'recruitmentSession', targetEntity: PreRegistrationFormField::class,  orphanRemoval: true)]
    private $preRegistrationFormFields;

    #[ORM\OneToMany(mappedBy: 'recruitmentSession', targetEntity: TechnicalTest::class)]
    private $technicalTests;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->collectiveInterviews = new ArrayCollection();
        $this->collectiveInterviewsEvaluationCriteria = new ArrayCollection();
        $this->interviewsEvaluationSheetParts = new ArrayCollection();
        $this->interviews = new ArrayCollection();
        $this->preRegistrationFormFields = new ArrayCollection();
        $this->technicalTests = new ArrayCollection();
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

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getRegistrationEnd(): ?\DateTimeInterface
    {
        return $this->registrationEnd;
    }

    public function setRegistrationEnd(\DateTimeInterface $registrationEnd): self
    {
        $this->registrationEnd = $registrationEnd;

        return $this;
    }

    public function getPreRegistrationSelectionEnd(): ?\DateTimeInterface
    {
        return $this->preRegistrationSelectionEnd;
    }

    public function setPreRegistrationSelectionEnd(\DateTimeInterface $preRegistrationSelectionEnd): self
    {
        $this->preRegistrationSelectionEnd = $preRegistrationSelectionEnd;

        return $this;
    }

    public function getCollectiveInterviewsEnd(): ?\DateTimeInterface
    {
        return $this->collectiveInterviewsEnd;
    }

    public function setCollectiveInterviewsEnd(?\DateTimeInterface $collectiveInterviewsEnd): self
    {
        $this->collectiveInterviewsEnd = $collectiveInterviewsEnd;

        return $this;
    }

    public function getCollectiveInterviewsSelectionEnd(): ?\DateTimeInterface
    {
        return $this->collectiveInterviewsSelectionEnd;
    }

    public function setCollectiveInterviewsSelectionEnd(?\DateTimeInterface $collectiveInterviewsSelectionEnd): self
    {
        $this->collectiveInterviewsSelectionEnd = $collectiveInterviewsSelectionEnd;

        return $this;
    }

    public function getTechnicalTestEnd(): ?\DateTimeInterface
    {
        return $this->technicalTestEnd;
    }

    public function setTechnicalTestEnd(?\DateTimeInterface $technicalTestEnd): self
    {
        $this->technicalTestEnd = $technicalTestEnd;

        return $this;
    }

    public function getTechnicalTestSelectionEnd(): ?\DateTimeInterface
    {
        return $this->technicalTestSelectionEnd;
    }

    public function setTechnicalTestSelectionEnd(?\DateTimeInterface $technicalTestSelectionEnd): self
    {
        $this->technicalTestSelectionEnd = $technicalTestSelectionEnd;

        return $this;
    }

    public function getInterviewsScheduleEnd(): ?\DateTimeInterface
    {
        return $this->interviewsScheduleEnd;
    }

    public function setInterviewsScheduleEnd(\DateTimeInterface $interviewsScheduleEnd): self
    {
        $this->interviewsScheduleEnd = $interviewsScheduleEnd;

        return $this;
    }

    public function getRecruitersAvailabilityEnd(): ?\DateTimeInterface
    {
        return $this->recruitersAvailabilityEnd;
    }

    public function setRecruitersAvailabilityEnd(\DateTimeInterface $recruitersAvailabilityEnd): self
    {
        $this->recruitersAvailabilityEnd = $recruitersAvailabilityEnd;

        return $this;
    }

    public function getForBookingInterviewsScheduleEnd(): ?\DateTimeInterface
    {
        return $this->forBookingInterviewsScheduleEnd;
    }

    public function setForBookingInterviewsScheduleEnd(?\DateTimeInterface $forBookingInterviewsScheduleEnd): self
    {
        $this->forBookingInterviewsScheduleEnd = $forBookingInterviewsScheduleEnd;

        return $this;
    }

    public function getValidateInterviewsScheduleEnd(): ?\DateTimeInterface
    {
        return $this->validateInterviewsScheduleEnd;
    }

    public function setValidateInterviewsScheduleEnd(\DateTimeInterface $validateInterviewsScheduleEnd): self
    {
        $this->validateInterviewsScheduleEnd = $validateInterviewsScheduleEnd;

        return $this;
    }

    public function getInterviewsStart(): ?\DateTimeInterface
    {
        return $this->interviewsStart;
    }

    public function setInterviewsStart(\DateTimeInterface $interviewsStart): self
    {
        $this->interviewsStart = $interviewsStart;

        return $this;
    }

    public function getInterviewsEnd(): ?\DateTimeInterface
    {
        return $this->interviewsEnd;
    }

    public function setInterviewsEnd(\DateTimeInterface $interviewsEnd): self
    {
        $this->interviewsEnd = $interviewsEnd;

        return $this;
    }

    public function getInterviewsSelectionEnd(): ?\DateTimeInterface
    {
        return $this->interviewsSelectionEnd;
    }

    public function setInterviewsSelectionEnd(\DateTimeInterface $interviewsSelectionEnd): self
    {
        $this->interviewsSelectionEnd = $interviewsSelectionEnd;

        return $this;
    }

    public function getTrialPeriodSelectionEnd(): ?\DateTimeInterface
    {
        return $this->trialPeriodSelectionEnd;
    }

    public function setTrialPeriodSelectionEnd(?\DateTimeInterface $trialPeriodSelectionEnd): self
    {
        $this->trialPeriodSelectionEnd = $trialPeriodSelectionEnd;

        return $this;
    }

    public function getCollectiveInterview(): ?bool
    {
        return $this->collectiveInterview;
    }

    public function setCollectiveInterview(bool $collectiveInterview): self
    {
        $this->collectiveInterview = $collectiveInterview;

        return $this;
    }

    public function getTechnicalTest(): ?bool
    {
        return $this->technicalTest;
    }

    public function setTechnicalTest(bool $technicalTest): self
    {
        $this->technicalTest = $technicalTest;

        return $this;
    }

    public function getBookingForInterview(): ?bool
    {
        return $this->bookingForInterview;
    }

    public function setBookingForInterview(bool $bookingForInterview): self
    {
        $this->bookingForInterview = $bookingForInterview;

        return $this;
    }

    public function getTrialPeriod(): ?bool
    {
        return $this->trialPeriod;
    }

    public function setTrialPeriod(bool $trialPeriod): self
    {
        $this->trialPeriod = $trialPeriod;

        return $this;
    }

    public function getDepChoiceMaxNbre(): ?int
    {
        return $this->depChoiceMaxNbre;
    }

    public function setDepChoiceMaxNbre(?int $depChoiceMaxNbre): self
    {
        $this->depChoiceMaxNbre = $depChoiceMaxNbre;

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
            $this->users[] = $user;
            $user->setRecruitmentSession($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getRecruitmentSession() === $this) {
                $user->setRecruitmentSession(null);
            }
        }

        return $this;
    }


    /**
     * @return Collection<int, CollectiveInterview>
     */
    public function getCollectiveInterviews(): Collection
    {
        return $this->collectiveInterviews;
    }

    public function addCollectiveInterview(CollectiveInterview $collectiveInterview): self
    {
        if (!$this->collectiveInterviews->contains($collectiveInterview)) {
            $this->collectiveInterviews[] = $collectiveInterview;
            $collectiveInterview->setRecruitmentSession($this);
        }

        return $this;
    }

    public function removeCollectiveInterview(CollectiveInterview $collectiveInterview): self
    {
        if ($this->collectiveInterviews->removeElement($collectiveInterview)) {
            // set the owning side to null (unless already changed)
            if ($collectiveInterview->getRecruitmentSession() === $this) {
                $collectiveInterview->setRecruitmentSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CollectiveInterviewsEvaluationCriterion>
     */
    public function getCollectiveInterviewsEvaluationCriteria(): Collection
    {
        return $this->collectiveInterviewsEvaluationCriteria;
    }

    public function addCollectiveInterviewsEvaluationCriterion(CollectiveInterviewsEvaluationCriterion $collectiveInterviewsEvaluationCriterion): self
    {
        if (!$this->collectiveInterviewsEvaluationCriteria->contains($collectiveInterviewsEvaluationCriterion)) {
            $this->collectiveInterviewsEvaluationCriteria[] = $collectiveInterviewsEvaluationCriterion;
            $collectiveInterviewsEvaluationCriterion->setRecruitmentSession($this);
        }

        return $this;
    }

    public function removeCollectiveInterviewsEvaluationCriterion(CollectiveInterviewsEvaluationCriterion $collectiveInterviewsEvaluationCriterion): self
    {
        if ($this->collectiveInterviewsEvaluationCriteria->removeElement($collectiveInterviewsEvaluationCriterion)) {
            // set the owning side to null (unless already changed)
            if ($collectiveInterviewsEvaluationCriterion->getRecruitmentSession() === $this) {
                $collectiveInterviewsEvaluationCriterion->setRecruitmentSession(null);
            }
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
            $interviewsEvaluationSheetPart->setRecruitmentSession($this);
        }

        return $this;
    }

    public function removeInterviewsEvaluationSheetPart(InterviewsEvaluationSheetPart $interviewsEvaluationSheetPart): self
    {
        if ($this->interviewsEvaluationSheetParts->removeElement($interviewsEvaluationSheetPart)) {
            // set the owning side to null (unless already changed)
            if ($interviewsEvaluationSheetPart->getRecruitmentSession() === $this) {
                $interviewsEvaluationSheetPart->setRecruitmentSession(null);
            }
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
            $interview->setRecruitmentSession($this);
        }

        return $this;
    }

    public function removeInterview(Interview $interview): self
    {
        if ($this->interviews->removeElement($interview)) {
            // set the owning side to null (unless already changed)
            if ($interview->getRecruitmentSession() === $this) {
                $interview->setRecruitmentSession(null);
            }
        }

        return $this;
    }

    public function getCurrent(): ?bool
    {
        return $this->current;
    }

    public function setCurrent(bool $current): self
    {
        $this->current = $current;

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
            $preRegistrationFormField->setRecruitmentSession($this);
        }

        return $this;
    }

    public function removePreRegistrationFormField(PreRegistrationFormField $preRegistrationFormField): self
    {
        if ($this->preRegistrationFormFields->removeElement($preRegistrationFormField)) {
            // set the owning side to null (unless already changed)
            if ($preRegistrationFormField->getRecruitmentSession() === $this) {
                $preRegistrationFormField->setRecruitmentSession(null);
            }
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
            $technicalTest->setRecruitmentSession($this);
        }

        return $this;
    }

    public function removeTechnicalTest(TechnicalTest $technicalTest): self
    {
        if ($this->technicalTests->removeElement($technicalTest)) {
            // set the owning side to null (unless already changed)
            if ($technicalTest->getRecruitmentSession() === $this) {
                $technicalTest->setRecruitmentSession(null);
            }
        }

        return $this;
    }
}
