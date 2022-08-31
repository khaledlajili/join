<?php

namespace App\Entity;

use App\Repository\TechnicalTestResultRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TechnicalTestResultRepository::class)]
class TechnicalTestResult
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $filename;

    #[ORM\ManyToOne(inversedBy: 'technicalTestResults')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $candidate = null;

    #[ORM\ManyToOne(inversedBy: 'technicalTestResults')]
    #[ORM\JoinColumn(nullable: false)]
    private ?TechnicalTest $technicalTest = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilename(): ?string
    {
        return $this->filename;
    }

    public function setFilename(string $filename): self
    {
        $this->filename = $filename;

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

    public function getTechnicalTest(): ?TechnicalTest
    {
        return $this->technicalTest;
    }

    public function setTechnicalTest(?TechnicalTest $technicalTest): self
    {
        $this->technicalTest = $technicalTest;

        return $this;
    }
}
