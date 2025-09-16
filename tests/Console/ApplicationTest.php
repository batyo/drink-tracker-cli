<?php

namespace Tests\Console;

use App\Console\Application;
use PHPUnit\Framework\TestCase;

/**
 * vendor/bin/phpunit --bootstrap vendor/autoload.php tests で実行
 */
class ApplicationTest extends TestCase
{
    private string $dbPath;

    protected function setUp(): void
    {
        $this->dbPath = sys_get_temp_dir() . '/drink_test_' . uniqid() . '.sqlite';
        if (file_exists($this->dbPath)) {
            unlink($this->dbPath);
        }
    }

    protected function tearDown(): void
    {
        if (file_exists($this->dbPath)) {
            unlink($this->dbPath);
        }
    }

    private function runApp(array $argv): string
    {
        $app = new Application($this->dbPath);

        ob_start();
        try {
            $app->run($argv);
        } catch (\Throwable $e) {
            echo "[EXCEPTION] " . $e->getMessage();
        }
        return ob_get_clean();
    }

    public function testInitDbCreatesFile(): void
    {
        $output = $this->runApp(['bin/drink', 'init-db']);
        $this->assertStringContainsString('Database initialized', $output);
        $this->assertFileExists($this->dbPath);
    }

    public function testAddAndListAndSummary(): void
    {
        $this->runApp(['bin/drink', 'init-db']);
        $this->runApp(['bin/drink', 'add', '2025-09-15', 'Beer', '350', '5']);
        $this->runApp(['bin/drink', 'add', '2025-09-15', 'Wine', '150', '12']);

        $list = $this->runApp(['bin/drink', 'list', '2025-09-15', '2025-09-15']);
        $this->assertStringContainsString('Beer', $list);
        $this->assertStringContainsString('Wine', $list);

        $summary = $this->runApp(['bin/drink', 'summary', '2025-09-15', '2025-09-15']);
        $this->assertStringContainsString('Entries: 2', $summary);
        $this->assertStringContainsString('Standard Drinks', $summary);
    }

    public function testAddFailsWithInvalidAbv(): void
    {
        $this->runApp(['bin/drink', 'init-db']);
        $out = $this->runApp(['bin/drink', 'add', '2025-09-15', 'Whisky', '50', '200']);
        $this->assertStringContainsString('abv must be between 0 and 100', $out);
    }

    public function testListFailsWithBadDate(): void
    {
        $this->runApp(['bin/drink', 'init-db']);
        $out = $this->runApp(['bin/drink', 'list', 'BADDATE']);
        $this->assertStringContainsString('Invalid date format', $out);
    }
}
