<?php

namespace App\Filament\Admin\Resources\Edges;

use App\Filament\Admin\Resources\Edges\Pages\CreateEdge;
use App\Filament\Admin\Resources\Edges\Pages\EditEdge;
use App\Filament\Admin\Resources\Edges\Pages\ListEdges;
use App\Filament\Admin\Resources\Edges\Schemas\EdgeForm;
use App\Filament\Admin\Resources\Edges\Tables\EdgesTable;
use App\Models\Edge;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EdgeResource extends Resource
{
	protected static ?string $model = Edge::class;

	protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

	protected static ?string $navigationLabel = 'Kanten';

	protected static ?string $modelLabel = 'Kante';

	protected static ?string $pluralModelLabel = 'Kanten';

	protected static ?string $breadcrumb = 'Kanten';

	protected static string|\UnitEnum|null $navigationGroup = 'Einstellungen';

	protected static ?int $navigationSort = 8;

	public static function form(Schema $schema): Schema
	{
		return EdgeForm::configure($schema);
	}

	public static function table(Table $table): Table
	{
		return EdgesTable::configure($table);
	}

	public static function getRelations(): array
	{
		return [
			//
		];
	}

	public static function getPages(): array
	{
		return [
			'index' => ListEdges::route('/'),
			'create' => CreateEdge::route('/create'),
			'edit' => EditEdge::route('/{record}/edit'),
		];
	}
}
