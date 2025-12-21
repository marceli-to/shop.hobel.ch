<?php

namespace App\Filament\Admin\Resources\WoodTypes;

use App\Filament\Admin\Resources\WoodTypes\Pages\CreateWoodType;
use App\Filament\Admin\Resources\WoodTypes\Pages\EditWoodType;
use App\Filament\Admin\Resources\WoodTypes\Pages\ListWoodTypes;
use App\Filament\Admin\Resources\WoodTypes\Schemas\WoodTypeForm;
use App\Filament\Admin\Resources\WoodTypes\Tables\WoodTypesTable;
use App\Models\WoodType;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class WoodTypeResource extends Resource
{
	protected static ?string $model = WoodType::class;

	protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquare3Stack3d;

	protected static ?string $navigationLabel = 'Holzarten';

	protected static ?string $modelLabel = 'Holzart';

	protected static ?string $pluralModelLabel = 'Holzarten';

	protected static ?string $breadcrumb = 'Holzarten';

	protected static ?int $navigationSort = 4;

	public static function form(Schema $schema): Schema
	{
		return WoodTypeForm::configure($schema);
	}

	public static function table(Table $table): Table
	{
		return WoodTypesTable::configure($table);
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
			'index' => ListWoodTypes::route('/'),
			'create' => CreateWoodType::route('/create'),
			'edit' => EditWoodType::route('/{record}/edit'),
		];
	}
}
