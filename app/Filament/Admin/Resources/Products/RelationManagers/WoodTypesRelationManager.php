<?php

namespace App\Filament\Admin\Resources\Products\RelationManagers;

use App\Enums\ProductType;
use App\Models\WoodType;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class WoodTypesRelationManager extends RelationManager
{
	protected static string $relationship = 'woodTypes';

	protected static ?string $recordTitleAttribute = 'name';

	protected static ?string $title = 'Holzarten';

	protected static ?string $modelLabel = 'Holzart';

	protected static ?string $pluralModelLabel = 'Holzarten';

	public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
	{
		return $ownerRecord->type === ProductType::Configurable;
	}

	public function table(Table $table): Table
	{
		return $table
			->defaultSort('order', 'asc')
			->columns([
				ImageColumn::make('image.file_path')
					->label('Bild')
					->disk('public')
					->circular(),
				TextColumn::make('name')
					->label('Name')
					->searchable()
					->sortable(),
				TextColumn::make('price')
					->label('Preis/m³')
					->formatStateUsing(fn ($state) => number_format($state, 2, '.', '\''))
					->sortable(),
				TextColumn::make('sorting_factor')
					->label('Sortier-/Verschnittfaktor')
					->formatStateUsing(fn ($state) => number_format($state, 2, '.', '\''))
					->sortable(),
			])
			->headerActions([
				Action::make('attach')
					->label('Holzart hinzufügen')
					->modalHeading('Holzarten hinzufügen')
					->modalSubmitActionLabel('Hinzufügen')
					->modalWidth('4xl')
					->schema([
						CheckboxList::make('records')
							->label('Holzarten')
							->options(fn () => WoodType::orderBy('order')
								->whereNotIn('id', $this->getOwnerRecord()->woodTypes()->pluck('wood_types.id'))
								->pluck('name', 'id'))
							->descriptions(fn () => WoodType::orderBy('order')
								->whereNotIn('id', $this->getOwnerRecord()->woodTypes()->pluck('wood_types.id'))
								->get()
								->mapWithKeys(fn (WoodType $woodType) => [
									$woodType->id => number_format($woodType->price, 2, '.', "'").' CHF/m³ · Faktor '.number_format($woodType->sorting_factor, 2, '.', "'"),
								])
								->all())
							->columns(3)
							->required(),
					])
					->action(function (array $data): void {
						$this->getOwnerRecord()->woodTypes()->syncWithoutDetaching($data['records']);
					}),
			])
			->recordActions([
				DetachAction::make()
					->label('Entfernen'),
			])
			->toolbarActions([
				BulkActionGroup::make([
					DetachBulkAction::make(),
				]),
			]);
	}
}
