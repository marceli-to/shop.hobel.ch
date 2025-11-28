<?php

namespace App\Filament\Admin\Resources\Products\Schemas;

use App\Models\Category;
use Filament\Forms\Components\CheckboxList;
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
					])
					->columnSpan(2),

				// Right Column - Settings Section
				Section::make('Einstellungen')
					->schema([
						Toggle::make('published')
							->label('Publizieren')
              ->inline(false)
							->default(false),

						CheckboxList::make('categories')
							->label('Kategorien')
							->relationship('categories', 'name')
							->options(Category::pluck('name', 'id'))
							->columns(1)
							->helperText('Wählen Sie eine oder mehrere Kategorien aus'),
					])
					->columnSpan(1)
					->collapsible(),

				// Left Column - Bilder Section
				Section::make('Bilder')
					->schema([
						Repeater::make('media')
							->label('Bilder')
							->relationship('media')
							->addActionLabel('Bild hinzufügen')
							->orderColumn('order')
							->reorderable()
							->collapsible()
							->collapsed()
							->itemLabel(fn (array $state): ?string => $state['caption'] ?? 'Bild')
							->schema([
								FileUpload::make('file_path')
									->label('Bild')
									->image()
									->directory('products')
									->disk('public')
									->imagePreviewHeight('250')
									->maxSize(5120)
									->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
									->helperText('Erlaubte Dateitypen: JPG, PNG, WebP')
									->required()
									->columnSpanFull()
									->getUploadedFileNameForStorageUsing(function ($file) {
										$fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
										$name = $fileName . '-' . uniqid() . '.' . $file->extension();
										return (string) str($name);
									}),
								TextInput::make('caption')
									->label('Bildunterschrift')
									->maxLength(255)
									->columnSpanFull(),
							])
							->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
								$data['file_name'] = basename($data['file_path']);

								// Get image dimensions
								$filePath = storage_path('app/public/' . $data['file_path']);
								if (file_exists($filePath)) {
									$imageInfo = getimagesize($filePath);
									if ($imageInfo !== false) {
										$data['width'] = $imageInfo[0];
										$data['height'] = $imageInfo[1];
									}
								}

								return $data;
							})
							->mutateRelationshipDataBeforeSaveUsing(function (array $data): array {
								if (isset($data['file_path'])) {
									$data['file_name'] = basename($data['file_path']);

									// Get image dimensions if file path changed
									$filePath = storage_path('app/public/' . $data['file_path']);
									if (file_exists($filePath)) {
										$imageInfo = getimagesize($filePath);
										if ($imageInfo !== false) {
											$data['width'] = $imageInfo[0];
											$data['height'] = $imageInfo[1];
										}
									}
								}
								return $data;
							})
							->columns(1),
					])
					->columnSpan(2)
					->collapsible(),
			]);
	}
}
