<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\service\UploaderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[UniqueEntity(fields: ['phone'], message: 'There is already an account with this phone number')]
class User implements UserInterface, PasswordAuthenticatedUserInterface, EquatableInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email(message: "please enter a valid Email.")]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = ['ROLE_CANDIDATE'];

    #[ORM\Column(type: 'string')]
    private $password;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private $fName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private $lName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[AssertPhoneNumber]
    private $phone;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private $birthday;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private $adress;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $img;

    #[ORM\ManyToOne(targetEntity: StudyLevel::class, inversedBy: 'users')]
    private $studyLevel;

    #[ORM\OneToOne(targetEntity: TechnicalTestResult::class, cascade: ['persist', 'remove'])]
    private $technicalTestResult;

    #[ORM\OneToOne(mappedBy: '_user', targetEntity: Result::class, cascade: ['persist', 'remove'])]
    private $result;

    #[ORM\ManyToOne(targetEntity: RecruitmentSession::class, inversedBy: 'users')]
    private $recruitmentSession;

    #[ORM\OneToMany(mappedBy: '_user', targetEntity: Response::class)]
    private $responses;

    #[ORM\ManyToMany(targetEntity: CollectiveInterview::class, mappedBy: 'recruiters')]
    private $collectiveInterviews;

    #[ORM\ManyToOne(targetEntity: CollectiveInterview::class, inversedBy: 'users')]
    #[ORM\JoinColumn(name: 'collective_interview_id', referencedColumnName: 'id', onDelete: 'SET NULL')]
    private $collectiveInterview;

    #[ORM\OneToMany(mappedBy: 'candidate', targetEntity: CollectiveInterviewsCriterionResult::class)]
    private $collectiveInterviewsCriterionResults;

    #[ORM\OneToMany(mappedBy: 'recruiter', targetEntity: CollectiveInterviewsCriterionResult::class)]
    private $collectiveInterviewsCriterionResultsRecuiter;


    #[ORM\OneToMany(mappedBy: 'recruiter', targetEntity: CollectiveInterviewsResult::class)]
    private $collectiveInterviewsResults;

    #[ORM\OneToOne(mappedBy: 'candidate', targetEntity: Interview::class, cascade: ['persist', 'remove'])]
    private $interview;

    #[ORM\ManyToMany(targetEntity: Interview::class, mappedBy: 'recruiters')]
    private $interviews;

    #[ORM\OneToMany(mappedBy: 'recruiter', targetEntity: InterviewsAvailability::class)]
    private $interviewsAvailabilities;

    #[ORM\OneToMany(mappedBy: 'candidate', targetEntity: InterviewCriterionResult::class)]
    private $interviewCriterionResults;

    #[ORM\OneToMany(mappedBy: 'recruiter', targetEntity: InterviewCriterionResult::class)]
    private $interviewCriterionResultsRecruiter;

    #[ORM\OneToMany(mappedBy: 'recruiters', targetEntity: InterviewsResult::class)]
    private $interviewsResults;

    #[ORM\OneToMany(mappedBy: 'candidate', targetEntity: InterviewsResult::class)]
    private $inteviewsResultsAsCandidate;

    #[ORM\OneToMany(mappedBy: 'candidate', targetEntity: CollectiveInterviewsResult::class)]
    private $collectiveInterviewsResultsAsCandidate;

    #[ORM\ManyToMany(targetEntity: Department::class, inversedBy: 'users')]
    private Collection $departments;

    #[ORM\OneToMany(mappedBy: 'sender', targetEntity: Demande::class)]
    private Collection $demandes;

    #[ORM\Column]
    private ?bool $isVerified = null;

    #[ORM\OneToMany(mappedBy: 'candidate', targetEntity: TechnicalTestResult::class)]
    private Collection $technicalTestResults;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    private ?Feedback $feedback = null;


    public function __construct()
    {
        $this->responses = new ArrayCollection();
        $this->collectiveInterviews = new ArrayCollection();
        $this->collectiveInterviewsCriterionResults = new ArrayCollection();
        $this->collectiveInterviewsResults = new ArrayCollection();
        $this->interviews = new ArrayCollection();
        $this->interviewsAvailabilities = new ArrayCollection();
        $this->interviewCriterionResults = new ArrayCollection();
        $this->interviewsResults = new ArrayCollection();
        $this->departments = new ArrayCollection();
        $this->inteviewsResultsAsCandidate = new ArrayCollection();
        $this->collectiveInterviewsResultsAsCandidate = new ArrayCollection();
        $this->demandes = new ArrayCollection();
        $this->technicalTestResults = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string)$this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFName(): ?string
    {
        return $this->fName;
    }

    public function setFName(string $fName): self
    {
        $this->fName = $fName;

        return $this;
    }

    public function getLName(): ?string
    {
        return $this->lName;
    }

    public function setLName(string $lName): self
    {
        $this->lName = $lName;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getImg(): ?string
    {
        return $this->img;
    }

    public function setImg(?string $img): self
    {
        $this->img = $img;

        return $this;
    }

    public function getStudyLevel(): ?StudyLevel
    {
        return $this->studyLevel;
    }

    public function setStudyLevel(?StudyLevel $studyLevel): self
    {
        $this->studyLevel = $studyLevel;

        return $this;
    }

    public function getTechnicalTestResult(): ?TechnicalTestResult
    {
        return $this->technicalTestResult;
    }

    public function setTechnicalTestResult(?TechnicalTestResult $technicalTestResult): self
    {
        $this->technicalTestResult = $technicalTestResult;

        return $this;
    }

    public function getResult(): ?Result
    {
        return $this->result;
    }

    public function setResult(Result $result): self
    {
        // set the owning side of the relation if necessary
        if ($result->getUser() !== $this) {
            $result->setUser($this);
        }

        $this->result = $result;

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
     * @return Collection<int, Response>
     */
    public function getResponses(): Collection
    {
        return $this->responses;
    }

    public function addResponse(Response $response): self
    {
        if (!$this->responses->contains($response)) {
            $this->responses[] = $response;
            $response->setUser($this);
        }

        return $this;
    }

    public function removeResponse(Response $response): self
    {
        if ($this->responses->removeElement($response)) {
            // set the owning side to null (unless already changed)
            if ($response->getUser() === $this) {
                $response->setUser(null);
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
            $collectiveInterview->addRecruiter($this);
        }

        return $this;
    }

    public function removeCollectiveInterview(CollectiveInterview $collectiveInterview): self
    {
        if ($this->collectiveInterviews->removeElement($collectiveInterview)) {
            $collectiveInterview->removeRecruiter($this);
        }

        return $this;
    }

    public function getCollectiveInterview(): ?CollectiveInterview
    {
        return $this->collectiveInterview;
    }

    public function setCollectiveInterview(?CollectiveInterview $collectiveInterview): self
    {
        $this->collectiveInterview = $collectiveInterview;

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
            $collectiveInterviewsCriterionResult->setCandidate($this);
        }

        return $this;
    }

    public function removeCollectiveInterviewsCriterionResult(CollectiveInterviewsCriterionResult $collectiveInterviewsCriterionResult): self
    {
        if ($this->collectiveInterviewsCriterionResults->removeElement($collectiveInterviewsCriterionResult)) {
            // set the owning side to null (unless already changed)
            if ($collectiveInterviewsCriterionResult->getCandidate() === $this) {
                $collectiveInterviewsCriterionResult->setCandidate(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCollectiveInterviewsCriterionResultsRecuiter()
    {
        return $this->collectiveInterviewsCriterionResultsRecuiter;
    }

    /**
     * @param mixed $collectiveInterviewsCriterionResultsRecuiter
     */
    public function setCollectiveInterviewsCriterionResultsRecuiter($collectiveInterviewsCriterionResultsRecuiter): void
    {
        $this->collectiveInterviewsCriterionResultsRecuiter = $collectiveInterviewsCriterionResultsRecuiter;
    }


    /**
     * @return Collection<int, CollectiveInterviewsResult>
     */
    public function getCollectiveInterviewsResults(): Collection
    {
        return $this->collectiveInterviewsResults;
    }

    public function addCollectiveInterviewsResult(CollectiveInterviewsResult $collectiveInterviewsResult): self
    {
        if (!$this->collectiveInterviewsResults->contains($collectiveInterviewsResult)) {
            $this->collectiveInterviewsResults[] = $collectiveInterviewsResult;
            $collectiveInterviewsResult->setRecruiter($this);
        }

        return $this;
    }

    public function removeCollectiveInterviewsResult(CollectiveInterviewsResult $collectiveInterviewsResult): self
    {
        if ($this->collectiveInterviewsResults->removeElement($collectiveInterviewsResult)) {
            // set the owning side to null (unless already changed)
            if ($collectiveInterviewsResult->getRecruiter() === $this) {
                $collectiveInterviewsResult->setRecruiter(null);
            }
        }

        return $this;
    }

    public function getInterview(): ?Interview
    {
        return $this->interview;
    }

    public function setInterview(?Interview $interview): self
    {
        // unset the owning side of the relation if necessary
        if ($interview === null && $this->interview !== null) {
            $this->interview->setCandidate(null);
        }

        // set the owning side of the relation if necessary
        if ($interview !== null && $interview->getCandidate() !== $this) {
            $interview->setCandidate($this);
        }

        $this->interview = $interview;

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
            $interview->addRecruiter($this);
        }

        return $this;
    }

    public function removeInterview(Interview $interview): self
    {
        if ($this->interviews->removeElement($interview)) {
            $interview->removeRecruiter($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, InterviewsAvailability>
     */
    public function getInterviewsAvailabilities(): Collection
    {
        return $this->interviewsAvailabilities;
    }

    public function addInterviewsAvailability(InterviewsAvailability $interviewsAvailability): self
    {
        if (!$this->interviewsAvailabilities->contains($interviewsAvailability)) {
            $this->interviewsAvailabilities[] = $interviewsAvailability;
            $interviewsAvailability->setRecruiter($this);
        }

        return $this;
    }

    public function removeInterviewsAvailability(InterviewsAvailability $interviewsAvailability): self
    {
        if ($this->interviewsAvailabilities->removeElement($interviewsAvailability)) {
            // set the owning side to null (unless already changed)
            if ($interviewsAvailability->getRecruiter() === $this) {
                $interviewsAvailability->setRecruiter(null);
            }
        }

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
            $interviewCriterionResult->setRecruiter($this);
        }

        return $this;
    }

    public function removeInterviewCriterionResult(InterviewCriterionResult $interviewCriterionResult): self
    {
        if ($this->interviewCriterionResults->removeElement($interviewCriterionResult)) {
            // set the owning side to null (unless already changed)
            if ($interviewCriterionResult->getRecruiter() === $this) {
                $interviewCriterionResult->setRecruiter(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInterviewCriterionResultsRecruiter()
    {
        return $this->interviewCriterionResultsRecruiter;
    }

    /**
     * @param mixed $interviewCriterionResultsRecruiter
     */
    public function setInterviewCriterionResultsRecruiter($interviewCriterionResultsRecruiter): void
    {
        $this->interviewCriterionResultsRecruiter = $interviewCriterionResultsRecruiter;
    }

    /**
     * @return Collection<int, InterviewsResult>
     */
    public function getInterviewsResults(): Collection
    {
        return $this->interviewsResults;
    }

    public function addInterviewsResult(InterviewsResult $interviewsResult): self
    {
        if (!$this->interviewsResults->contains($interviewsResult)) {
            $this->interviewsResults[] = $interviewsResult;
            $interviewsResult->setRecruiters($this);
        }

        return $this;
    }

    public function removeInterviewsResult(InterviewsResult $interviewsResult): self
    {
        if ($this->interviewsResults->removeElement($interviewsResult)) {
            // set the owning side to null (unless already changed)
            if ($interviewsResult->getRecruiters() === $this) {
                $interviewsResult->setRecruiters(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InterviewsResult>
     */
    public function getInteviewsResultsAsCandidate(): Collection
    {
        return $this->inteviewsResultsAsCandidate;
    }

    public function addInteviewsResultsAsCandidate(InterviewsResult $inteviewsResultsAsCandidate): self
    {
        if (!$this->inteviewsResultsAsCandidate->contains($inteviewsResultsAsCandidate)) {
            $this->inteviewsResultsAsCandidate[] = $inteviewsResultsAsCandidate;
            $inteviewsResultsAsCandidate->setCandidate($this);
        }

        return $this;
    }

    public function removeInteviewsResultsAsCandidate(InterviewsResult $inteviewsResultsAsCandidate): self
    {
        if ($this->inteviewsResultsAsCandidate->removeElement($inteviewsResultsAsCandidate)) {
            // set the owning side to null (unless already changed)
            if ($inteviewsResultsAsCandidate->getCandidate() === $this) {
                $inteviewsResultsAsCandidate->setCandidate(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CollectiveInterviewsResult>
     */
    public function getCollectiveInterviewsResultsAsCandidate(): Collection
    {
        return $this->collectiveInterviewsResultsAsCandidate;
    }

    public function addCollectiveInterviewsResultsAsCandidate(CollectiveInterviewsResult $collectiveInterviewsResultsAsCandidate): self
    {
        if (!$this->collectiveInterviewsResultsAsCandidate->contains($collectiveInterviewsResultsAsCandidate)) {
            $this->collectiveInterviewsResultsAsCandidate[] = $collectiveInterviewsResultsAsCandidate;
            $collectiveInterviewsResultsAsCandidate->setCandidate($this);
        }

        return $this;
    }

    public function removeCollectiveInterviewsResultsAsCandidate(CollectiveInterviewsResult $collectiveInterviewsResultsAsCandidate): self
    {
        if ($this->collectiveInterviewsResultsAsCandidate->removeElement($collectiveInterviewsResultsAsCandidate)) {
            // set the owning side to null (unless already changed)
            if ($collectiveInterviewsResultsAsCandidate->getCandidate() === $this) {
                $collectiveInterviewsResultsAsCandidate->setCandidate(null);
            }
        }

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
            $this->departments->add($department);
        }

        return $this;
    }

    public function removeDepartment(Department $department): self
    {
        $this->departments->removeElement($department);

        return $this;
    }


    /**
     * @return Collection<int, Demande>
     */
    public function getDemandes(): Collection
    {
        return $this->demandes;
    }

    public function addDemande(Demande $demande): self
    {
        if (!$this->demandes->contains($demande)) {
            $this->demandes->add($demande);
            $demande->setSender($this);
        }
        return $this;
    }

    public function isIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

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
            $technicalTestResult->setCandidate($this);
        }

        return $this;
    }

    public function removeDemande(Demande $demande): self
    {
        if ($this->demandes->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getSender() === $this) {
                $demande->setSender(null);
            }
        }
        return $this;
    }

    public function removeTechnicalTestResult(TechnicalTestResult $technicalTestResult): self
    {
        if ($this->technicalTestResults->removeElement($technicalTestResult)) {
            // set the owning side to null (unless already changed)
            if ($technicalTestResult->getCandidate() === $this) {
                $technicalTestResult->setCandidate(null);
            }
        }

        return $this;
    }

    public function getImgPath()
    {
        return 'uploads/' . UploaderHelper::PROFILE_IMGS . '/' . $this->getImg();
    }

    public function isRefused(): ?bool
    {
        $result = $this->getResult();
        if ($result && ( ($result->getPreRegistration() === false && $result->isPreRegistrationEmail()) || ($result->getCollectiveInterviews() === false && $result->isCollectiveInterviewsEmail()) || ($result->getTechnicalTestResult() === false && $result->isTechnicalTestEmail()) || ($result->getInterview() === false && $result->isInterviewEmail()) || ($result->getTrialPeriod() === false && $result->isTrialPeriodEmail())))
            return true;
        return false;
    }

    public function isEqualTo(UserInterface $user): bool
    {
        if ($this->isRefused())
            return false;
        return true;
    }

    public function getFeedback(): ?Feedback
    {
        return $this->feedback;
    }

    public function setFeedback(?Feedback $feedback): self
    {
        // unset the owning side of the relation if necessary
        if ($feedback === null && $this->feedback !== null) {
            $this->feedback->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($feedback !== null && $feedback->getUser() !== $this) {
            $feedback->setUser($this);
        }

        $this->feedback = $feedback;

        return $this;
    }
}
