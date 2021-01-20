<?php


namespace Bx\Model\Models;


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
        return (int)$this['id'];
    }

    public function getName(): string
    {
        return (string)$this['name'];
    }

    public function getLastName(): string
    {
        return (string)$this['last_name'];
    }

    public function getSecondName(): string
    {
        return (string)$this['second_name'];
    }

    public function getEmail(): string
    {
        return (string)$this['email'];
    }

    public function getPhone(): string
    {
        return (string)$this['phone'];
    }
}