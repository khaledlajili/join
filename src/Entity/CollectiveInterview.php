<?php

namespace App\Entity;

use App\Repository\CollectiveInterviewRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CollectiveInterviewRepository::class)]
class CollectiveInterview
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(message: "This field is required")]
    private $place;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: "This field is required")]
    #[Assert\Range(
        min: '+2 days'
    )]
    private $start;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank(message: "This field is required")]
    #[Assert\Expression('this.getEnd() >= this.getStart()', message: 'end must be greater than start')]
    private $end;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'collectiveInterviews')]
    #[Assert\NotBlank(message: "This field is required")]
    private $recruiters;

    #[ORM\OneToMany(mappedBy: 'collectiveInterview', targetEntity: User::class)]
    #[Assert\NotBlank(message: "This field is required")]
    private $users;

    #[ORM\ManyToOne(targetEntity: RecruitmentSession::class, inversedBy: 'collectiveInterviews')]
    #[ORM\JoinColumn(nullable: false)]
    private $recruitmentSession;

    #[ORM\Column(nullable: false)]
    private ?bool $EmailSent = false;


    public function __construct()
    {
        $this->recruiters = new ArrayCollection();
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

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
            $user->setCollectiveInterview($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCollectiveInterview() === $this) {
                $user->setCollectiveInterview(null);
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

    public function isEmailSent(): ?bool
    {
        return $this->EmailSent;
    }

    public function setEmailSent(?bool $EmailSent): self
    {
        $this->EmailSent = $EmailSent;

        return $this;
    }

}
