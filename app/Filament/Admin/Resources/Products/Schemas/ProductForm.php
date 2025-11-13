<?php

namespace App\Filament\Admin\Resources\Products\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
	public static function configure(Schema $schema): Schema
	{
		return $schema
			->components([
				TextInput::make('uuid')
					->label('UUID')
					->required(),
				TextInput::make('name')
					->required(),
				TextInput::make('slug')
					->required(),
				Textarea::make('description')
					->columnSpanFull(),
				TextInput::make('price')
					->required()
					->numeric()
					->prefix('CHF')
					->helperText('Basispreis (für konfigurierbare Produkte)')
					->live(),
				TextInput::make('stock')
					->required()
					->numeric()
					->default(0),
				FileUpload::make('image')
					->disk('public')
					->directory('products')
					->visibility('public')
					->image()
					->imagePreviewHeight('250'),
				DateTimePicker::make('published_at'),

				// Configuration Section
				Section::make('Produktkonfiguration')
					->description('Konfigurierbare Produkte ermöglichen Kunden, Optionen wie Material, Größe, etc. zu wählen.')
					->schema([
						Toggle::make('is_configurable')
							->label('Konfigurierbares Produkt')
							->live()
							->helperText('Aktivieren Sie dies für Produkte mit wählbaren Optionen'),

						Repeater::make('configuration_schema.attributes')
							->label('Konfigurationsoptionen')
							->schema([
								TextInput::make('key')
									->required()
									->label('Schlüssel')
									->helperText('Eindeutiger Bezeichner (z.B. table_top, width)')
									->columnSpan(1),
								TextInput::make('label')
									->required()
									->label('Bezeichnung')
									->helperText('Angezeigter Name (z.B. Tischplatte, Breite)')
									->columnSpan(1),
								Toggle::make('required')
									->label('Pflichtfeld')
									->default(true)
									->columnSpan(1),
								Repeater::make('options')
									->label('Optionen')
									->schema([
										TextInput::make('value')
											->required()
											->label('Wert')
											->helperText('Technischer Wert (z.B. oak, 100cm)')
											->columnSpan(1),
										TextInput::make('label')
											->required()
											->label('Bezeichnung')
											->helperText('Angezeigter Name (z.B. Eiche, 100 cm)')
											->columnSpan(1),
										TextInput::make('price_modifier')
											->numeric()
											->default(0)
											->prefix('CHF')
											->label('Preisaufschlag')
											->helperText('Zusätzlicher Preis für diese Option')
											->columnSpan(1),
										Textarea::make('description')
											->label('Beschreibung')
											->helperText('Optional: Zusätzliche Informationen')
											->rows(2)
											->columnSpan(3),
									])
									->columns(3)
									->collapsible()
									->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
									->defaultItems(0)
									->columnSpanFull(),
							])
							->columns(3)
							->collapsible()
							->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
							->defaultItems(0)
							->visible(fn ($get) => $get('is_configurable'))
							->columnSpanFull(),
					])
					->collapsible()
					->collapsed(),
			]);
	}
}
