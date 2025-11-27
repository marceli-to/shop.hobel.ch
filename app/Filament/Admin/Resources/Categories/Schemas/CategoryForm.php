<?php

namespace App\Filament\Admin\Resources\Categories\Schemas;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Spatie\Image\Image;

class CategoryForm
{
	public static function configure(Schema $schema): Schema
	{
		return $schema
			->columns(3)
			->components([
				// Main Section - Kategorie
				Section::make('Kategorie')
					->schema([
						TextInput::make('name')
							->label('Name')
							->required()
							->live(onBlur: true)
							->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),

						TextInput::make('slug')
							->label('Slug')
							->required()
							->disabled()
							->dehydrated()
							->unique(ignoreRecord: true),
					])
					->columnSpan(2),

				// Right Column - Medien Section
				Section::make('Bild')
					->schema([
						SpatieMediaLibraryFileUpload::make('image')
							->label('Kategoriebild')
							->collection('image')
							->image()
							->imagePreviewHeight('250')
							->maxSize(5120)
							->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
							->helperText('Laden Sie ein Bild für die Kategorie hoch. Erlaubte Dateitypen: JPG, PNG, WebP')
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
