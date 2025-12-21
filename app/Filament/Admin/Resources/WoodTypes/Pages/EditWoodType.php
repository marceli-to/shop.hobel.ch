<?php

namespace App\Filament\Admin\Resources\WoodTypes\Pages;

use App\Filament\Admin\Resources\WoodTypes\WoodTypeResource;
use Filament\Resources\Pages\EditRecord;

class EditWoodType extends EditRecord
{
	protected static string $resource = WoodTypeResource::class;

	protected function getRedirectUrl(): string
	{
		return $this->getResource()::getUrl('index');
	}
}
