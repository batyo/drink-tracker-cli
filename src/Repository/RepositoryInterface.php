<?php

namespace App\Repository;

use App\Entity\DrinkEntry;

interface RepositoryInterface
{
    public function init(): void;

    public function add(DrinkEntry $entry): void;

    /** @return DrinkEntry[] */
    public function list(\DateTimeImmutable $from, \DateTimeImmutable $to): array;
}
