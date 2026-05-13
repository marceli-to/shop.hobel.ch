<?php

namespace App\Filament\Admin\Resources\Edges\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EdgeForm
{
	public static function configure(Schema $schema): Schema
	{
		return $schema
			->components([
				Section::make('Kante')
					->schema([
						TextInput::make('name')
							->label('Kantenausbildung')
							->required(),

						TextInput::make('price')
							->label('Preis/m')
							->numeric()
							->prefix('CHF')
							->default(0)
							->required(),
					]),
			]);
	}
}
