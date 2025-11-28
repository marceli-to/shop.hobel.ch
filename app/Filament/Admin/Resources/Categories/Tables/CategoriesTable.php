<?php

namespace App\Filament\Admin\Resources\Categories\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CategoriesTable
{
	public static function configure(Table $table): Table
	{
		return $table
			->columns([
				ImageColumn::make('media.file_path')
					->label('Bild')
					->disk('public')
					->size(40)
					->getStateUsing(function ($record) {
						return $record->media()->orderBy('order')->first()?->file_path;
					}),
				TextColumn::make('name')
					->label('Name')
					->searchable()
					->sortable(),
				TextColumn::make('products_count')
					->label('Produkte')
					->counts('products')
					->sortable(),
				IconColumn::make('featured')
					->label('Featured')
					->boolean()
					->sortable(),
				TextColumn::make('uuid')
					->label('UUID')
					->searchable()
					->toggleable(isToggledHiddenByDefault: true),
				TextColumn::make('slug')
					->label('Slug')
					->searchable()
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
