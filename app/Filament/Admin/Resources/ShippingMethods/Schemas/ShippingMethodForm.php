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

						Toggle::make('is_shipping')
							->label('Versand')
							->helperText('Aktivieren, wenn bei dieser Versandart Versandkosten anfallen und im Warenkorb sowie in der Bestellübersicht eine Versandzeile angezeigt werden soll. Bei reiner Abholung deaktiviert lassen.')
							->default(false),
					]),
			]);
	}
}
