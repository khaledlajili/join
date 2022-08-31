<?php

namespace App\Entity;

use App\Repository\ResponseRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResponseRepository::class)]
class Response
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'text')]
    private $response;

    #[ORM\ManyToOne(targetEntity: PreRegistrationFormField::class, inversedBy: 'responses')]
    #[ORM\JoinColumn(nullable: false)]
    private $preRegistrationFormField;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'responses')]
    #[ORM\JoinColumn(nullable: false)]
    private $_user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(string $response): self
    {
        $this->response = $response;

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

    public function getUser(): ?User
    {
        return $this->_user;
    }

    public function setUser(?User $_user): self
    {
        $this->_user = $_user;

        return $this;
    }
}
