<?php

namespace App\Repository;

use App\Entity\DrinkEntry;
use PDO;

class SqliteRepository implements RepositoryInterface
{
    private PDO $pdo;

    public function __construct(string $dbPath)
    {
        $this->pdo = new PDO('sqlite:' . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->exec('PRAGMA foreign_keys = ON;');
    }

    public function init(): void
    {
        $sql = file_get_contents(__DIR__ . '/../../migrations/001_create_drinks_table.sql');
        $this->pdo->exec($sql);
    }

    public function add(DrinkEntry $entry): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO drinks (date, name, volume_ml, abv)
            VALUES (:date, :name, :volume_ml, :abv)'
        );
        $stmt->execute([
            ':date' => $entry->date->format('Y-m-d'),
            ':name' => $entry->name,
            ':volume_ml' => $entry->volumeMl,
            ':abv' => $entry->abv,
        ]);
    }

    /**
     * @return DrinkEntry[]
     */
    public function list(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM drinks WHERE date BETWEEN :from AND :to ORDER BY date ASC'
        );
        $stmt->execute([
            ':from' => $from->format('Y-m-d'),
            ':to'   => $to->format('Y-m-d'),
        ]);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($r) => new DrinkEntry(
            (int)$r['id'],
            new \DateTimeImmutable($r['date']),
            $r['name'],
            (float)$r['volume_ml'],
            (float)$r['abv']
        ), $rows);
    }
}
