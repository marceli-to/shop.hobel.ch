<?php

namespace App\Filament\Admin\Resources\Edges\Pages;

use App\Filament\Admin\Resources\Edges\EdgeResource;
use Filament\Resources\Pages\EditRecord;

class EditEdge extends EditRecord
{
	protected static string $resource = EdgeResource::class;

	protected function getRedirectUrl(): string
	{
		return $this->getResource()::getUrl('index');
	}
}
