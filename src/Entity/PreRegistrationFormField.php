<?php

namespace App\Entity;

use App\Repository\PreRegistrationFormFieldRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PreRegistrationFormFieldRepository::class)]
class PreRegistrationFormField
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private $label;

    #[ORM\Column(type: 'string', length: 255)]
    private $type;

    #[ORM\Column(type: 'boolean')]
    private $required;

    #[ORM\OneToOne(inversedBy: 'nextFormField', targetEntity: self::class, cascade: ['persist'])]
    private $previousFormField;

    #[ORM\OneToOne(mappedBy: 'previousFormField', targetEntity: self::class, cascade: ['persist'])]
    private $nextFormField;

    #[ORM\OneToMany(mappedBy: 'preRegistrationFormField', targetEntity: Response::class, orphanRemoval: true)]
    private $responses;

    #[ORM\ManyToMany(targetEntity: Department::class, inversedBy: 'preRegistrationFormFields')]
    private $departments;

    #[ORM\ManyToOne(targetEntity: RecruitmentSession::class, inversedBy: 'preRegistrationFormFields')]
    #[ORM\JoinColumn(nullable: false)]
    private $recruitmentSession;

    #[ORM\OneToMany(mappedBy: 'preRegistrationFormField', targetEntity: PreRegistrationFormFieldOption::class, cascade: ["persist"])]
    #[Assert\Valid]
    private $preRegistrationFormFieldOptions;

    public function __construct()
    {
       // $this->options = new ArrayCollection();
        $this->responses = new ArrayCollection();
        $this->departments = new ArrayCollection();
        $this->options = new ArrayCollection();
        $this->preRegistrationFormFieldOptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getRequired(): ?bool
    {
        return $this->required;
    }

    public function setRequired(bool $required): self
    {
        $this->required = $required;

        return $this;
    }

    public function getPreviousFormField(): ?self
    {
        return $this->previousFormField;
    }

    public function setPreviousFormField(?self $previousFormField): self
    {
        $this->previousFormField = $previousFormField;

        return $this;
    }

    public function getNextFormField(): ?self
    {
        return $this->nextFormField;
    }

    public function setNextFormField(?self $nextFormField): self
    {
        // unset the owning side of the relation if necessary
        if ($nextFormField === null && $this->nextFormField !== null) {
            $this->nextFormField->setPreviousFormField(null);
        }

        // set the owning side of the relation if necessary
        if ($nextFormField !== null && $nextFormField->getPreviousFormField() !== $this) {
            $nextFormField->setPreviousFormField($this);
        }

        $this->nextFormField = $nextFormField;

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
            $response->setPreRegistrationFormField($this);
        }

        return $this;
    }

    public function removeResponse(Response $response): self
    {
        if ($this->responses->removeElement($response)) {
            // set the owning side to null (unless already changed)
            if ($response->getPreRegistrationFormField() === $this) {
                $response->setPreRegistrationFormField(null);
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
            $this->departments[] = $department;
        }

        return $this;
    }

    public function removeDepartment(Department $department): self
    {
        $this->departments->removeElement($department);

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
     * @return Collection<int, PreRegistrationFormFieldOption>
     */
    public function getPreRegistrationFormFieldOptions(): Collection
    {
        return $this->preRegistrationFormFieldOptions;
    }

    public function addPreRegistrationFormFieldOption(PreRegistrationFormFieldOption $preRegistrationFormFieldOption): self
    {
        if (!$this->preRegistrationFormFieldOptions->contains($preRegistrationFormFieldOption)) {
            $this->preRegistrationFormFieldOptions[] = $preRegistrationFormFieldOption;
            $preRegistrationFormFieldOption->setPreRegistrationFormField($this);
        }

        return $this;
    }

    public function removePreRegistrationFormFieldOption(PreRegistrationFormFieldOption $preRegistrationFormFieldOption): self
    {
        if ($this->preRegistrationFormFieldOptions->removeElement($preRegistrationFormFieldOption)) {
            // set the owning side to null (unless already changed)
            if ($preRegistrationFormFieldOption->getPreRegistrationFormField() === $this) {
                $preRegistrationFormFieldOption->setPreRegistrationFormField(null);
            }
        }

        return $this;
    }
}
