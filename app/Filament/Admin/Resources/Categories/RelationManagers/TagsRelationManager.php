<?php

namespace App\Filament\Admin\Resources\Categories\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;

class TagsRelationManager extends RelationManager
{
	protected static string $relationship = 'tags';

	protected static ?string $title = 'Tags';

	protected static ?string $modelLabel = 'Tag';

	protected static ?string $pluralModelLabel = 'Tags';

	public function form(Schema $schema): Schema
	{
		return $schema
			->components([
				TextInput::make('name')
					->label('Name')
					->required()
					->columnSpanFull(),
			]);
	}

	public function table(Table $table): Table
	{
		return $table
			->reorderable('order')
			->defaultSort('order', 'asc')
			->columns([
				TextColumn::make('name')
					->label('Name')
					->searchable()
					->sortable(),
				TextColumn::make('slug')
					->label('Slug')
					->toggleable(isToggledHiddenByDefault: true),
			])
			->headerActions([
				CreateAction::make()
					->label('Tag erstellen')
					->modalWidth('lg'),
			])
			->actions([
				ActionGroup::make([
					EditAction::make()
						->label('Bearbeiten')
						->modalWidth('lg'),
					DeleteAction::make()
						->label('Löschen'),
				]),
			])
			->bulkActions([
				BulkActionGroup::make([
					DeleteBulkAction::make(),
				]),
			]);
	}
}
