<?php

namespace App\Filament\Admin\Resources\Categories\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
	public static function configure(Table $table): Table
	{
		return $table
			->columns([
				TextColumn::make('name')
					->label('Name')
					->searchable()
					->sortable(),
				TextColumn::make('slug')
					->label('Slug')
					->searchable()
					->sortable(),
				TextColumn::make('products_count')
					->label('Produkte')
					->counts('products')
					->sortable(),
				TextColumn::make('description')
					->label('Beschreibung')
					->limit(50),
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
