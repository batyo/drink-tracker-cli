<?php

declare(strict_types=1);

namespace App\Validation;

class Validator
{
    public static function validateDate(string $date): bool
    {
        try {
            new \DateTimeImmutable($date);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }


    public static function validateVolume(int $volume): bool
    {
        return $volume > 0;
    }


    public static function validateAbv(float $abv): bool
    {
        return $abv >= 0.0 && $abv <= 100.0;
    }
}
