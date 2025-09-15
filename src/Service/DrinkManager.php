<?php

namespace App\Service;

use App\Repository\RepositoryInterface;
use App\Entity\DrinkEntry;

class DrinkManager
{
    private const GRAMS_PER_STANDARD_DRINK = 10.0;

    public function __construct(
        private RepositoryInterface $repository
    ) {}

    public function add(DrinkEntry $entry): void
    {
        $this->repository->add($entry);
    }

    /**
     * @return DrinkEntry[]
     */
    public function list(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->repository->list($from, $to);
    }

    /**
     * 期間内の合計統計を返す
     * @return array{count:int,totalMl:float,totalGrams:float,standardDrinks:float}
     */
    public function summary(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        $entries = $this->repository->list($from, $to);

        $totalMl = 0.0;
        $totalGrams = 0.0;

        foreach ($entries as $entry) {
            $totalMl += $entry->volumeMl;
            $totalGrams += $entry->getAlcoholGrams();
        }

        $standardDrinks = $totalGrams / self::GRAMS_PER_STANDARD_DRINK;

        return [
            'count' => count($entries),
            'totalMl' => $totalMl,
            'totalGrams' => $totalGrams,
            'standardDrinks' => $standardDrinks,
        ];
    }
}
