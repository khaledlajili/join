<?php

namespace App\Entity;

use App\Repository\PreRegistrationFormFieldOptionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: PreRegistrationFormFieldOptionRepository::class)]
class PreRegistrationFormFieldOption
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private $value;

    #[ORM\ManyToOne(targetEntity: PreRegistrationFormField::class, inversedBy: 'preRegistrationFormFieldOptions')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private $preRegistrationFormField;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getPreRegistrationFormField(): ?PreRegistrationFormField
    {
        return $this->preRegistrationFormField;
    }

    public function setPreRegistrationFormField(?PreRegistrationFormField $preRegistrationFormField): self
    {
        $this->preRegistrationFormField = $preRegistrationFormField;

        return $this;
    }
}
