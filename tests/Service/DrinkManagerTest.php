<?php

namespace Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\DrinkManager;
use App\Repository\RepositoryInterface;
use App\Entity\DrinkEntry;

class DrinkManagerTest extends TestCase
{
    public function testSummaryCalculatesTotalGrams(): void
    {
        $mockRepo = $this->createMock(RepositoryInterface::class);
        $mockRepo->method('list')->willReturn([
            new DrinkEntry(null, new \DateTimeImmutable('2025-01-01'), 'Beer', 500, 5.0),
            new DrinkEntry(null, new \DateTimeImmutable('2025-01-02'), 'Wine', 150, 12.0),
        ]);

        $manager = new DrinkManager($mockRepo);
        $result = $manager->summary(
            new \DateTimeImmutable('2025-01-01'),
            new \DateTimeImmutable('2025-01-31')
        );

        $this->assertArrayHasKey('totalGrams', $result);
        $this->assertGreaterThan(0, $result['totalGrams']);
    }
}
