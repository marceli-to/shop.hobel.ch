<?php

namespace App\Filament\Admin\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
	public static function configure(Schema $schema): Schema
	{
		return $schema
			->columns(3)
			->components([
				// Left Column - Produkt Section
				Section::make('Produkt')
					->schema([
						TextInput::make('name')
							->label('Titel')
							->required()
							->live(onBlur: true)
							->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),

						TextInput::make('slug')
							->label('Slug')
							->required()
							->disabled()
							->dehydrated()
							->unique(ignoreRecord: true),

						Textarea::make('description')
							->label('Beschreibung')
							->rows(4),

						TextInput::make('price')
							->label('Preis')
							->required()
							->numeric()
							->prefix('CHF')
							->step(0.01),

						TextInput::make('stock')
							->label('Lagerbestand')
							->required()
							->numeric()
							->default(0)
							->minValue(0),

						CheckboxList::make('categories')
							->label('Kategorien')
							->relationship('categories', 'name')
							->options(Category::pluck('name', 'id'))
							->columns(2)
							->helperText('Wählen Sie eine oder mehrere Kategorien aus'),
					])
					->columnSpan(2),

				// Right Column - Medien Section
				Section::make('Medien')
					->schema([
						FileUpload::make('image')
							->label('Hauptbild')
							->disk('public')
							->directory('products')
							->visibility('public')
							->image()
							->imageEditor()
							->imagePreviewHeight('250')
							->maxSize(5120)
							->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
							->helperText('Erlaubte Dateitypen: JPG, PNG, WebP'),
					])
					->columnSpan(1)
					->collapsible(),
			]);
	}
}
