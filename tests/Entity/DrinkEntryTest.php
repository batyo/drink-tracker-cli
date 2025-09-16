<?php

namespace Tests\Entity;

use PHPUnit\Framework\TestCase;
use App\Entity\DrinkEntry;

class DrinkEntryTest extends TestCase
{
    public function testAlcoholGramsCalculation(): void
    {
        $entry = new DrinkEntry(null, new \DateTimeImmutable('2025-01-01'), 'Beer', 500, 5.0);
        $this->assertSame(500 * 0.05 * 0.8, $entry->getAlcoholGrams());
    }
}
