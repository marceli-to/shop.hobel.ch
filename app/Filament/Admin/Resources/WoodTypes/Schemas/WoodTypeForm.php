<?php

namespace App\Filament\Admin\Resources\WoodTypes\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
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

						TextInput::make('sorting_factor')
							->label('Sortier-/Verschnittfaktor')
							->numeric()
							->step(0.01)
							->default(1)
							->required(),

						Repeater::make('image')
							->label('Bild')
							->relationship('image')
							->maxItems(1)
							->addActionLabel('Bild hinzufügen')
							->collapsible()
							->itemLabel(fn (array $state): ?string => $state['caption'] ?? 'Bild')
							->schema([
								FileUpload::make('file_path')
									->label('Bild')
									->image()
									->directory('wood-types')
									->disk('public')
									->imagePreviewHeight('250')
									->maxSize(5120)
									->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])
									->helperText('Erlaubte Dateitypen: JPG, PNG, WebP')
									->required()
									->columnSpanFull(),

								TextInput::make('caption')
									->label('Bildunterschrift')
									->maxLength(255)
									->columnSpanFull(),
							])
							->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
								$data['file_name'] = basename($data['file_path']);

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
					]),
			]);
	}
}
