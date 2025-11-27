<?php

namespace App\Filament\Admin\Resources\Categories\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CategoryForm
{
	public static function configure(Schema $schema): Schema
	{
		return $schema
			->columns(3)
			->components([
				// Main Section - Kategorie
				Section::make('Kategorie')
					->schema([
						TextInput::make('name')
							->label('Name')
							->required()
							->live(onBlur: true)
							->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),

						TextInput::make('slug')
							->label('Slug')
							->required()
							->disabled()
							->dehydrated()
							->unique(ignoreRecord: true),
					])
					->columnSpan(2),
			]);
	}
}
