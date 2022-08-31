<?php

namespace App\Entity;

use App\Repository\InterviewsResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterviewsResultRepository::class)]
class InterviewsResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $result;

    #[ORM\Column(type: 'text')]
    private $remark;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'interviewsResults')]
    #[ORM\JoinColumn(nullable: false)]
    private $recruiters;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'inteviewsResultsAsCandidate')]
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

    public function getRecruiters(): ?User
    {
        return $this->recruiters;
    }

    public function setRecruiters(?User $recruiters): self
    {
        $this->recruiters = $recruiters;

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
