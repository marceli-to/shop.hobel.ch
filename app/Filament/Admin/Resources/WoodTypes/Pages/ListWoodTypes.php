<?php

namespace App\Filament\Admin\Resources\WoodTypes\Pages;

use App\Filament\Admin\Resources\WoodTypes\WoodTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWoodTypes extends ListRecords
{
	protected static string $resource = WoodTypeResource::class;

	protected function getHeaderActions(): array
	{
		return [
			CreateAction::make()
				->label('Erstellen'),
		];
	}
}
