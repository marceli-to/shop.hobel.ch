<?php

namespace App\Filament\Admin\Resources\Surfaces\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SurfacesTable
{
	public static function configure(Table $table): Table
	{
		return $table
			->reorderable('order')
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
			])
			->filters([
				//
			])
			->recordActions([
				ActionGroup::make([
					EditAction::make()
						->label('Bearbeiten'),
					DeleteAction::make()
						->label('Löschen'),
				]),
			])
			->toolbarActions([
				BulkActionGroup::make([
					DeleteBulkAction::make(),
				]),
			]);
	}
}
