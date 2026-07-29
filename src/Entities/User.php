<?php

namespace App\Entities;

use Trunk\ORM\BaseEntity;
use Trunk\Database\ORM\Attributes\Entity;
use Trunk\Database\ORM\Attributes\Column;

#[Entity(table: 'users')]
class User extends BaseEntity
{
    #[Column(primary: true)]
    private ?int $id = null;
    
    #[Column(type: 'VARCHAR', length: 255)]
    private string $name;
    
    #[Column(type: 'VARCHAR', length: 255)]
    private string $email;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}
