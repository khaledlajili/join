<?php

namespace App\Entity;

use App\Repository\InterviewRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: InterviewRepository::class)]
class Interview
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    #[Assert\Range(
        min: '+2 days'
    )]
    private $start;

    #[ORM\Column(type: 'datetime')]
    #[Assert\Expression('this.getEnd() > this.getStart()', message: 'End date must be greater than start date')]
    private $_end;

    #[ORM\Column(type: 'string', length: 255)]
    private $place;

    #[ORM\ManyToMany(targetEntity: Department::class, inversedBy: 'interviews')]
    private $departments;

    #[ORM\OneToOne(inversedBy: 'interview', targetEntity: User::class)]
    private $candidate;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'interviews')]
    private $recruiters;

    #[ORM\OneToMany(mappedBy: 'interview', targetEntity: InterviewsAvailability::class,cascade: ['persist', 'remove'])]
    private $interviewsAvailabilities;

    #[ORM\ManyToOne(targetEntity: RecruitmentSession::class, inversedBy: 'interviews')]
    #[ORM\JoinColumn(nullable: false)]
    private $recruitmentSession;

    #[ORM\OneToMany(mappedBy: 'interview', targetEntity: Demande::class)]
    private Collection $demandes;

    #[ORM\Column(nullable: true)]
    private ?bool $emailSent = null;

    public function __construct()
    {
        $this->departments = new ArrayCollection();
        $this->recruiters = new ArrayCollection();
        $this->interviewsAvailabilities = new ArrayCollection();
        $this->demandes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->_end;
    }

    public function setEnd(\DateTimeInterface $_end): self
    {
        $this->_end = $_end;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(string $place): self
    {
        $this->place = $place;

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

    public function getCandidate(): ?User
    {
        return $this->candidate;
    }

    public function setCandidate(?User $candidate): self
    {
        $this->candidate = $candidate;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getRecruiters(): Collection
    {
        return $this->recruiters;
    }

    public function addRecruiter(User $recruiter): self
    {
        if (!$this->recruiters->contains($recruiter)) {
            $this->recruiters[] = $recruiter;
        }

        return $this;
    }

    public function removeRecruiter(User $recruiter): self
    {
        $this->recruiters->removeElement($recruiter);

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
            $interviewsAvailability->setInterview($this);
        }

        return $this;
    }

    public function removeInterviewsAvailability(InterviewsAvailability $interviewsAvailability): self
    {
        if ($this->interviewsAvailabilities->removeElement($interviewsAvailability)) {
            // set the owning side to null (unless already changed)
            if ($interviewsAvailability->getInterview() === $this) {
                $interviewsAvailability->setInterview(null);
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
            $demande->setInterview($this);
        }

        return $this;
    }

    public function removeDemande(Demande $demande): self
    {
        if ($this->demandes->removeElement($demande)) {
            // set the owning side to null (unless already changed)
            if ($demande->getInterview() === $this) {
                $demande->setInterview(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->getId() . " : " . $this->getStart()->format("Y/m/d H:m");
    }

    public function isEmailSent(): ?bool
    {
        return $this->emailSent;
    }

    public function setEmailSent(?bool $emailSent): self
    {
        $this->emailSent = $emailSent;

        return $this;
    }
}
