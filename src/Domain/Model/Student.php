<?php

namespace Alura\Pdo\Domain\Model;

class Student
{
    private ?int $id;
    private string $name;
    private \DateTimeInterface $birthDate;
    /**
     * @var phone[]
     */
    private array $phones = [];


    public function __construct(?int $id, string $name, \DateTimeInterface $birthDate)
    {
        $this->id = $id;
        $this->name = $name;
        $this->birthDate = $birthDate;
    }

    public function defineId(int $id)
    {
        if (!is_null($this->id))
        {
            throw new \DomainException("Você só pode definir o ID uma vez.");
        }

        $this->id = $id;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function birthDate(): \DateTimeInterface
    {
        return $this->birthDate;
    }

    public function age(): int
    {
        return $this->birthDate
            ->diff(new \DateTimeImmutable())
            ->y;
    }

    public function addPhone(Phone $phone)
    {
        $this->phones[] = $phone;
    }

    /**
     * @return phone[]
     */
    public function phones(): array
    {
        return $this->phones;
    }
}
