<?php

namespace App\Filament\Admin\Resources\Orders\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrdersTable
{
	public static function configure(Table $table): Table
	{
		return $table
			->columns([
				TextColumn::make('uuid')
					->label('UUID')
					->searchable(),
				TextColumn::make('customer_name')
					->searchable(),
				TextColumn::make('customer_email')
					->searchable(),
				TextColumn::make('customer_phone')
					->searchable(),
				TextColumn::make('invoice_company')
					->searchable(),
				TextColumn::make('invoice_street')
					->searchable(),
				TextColumn::make('invoice_street_number')
					->searchable(),
				TextColumn::make('invoice_zip')
					->searchable(),
				TextColumn::make('invoice_city')
					->searchable(),
				TextColumn::make('invoice_country')
					->searchable(),
				IconColumn::make('use_invoice_address')
					->boolean(),
				TextColumn::make('shipping_company')
					->searchable(),
				TextColumn::make('shipping_street')
					->searchable(),
				TextColumn::make('shipping_street_number')
					->searchable(),
				TextColumn::make('shipping_zip')
					->searchable(),
				TextColumn::make('shipping_city')
					->searchable(),
				TextColumn::make('shipping_country')
					->searchable(),
				TextColumn::make('total')
					->numeric()
					->sortable(),
				TextColumn::make('payment_method')
					->searchable(),
				TextColumn::make('paid_at')
					->dateTime()
					->sortable(),
				TextColumn::make('created_at')
					->dateTime()
					->sortable()
					->toggleable(isToggledHiddenByDefault: true),
				TextColumn::make('updated_at')
					->dateTime()
					->sortable()
					->toggleable(isToggledHiddenByDefault: true),
			])
			->filters([
				//
			])
			->recordActions([
				ViewAction::make(),
				EditAction::make(),
			])
			->toolbarActions([
				BulkActionGroup::make([
					DeleteBulkAction::make(),
				]),
			]);
	}
}
