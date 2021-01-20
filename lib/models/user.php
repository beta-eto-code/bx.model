<?php


namespace Bx\Model\Models;

use Bx\Model\AbsOptimizedModel;

class User extends AbsOptimizedModel
{
    protected function toArray(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'second_name' => $this->getSecondName(),
            'last_name' => $this->getLastName(),
            'email' => $this->getEmail(),
            'phone' => $this->getPhone()
        ];
    }

    public function getId(): int
    {
        return (int)$this['ID'];
    }

    public function getName(): string
    {
        return (string)$this['NAME'];
    }

    public function getLastName(): string
    {
        return (string)$this['LAST_NAME'];
    }

    public function getSecondName(): string
    {
        return (string)$this['SECOND_NAME'];
    }

    public function getEmail(): string
    {
        return (string)$this['EMAIL'];
    }

    public function getPhone(): string
    {
        return (string)$this['PERSONAL_PHONE'];
    }
}