<?php

namespace Tests\Repository;

use PHPUnit\Framework\TestCase;
use App\Repository\SqliteRepository;
use App\Entity\DrinkEntry;

class SqliteRepositoryTest extends TestCase
{
    private string $dbFile;

    protected function setUp(): void
    {
        $this->dbFile = __DIR__ . '/test.sqlite';
        if (file_exists($this->dbFile)) {
            unlink($this->dbFile);
        }
    }

    public function testAddAndList(): void
    {
        $repo = new SqliteRepository($this->dbFile);
        $repo->init();

        $entry = new DrinkEntry(null, new \DateTimeImmutable('2025-01-01'), 'Beer', 500, 5.0);
        $repo->add($entry);

        $results = $repo->list(
            new \DateTimeImmutable('2025-01-01'),
            new \DateTimeImmutable('2025-01-02')
        );

        $this->assertCount(1, $results);
        $this->assertSame('Beer', $results[0]->name);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->dbFile)) {
            unlink($this->dbFile);
        }
    }
}
