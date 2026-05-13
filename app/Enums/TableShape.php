<?php

namespace App\Enums;

enum TableShape: string
{
    case Rectangular = 'rectangular';
    case Round = 'round';
    case Oval = 'oval';

    public function label(): string
    {
        return match ($this) {
            self::Rectangular => 'Rechteckig',
            self::Round => 'Rund',
            self::Oval => 'Oval',
        };
    }
}
