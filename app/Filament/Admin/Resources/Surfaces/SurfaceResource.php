<?php

namespace App\Filament\Admin\Resources\Surfaces;

use App\Filament\Admin\Resources\Surfaces\Pages\CreateSurface;
use App\Filament\Admin\Resources\Surfaces\Pages\EditSurface;
use App\Filament\Admin\Resources\Surfaces\Pages\ListSurfaces;
use App\Filament\Admin\Resources\Surfaces\Schemas\SurfaceForm;
use App\Filament\Admin\Resources\Surfaces\Tables\SurfacesTable;
use App\Models\Surface;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SurfaceResource extends Resource
{
	protected static ?string $model = Surface::class;

	protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSwatch;

	protected static ?string $navigationLabel = 'Oberflächen';

	protected static ?string $modelLabel = 'Oberfläche';

	protected static ?string $pluralModelLabel = 'Oberflächen';

	protected static ?string $breadcrumb = 'Oberflächen';

	protected static string|\UnitEnum|null $navigationGroup = 'Einstellungen';

	protected static ?int $navigationSort = 6;

	public static function form(Schema $schema): Schema
	{
		return SurfaceForm::configure($schema);
	}

	public static function table(Table $table): Table
	{
		return SurfacesTable::configure($table);
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
			'index' => ListSurfaces::route('/'),
			'create' => CreateSurface::route('/create'),
			'edit' => EditSurface::route('/{record}/edit'),
		];
	}
}
