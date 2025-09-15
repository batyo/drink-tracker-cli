<?php
namespace App\Console;

use App\Repository\SqliteRepository;
use App\Service\DrinkManager;
use App\Entity\DrinkEntry;

class Application
{
    private DrinkManager $manager;

    public function __construct(private string $dbPath)
    {
        $repo = new SqliteRepository($dbPath);
        $this->manager = new DrinkManager($repo);
    }

    public function run(array $argv): void
    {
        $command = $argv[1] ?? null;

        try {
            switch ($command) {
                case 'init-db':
                    $this->initDb();
                    break;
                case 'add':
                    $this->add(array_slice($argv, 2));
                    break;
                case 'list':
                    $this->list(array_slice($argv, 2));
                    break;
                case 'summary':
                    $this->summary(array_slice($argv, 2));
                    break;
                default:
                    $this->printHelp();
                    exit(1);
            }
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            exit(1);
        }
    }

    private function initDb(): void
    {
        $repo = new SqliteRepository($this->dbPath);
        $repo->init();
        echo "Database initialized at {$this->dbPath}\n";
    }

    private function add(array $args): void
    {
        if (count($args) < 4) {
            throw new \InvalidArgumentException("Usage: add <YYYY-MM-DD> <name> <volume_ml> <abv>");
        }

        [$dateStr, $name, $volStr, $abvStr] = $args;

        $date = $this->parseDate($dateStr);
        $volume = $this->parsePositiveFloat($volStr, 'volume_ml');
        $abv = $this->parsePositiveFloat($abvStr, 'abv');

        if ($abv <= 0 || $abv > 100) {
            throw new \InvalidArgumentException("abv must be between 0 and 100");
        }

        $entry = new DrinkEntry(null, $date, $name, $volume, $abv);
        $this->manager->add($entry);

        echo "Added: {$name} ({$volume}ml @ {$abv}%)\n";
    }

    private function list(array $args): void
    {
        [$from, $to] = $this->parseDateRange($args);
        $entries = $this->manager->list($from, $to);

        if (!$entries) {
            echo "No entries found.\n";
            return;
        }

        foreach ($entries as $e) {
            echo $e->date->format('Y-m-d')
                . " | {$e->name} | {$e->volumeMl}ml @ {$e->abv}%"
                . " (" . round($e->getAlcoholGrams(), 1) . "g)\n";
        }
    }

    private function summary(array $args): void
    {
        [$from, $to] = $this->parseDateRange($args);
        $s = $this->manager->summary($from, $to);

        echo "Entries: {$s['count']}\n";
        echo "Total Volume: {$s['totalMl']} ml\n";
        echo "Total Pure Alcohol: " . round($s['totalGrams'], 1) . " g\n";
        echo "Standard Drinks: " . round($s['standardDrinks'], 1) . "\n";
    }

    // ------------------------
    // 🛡️ バリデーション補助
    // ------------------------
    private function parseDate(string $value): \DateTimeImmutable
    {
        $d = \DateTimeImmutable::createFromFormat('Y-m-d', $value);
        if (!$d) {
            throw new \InvalidArgumentException("Invalid date format: {$value} (expected YYYY-MM-DD)");
        }
        return $d;
    }

    private function parsePositiveFloat(string $value, string $field): float
    {
        if (!is_numeric($value)) {
            throw new \InvalidArgumentException("{$field} must be numeric");
        }
        $f = (float)$value;
        if ($f <= 0) {
            throw new \InvalidArgumentException("{$field} must be > 0");
        }
        return $f;
    }

    private function parseDateRange(array $args): array
    {
        $from = isset($args[0]) ? $this->parseDate($args[0]) : new \DateTimeImmutable('first day of this month');
        $to   = isset($args[1]) ? $this->parseDate($args[1]) : new \DateTimeImmutable('last day of this month');

        if ($from > $to) {
            throw new \InvalidArgumentException("from-date must be <= to-date");
        }
        return [$from, $to];
    }

    private function error(string $msg): void
    {
        fwrite(STDERR, "\033[31mError:\033[0m {$msg}\n");
    }

    private function printHelp(): void
    {
        echo <<<TXT
Usage:
    init-db
    add <YYYY-MM-DD> <name> <volume_ml> <abv>
    list [from] [to]
    summary [from] [to]

TXT;
    }
}
