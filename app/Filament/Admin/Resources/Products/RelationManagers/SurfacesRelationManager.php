<?php

namespace App\Filament\Admin\Resources\Products\RelationManagers;

use App\Enums\ProductType;
use App\Models\Surface;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SurfacesRelationManager extends RelationManager
{
	protected static string $relationship = 'surfaces';

	protected static ?string $recordTitleAttribute = 'name';

	protected static ?string $title = 'Oberflächen';

	protected static ?string $modelLabel = 'Oberfläche';

	protected static ?string $pluralModelLabel = 'Oberflächen';

	public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
	{
		return $ownerRecord->type === ProductType::Configurable;
	}

	public function table(Table $table): Table
	{
		return $table
			->defaultSort('order', 'asc')
			->columns([
				TextColumn::make('name')
					->label('Name')
					->searchable()
					->sortable(),
				TextColumn::make('price')
					->label('Preis/m²')
					->formatStateUsing(fn ($state) => number_format($state, 2, '.', '\''))
					->sortable(),
				TextColumn::make('minimum_amount')
					->label('Mindestbetrag')
					->formatStateUsing(fn ($state) => number_format($state, 2, '.', '\''))
					->sortable(),
				TextColumn::make('is_default')
					->label('Standard')
					->state(fn ($record) => $record->pivot->is_default ? 'Standard' : null)
					->badge()
					->color('success'),
			])
			->headerActions([
				Action::make('attach')
					->label('Oberfläche hinzufügen')
					->modalHeading('Oberflächen hinzufügen')
					->modalSubmitActionLabel('Hinzufügen')
					->modalWidth('md')
					->schema([
						CheckboxList::make('records')
							->label('Oberflächen')
							->options(fn () => Surface::orderBy('order')
								->whereNotIn('id', $this->getOwnerRecord()->surfaces()->pluck('surfaces.id'))
								->pluck('name', 'id'))
							->required(),
					])
					->action(function (array $data): void {
						$this->getOwnerRecord()->surfaces()->syncWithoutDetaching($data['records']);
					}),
			])
			->recordActions([
				ActionGroup::make([
					Action::make('setDefault')
						->label('Als Standard setzen')
						->icon('heroicon-o-star')
						->visible(fn ($record) => ! $record->pivot->is_default)
						->action(function ($record): void {
							$owner = $this->getOwnerRecord();
							DB::transaction(function () use ($owner, $record) {
								DB::table('product_surface')
									->where('product_id', $owner->id)
									->update(['is_default' => false]);
								DB::table('product_surface')
									->where('product_id', $owner->id)
									->where('surface_id', $record->id)
									->update(['is_default' => true]);
							});
						}),
					DetachAction::make()
						->label('Löschen')
						->icon('heroicon-o-trash'),
				]),
			])
			->toolbarActions([
				BulkActionGroup::make([
					DetachBulkAction::make(),
				]),
			]);
	}
}
