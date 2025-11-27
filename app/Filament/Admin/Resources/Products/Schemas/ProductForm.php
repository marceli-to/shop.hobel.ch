<?php

namespace App\Filament\Admin\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\Image\Image;

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
						SpatieMediaLibraryFileUpload::make('gallery')
							->label('Bilder')
							->collection('gallery')
							->multiple()
							->reorderable()
							->image()
							->imagePreviewHeight('250')
							->maxSize(5120)
							->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
							->helperText('Laden Sie ein oder mehrere Bilder hoch. Erlaubte Dateitypen: JPG, PNG, WebP')
							->mediaName(function (TemporaryUploadedFile $file): string {
								$originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
								$randomString = Str::random(8);

								return Str::slug($originalName) . '-' . $randomString;
							})
							->customProperties(function (TemporaryUploadedFile $file): array {
								$image = Image::load($file->getRealPath());

								return [
									'width' => $image->getWidth(),
									'height' => $image->getHeight(),
								];
							}),
					])
					->columnSpan(1)
					->collapsible(),
			]);
	}
}
