<?php

namespace App\Entity;

class DrinkEntry
{
    public function __construct(
        public readonly ?int $id,
        public readonly \DateTimeImmutable $date,
        public readonly string $name,
        public readonly float $volumeMl,
        public readonly float $abv
    ) {}

    public function getAlcoholGrams(): float
    {
        return $this->volumeMl * ($this->abv / 100) * 0.8;
    }
}
