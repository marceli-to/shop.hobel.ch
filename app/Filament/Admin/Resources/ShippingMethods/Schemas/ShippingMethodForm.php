<?php

namespace App\Filament\Admin\Resources\ShippingMethods\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ShippingMethodForm
{
	public static function configure(Schema $schema): Schema
	{
		return $schema
			->components([
				Section::make('Versandart')
					->schema([
						TextInput::make('name')
							->label('Name')
							->required(),

						TextInput::make('price')
							->label('Preis')
							->numeric()
							->prefix('CHF')
							->default(0)
							->required(),

						Toggle::make('requires_delivery_address')
							->label('Lieferadresse erforderlich')
							->helperText('Aktivieren, wenn für diese Versandart eine Lieferadresse benötigt wird (z. B. Versand per Post). Deaktivieren bei Selbstabholung oder digitalen Lieferungen.')
							->default(false),
					]),
			]);
	}
}
