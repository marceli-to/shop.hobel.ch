<?php

namespace App\Filament\Admin\Resources\Surfaces\Pages;

use App\Filament\Admin\Resources\Surfaces\SurfaceResource;
use Filament\Resources\Pages\EditRecord;

class EditSurface extends EditRecord
{
	protected static string $resource = SurfaceResource::class;

	protected function getRedirectUrl(): string
	{
		return $this->getResource()::getUrl('index');
	}
}
