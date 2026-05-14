<?php

namespace App\Console\Commands;

use App\Models\WoodType;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

class SeedWoodTypeFactors extends Command
{
    use ConfirmableTrait;

    protected $signature = 'app:seed-wood-type-factors {--force : Force the operation to run when in production}';

    protected $description = 'Update price and sorting_factor on wood types from the official price list, matching by name';

    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return Command::FAILURE;
        }

        // [price (CHF/m³), sorting_factor] keyed by the wood type names that
        // should receive these values. Rows whose names are not listed below
        // are left untouched.
        $groups = [
            'Fichte / Tanne (1.45)' => [
                'price' => 1800,
                'factor' => 1.45,
                'names' => ['Fichte', 'Tanne (Weisstanne)', 'Fichte (Rottanne)', 'Fichte gedämpft'],
            ],
            'Buche (1.45)' => [
                'price' => 2500,
                'factor' => 1.45,
                'names' => ['Buche', 'Buche schlicht', 'Buche natur', 'Buche gedämpft', 'Buche mit Kern'],
            ],
            'Ahorn (1.45)' => [
                'price' => 3500,
                'factor' => 1.45,
                'names' => ['Ahorn', 'Ahorn schlicht'],
            ],
            'Esche wild (1.55)' => [
                'price' => 3400,
                'factor' => 1.55,
                'names' => ['Esche wild'],
            ],
            'Lärche (1.60)' => [
                'price' => 2600,
                'factor' => 1.60,
                'names' => ['Lärche'],
            ],
            'Eiche (1.60)' => [
                'price' => 4500,
                'factor' => 1.60,
                'names' => [
                    'Eiche',
                    'Eiche (3,00 - 4,40 m)',
                    'Eiche lang (ab 4,50 m)',
                    'Eiche charaktervoll, astreich',
                    'Eiche geräuchert',
                    'Roteiche',
                ],
            ],
            'Eiche schlicht Rift mit Spiegel (1.75)' => [
                'price' => 5600,
                'factor' => 1.75,
                'names' => ['Eiche schlicht'],
            ],
            'Kirschbaum europäisch (1.60)' => [
                'price' => 6500,
                'factor' => 1.60,
                'names' => ['Kirschbaum Europäisch'],
            ],
            'Ulme (1.75)' => [
                'price' => 7500,
                'factor' => 1.75,
                'names' => ['Ulme', 'Ulme gedämpft, charaktervoll, astreich'],
            ],
            'Birnbaum (1.75)' => [
                'price' => 8500,
                'factor' => 1.75,
                'names' => ['Birnbaum'],
            ],
            'Nussbaum amerikanisch (1.95)' => [
                'price' => 9500,
                'factor' => 1.95,
                'names' => ['Nussbaum Amerikanisch'],
            ],
            'Nussbaum europäisch (1.95)' => [
                'price' => 12000,
                'factor' => 1.95,
                'names' => ['Nussbaum Europäisch'],
            ],
            'Räuchereiche (1.75)' => [
                'price' => 7000,
                'factor' => 1.75,
                'names' => ['Räuchereiche'],
            ],
            'Wenge (2.10)' => [
                'price' => 10500,
                'factor' => 2.10,
                'names' => ['Wenge'],
            ],
        ];

        $totalUpdated = 0;
        $missing = [];

        foreach ($groups as $label => $group) {
            $this->line($label);
            foreach ($group['names'] as $name) {
                $count = WoodType::where('name', $name)->update([
                    'price' => $group['price'],
                    'sorting_factor' => $group['factor'],
                ]);

                if ($count === 0) {
                    $missing[] = $name;
                    $this->warn(sprintf('  %-45s → not found', $name));
                    continue;
                }

                $totalUpdated += $count;
                $this->line(sprintf(
                    '  %-45s → CHF %s, factor %.2f (%d row%s)',
                    $name,
                    number_format($group['price'], 0, '.', "'"),
                    $group['factor'],
                    $count,
                    $count === 1 ? '' : 's',
                ));
            }
        }

        $this->newLine();
        $this->info("Updated {$totalUpdated} wood type row(s).");

        if ($missing) {
            $this->warn('No matching wood type found for: ' . implode(', ', $missing));
        }

        return Command::SUCCESS;
    }
}
