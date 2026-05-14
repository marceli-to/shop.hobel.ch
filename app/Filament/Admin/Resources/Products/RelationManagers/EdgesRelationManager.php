<?php

namespace App\Filament\Admin\Resources\Products\RelationManagers;

use App\Enums\ProductType;
use App\Models\Edge;
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

class EdgesRelationManager extends RelationManager
{
	protected static string $relationship = 'edges';

	protected static ?string $recordTitleAttribute = 'name';

	protected static ?string $title = 'Kanten';

	protected static ?string $modelLabel = 'Kante';

	protected static ?string $pluralModelLabel = 'Kanten';

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
					->label('Preis/m')
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
					->label('Kante hinzufügen')
					->modalHeading('Kanten hinzufügen')
					->modalSubmitActionLabel('Hinzufügen')
					->modalWidth('md')
					->schema([
						CheckboxList::make('records')
							->label('Kanten')
							->options(fn () => Edge::orderBy('order')
								->whereNotIn('id', $this->getOwnerRecord()->edges()->pluck('edges.id'))
								->pluck('name', 'id'))
							->required(),
					])
					->action(function (array $data): void {
						$this->getOwnerRecord()->edges()->syncWithoutDetaching($data['records']);
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
								DB::table('edge_product')
									->where('product_id', $owner->id)
									->update(['is_default' => false]);
								DB::table('edge_product')
									->where('product_id', $owner->id)
									->where('edge_id', $record->id)
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
