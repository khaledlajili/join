<?php

namespace App\Entity;

use App\Repository\CollectiveInterviewsResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CollectiveInterviewsResultRepository::class)]
class CollectiveInterviewsResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $result;

    #[ORM\Column(type: 'text')]
    private $remark;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'collectiveInterviewsResults')]
    #[ORM\JoinColumn(nullable: false)]
    private $recruiter;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'collectiveInterviewsResultsAsCandidate')]
    #[ORM\JoinColumn(nullable: false)]
    private $candidate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResult(): ?string
    {
        return $this->result;
    }

    public function setResult(string $result): self
    {
        $this->result = $result;

        return $this;
    }

    public function getRemark(): ?string
    {
        return $this->remark;
    }

    public function setRemark(string $remark): self
    {
        $this->remark = $remark;

        return $this;
    }
    public function getRecruiter(): ?User
    {
        return $this->recruiter;
    }

    public function setRecruiter(?User $recruiter): self
    {
        $this->recruiter = $recruiter;

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
}
