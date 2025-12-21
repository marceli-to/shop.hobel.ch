<?php

namespace App\Filament\Admin\Resources\WoodTypes\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WoodTypeForm
{
	public static function configure(Schema $schema): Schema
	{
		return $schema
			->components([
				Section::make('Holzart')
					->schema([
						TextInput::make('name')
							->label('Name')
							->required(),

						TextInput::make('price')
							->label('Preis/m³')
							->numeric()
							->prefix('CHF')
							->default(0)
							->required(),
					]),
			]);
	}
}
