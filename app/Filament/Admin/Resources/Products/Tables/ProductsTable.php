<?php

namespace App\Filament\Admin\Resources\Products\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductsTable
{
	public static function configure(Table $table): Table
	{
		return $table
			->columns([
				ImageColumn::make('image')
					->label('Bild')
					->disk('public')
					->circular()
					->size(60),
				TextColumn::make('name')
					->label('Titel')
					->searchable()
					->sortable(),
				TextColumn::make('description')
					->label('Beschreibung')
					->searchable()
					->limit(50),
				TextColumn::make('price')
					->label('Preis')
					->formatStateUsing(fn ($state) => 'CHF ' . number_format($state, 2, '.', '\''))
					->sortable(),
				TextColumn::make('stock')
					->label('Verfügbar')
					->numeric()
					->sortable(),
				TextColumn::make('uuid')
					->label('UUID')
					->searchable()
					->toggleable(isToggledHiddenByDefault: true),
				TextColumn::make('slug')
					->searchable()
					->toggleable(isToggledHiddenByDefault: true),
				TextColumn::make('created_at')
					->label('Erstellt')
					->dateTime()
					->sortable()
					->toggleable(isToggledHiddenByDefault: true),
				TextColumn::make('updated_at')
					->label('Aktualisiert')
					->dateTime()
					->sortable()
					->toggleable(isToggledHiddenByDefault: true),
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
