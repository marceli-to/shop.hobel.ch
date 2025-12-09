<?php

namespace App\Filament\Admin\Resources\ShippingMethods\Pages;

use App\Filament\Admin\Resources\ShippingMethods\ShippingMethodResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditShippingMethod extends EditRecord
{
	protected static string $resource = ShippingMethodResource::class;

	protected function getHeaderActions(): array
	{
		return [];
	}

	protected function getFormActions(): array
	{
		return [
			...parent::getFormActions(),
			DeleteAction::make(),
		];
	}

	protected function getRedirectUrl(): string
	{
		return $this->getResource()::getUrl('index');
	}
}
