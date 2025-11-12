<?php

namespace App\Filament\Admin\Resources\Products\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductForm
{
	public static function configure(Schema $schema): Schema
	{
		return $schema
			->components([
				TextInput::make('uuid')
					->label('UUID')
					->required(),
				TextInput::make('name')
					->required(),
				TextInput::make('slug')
					->required(),
				Textarea::make('description')
					->columnSpanFull(),
				TextInput::make('price')
					->required()
					->numeric()
					->prefix('$'),
				TextInput::make('stock')
					->required()
					->numeric()
					->default(0),
				FileUpload::make('image')
					->disk('public')
					->directory('products')
					->visibility('public')
					->image()
					->imagePreviewHeight('250'),
				DateTimePicker::make('published_at'),
			]);
	}
}
