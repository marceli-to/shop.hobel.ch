<?php

namespace App\Filament\Admin\Resources\Surfaces\Pages;

use App\Filament\Admin\Resources\Surfaces\SurfaceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSurface extends CreateRecord
{
	protected static string $resource = SurfaceResource::class;

	protected function getRedirectUrl(): string
	{
		return $this->getResource()::getUrl('index');
	}
}
