<?php

namespace App\Filament\Admin\Resources\Surfaces\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SurfaceForm
{
	public static function configure(Schema $schema): Schema
	{
		return $schema
			->components([
				Section::make('Oberfläche')
					->schema([
						TextInput::make('name')
							->label('Name')
							->required(),

						TextInput::make('price')
							->label('Preis/m²')
							->numeric()
							->prefix('CHF')
							->default(0)
							->required(),

						TextInput::make('minimum_amount')
							->label('Mindestbetrag')
							->numeric()
							->prefix('CHF')
							->default(0)
							->required(),
					]),
			]);
	}
}
