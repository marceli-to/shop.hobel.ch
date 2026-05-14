<?php

namespace App\Filament\Admin\Resources\Products\Schemas;

use App\Enums\ProductType;
use App\Enums\TableShape;
use App\Filament\Admin\Resources\Products\Pages\EditProduct;
use App\Filament\Admin\Resources\Products\RelationManagers\ChildrenRelationManager;
use App\Filament\Admin\Resources\Products\RelationManagers\EdgesRelationManager;
use App\Filament\Admin\Resources\Products\RelationManagers\SurfacesRelationManager;
use App\Filament\Admin\Resources\Products\RelationManagers\WoodTypesRelationManager;
use App\Models\Category;
use App\Models\ShippingMethod;
use App\Models\Tag;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ProductForm
{
	public static function configure(Schema $schema): Schema
	{
		return $schema
			->components([
				Tabs::make()
					->columnSpanFull()
					->tabs([
						Tab::make('Produkt')
							->columns(12)
							->schema([
								Group::make()
									->columnSpan(8)
									->schema(self::productMain()),
								Group::make()
									->columnSpan(4)
									->schema(self::productSidebar()),
							]),

						Tab::make('Bilder')
							->schema(self::imagesTab()),

						Tab::make('Einstellungen')
							->schema(self::settingsTab()),

						Tab::make('Konfiguration')
							->visible(fn (callable $get) => $get('type') === ProductType::Configurable->value)
							->schema(self::configurationTab()),

						Tab::make('Varianten')
							->visible(fn (callable $get) => $get('type') === ProductType::Variations->value)
							->schema(self::variationsTab()),
					]),
			]);
	}

	protected static function productMain(): array
	{
		return [
			TextInput::make('name')
				->label('Name')
				->required()
				->live(onBlur: true)
				->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),

			TextInput::make('sku')
				->label('SKU')
				->maxLength(255),

			Textarea::make('short_description')
				->label('Kurzbeschreibung')
				->rows(2)
				->maxLength(255)
				->helperText('Kurze Produktbeschreibung (max. 255 Zeichen)'),

			Textarea::make('description')
				->label('Beschreibung')
				->rows(4),

			TextInput::make('delivery_time')
				->label('Lieferzeit')
				->maxLength(255)
				->helperText('z.B. "2-3 Werktage"'),

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
		];
	}

	protected static function productSidebar(): array
	{
		return [
			Section::make('Attribute')
				->schema([
					Repeater::make('attributes')
						->label('Attribute')
						->relationship('attributes')
						->addActionLabel('Attribut hinzufügen')
						->orderColumn('order')
						->reorderable()
						->collapsible()
						->itemLabel(fn (array $state): ?string => $state['name'] ?? 'Attribut')
						->schema([
							TextInput::make('name')
								->label('Name')
								->required()
								->columnSpanFull(),
						]),
				])
				->collapsible(),
		];
	}

	protected static function configurationTab(): array
	{
		return [
			Section::make('Min/Max Werte')
				->collapsible()
				->collapsed()
				->columns(2)
				->schema([
					TextInput::make('min_length')
						->label('Min. Länge')
						->numeric()
						->step(0.01)
						->suffix('cm'),
					TextInput::make('max_length')
						->label('Max. Länge')
						->numeric()
						->step(0.01)
						->suffix('cm'),
					TextInput::make('min_width')
						->label('Min. Breite')
						->numeric()
						->step(0.01)
						->suffix('cm'),
					TextInput::make('max_width')
						->label('Max. Breite')
						->numeric()
						->step(0.01)
						->suffix('cm'),
				]),

			Section::make('Preisparameter')
				->collapsible()
				->collapsed()
				->columns(2)
				->schema([
					TextInput::make('base_price')
						->label('Modellbasis')
						->numeric()
						->step(0.01)
						->prefix('CHF'),
					TextInput::make('material_factor')
						->label('Material-Verkaufsfaktor')
						->numeric()
						->step(0.01),
					TextInput::make('surface_processing_price')
						->label('Flächenbearbeitung')
						->numeric()
						->step(0.01)
						->prefix('CHF')
						->suffix('/m²'),
					TextInput::make('minimum_price')
						->label('Mindestpreis exkl. MwSt.')
						->numeric()
						->step(0.01)
						->prefix('CHF'),
					TextInput::make('large_format_threshold')
						->label('Grossformat ab')
						->numeric()
						->step(0.01)
						->suffix('m²'),
					TextInput::make('large_format_surcharge')
						->label('Grossformatzuschlag')
						->numeric()
						->step(0.01)
						->prefix('CHF')
						->suffix('/m²'),
					TextInput::make('oversize_threshold')
						->label('Überformat ab')
						->numeric()
						->step(0.01)
						->suffix('m²'),
					TextInput::make('oversize_surcharge')
						->label('Überformatzuschlag')
						->numeric()
						->step(0.01)
						->prefix('CHF')
						->suffix('/m²'),
				]),

			Section::make('Form')
				->collapsible()
				->collapsed()
				->columns(2)
				->schema([
					Select::make('shape')
						->label('Tischform')
						->options(collect(TableShape::cases())->mapWithKeys(fn ($shape) => [$shape->value => $shape->label()])),
					TextInput::make('form_surcharge')
						->label('Formzuschlag')
						->numeric()
						->step(0.01)
						->prefix('CHF'),
				]),

			Section::make('Holzarten')
				->collapsible()
				->collapsed()
				->visible(fn ($record) => $record !== null)
				->schema([
					Livewire::make(WoodTypesRelationManager::class, fn ($record) => [
						'ownerRecord' => $record,
						'pageClass' => EditProduct::class,
					]),
				]),

			Section::make('Oberflächen')
				->collapsible()
				->collapsed()
				->visible(fn ($record) => $record !== null)
				->schema([
					Livewire::make(SurfacesRelationManager::class, fn ($record) => [
						'ownerRecord' => $record,
						'pageClass' => EditProduct::class,
					]),
				]),

			Section::make('Kanten')
				->collapsible()
				->collapsed()
				->visible(fn ($record) => $record !== null)
				->schema([
					Livewire::make(EdgesRelationManager::class, fn ($record) => [
						'ownerRecord' => $record,
						'pageClass' => EditProduct::class,
					]),
				]),
		];
	}

	protected static function variationsTab(): array
	{
		return [
			Section::make('Produktvarianten')
				->collapsible()
				->visible(fn ($record) => $record !== null)
				->schema([
					Livewire::make(ChildrenRelationManager::class, fn ($record) => [
						'ownerRecord' => $record,
						'pageClass' => EditProduct::class,
					]),
				]),
		];
	}

	protected static function imagesTab(): array
	{
		return [
			Repeater::make('images')
				->label('Bilder')
				->relationship('images')
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

					Toggle::make('preview')
						->label('Vorschaubild')
						->helperText('Dieses Bild wird in der Kategorieansicht angezeigt')
						->inline(false)
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
		];
	}

	protected static function settingsTab(): array
	{
		return [
			Toggle::make('published')
				->label('Publizieren')
				->inline(false)
				->default(false),

			Select::make('type')
				->label('Typ')
				->options(collect(ProductType::cases())->mapWithKeys(fn ($type) => [$type->value => $type->label()]))
				->default(ProductType::Simple->value)
				->required()
				->live(),

			Section::make('Kategorien / Tags')
				->schema([
					CheckboxList::make('categories')
						->label('Kategorien')
						->relationship('categories', 'name')
						->options(Category::pluck('name', 'id'))
						->columns(2)
						->live(),

					CheckboxList::make('tags')
						->label('Tags')
						->relationship('tags', 'name')
						->options(function (callable $get) {
							$categoryIds = $get('categories');
							if (empty($categoryIds)) {
								return [];
							}
							return Tag::whereIn('category_id', $categoryIds)
								->orderBy('order')
								->pluck('name', 'id');
						})
						->columns(2)
						->visible(fn (callable $get) => !empty($get('categories'))),
				])
				->collapsible(),

			Section::make('Versandarten')
				->schema([
					CheckboxList::make('shippingMethods')
						->label('Versandarten')
						->relationship('shippingMethods', 'name')
						->options(ShippingMethod::orderBy('order')->pluck('name', 'id'))
						->columns(1)
						->required()
						->minItems(1)
						->validationMessages([
							'required' => 'Bitte wählen Sie mindestens eine Versandart aus.',
							'min' => 'Bitte wählen Sie mindestens eine Versandart aus.',
						]),
				])
				->collapsible(),
		];
	}
}
