<?php

namespace App\Enums;

enum ProductType: string
{
    case Simple = 'simple';
    case Configurable = 'configurable';
    case Variations = 'variations';

    public function label(): string
    {
        return match ($this) {
            self::Simple => 'Einfach',
            self::Configurable => 'Konfigurierbar',
            self::Variations => 'Varianten',
        };
    }
}
