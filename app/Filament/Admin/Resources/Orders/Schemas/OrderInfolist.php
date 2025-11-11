<?php

namespace App\Filament\Admin\Resources\Orders\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
	public static function configure(Schema $schema): Schema
	{
		return $schema
			->components([
				TextEntry::make('uuid')
					->label('UUID'),
				TextEntry::make('customer_name'),
				TextEntry::make('customer_email'),
				TextEntry::make('customer_phone')
					->placeholder('-'),
				TextEntry::make('invoice_company')
					->placeholder('-'),
				TextEntry::make('invoice_street'),
				TextEntry::make('invoice_street_number'),
				TextEntry::make('invoice_zip'),
				TextEntry::make('invoice_city'),
				TextEntry::make('invoice_country'),
				IconEntry::make('use_invoice_address')
					->boolean(),
				TextEntry::make('shipping_company')
					->placeholder('-'),
				TextEntry::make('shipping_street')
					->placeholder('-'),
				TextEntry::make('shipping_street_number')
					->placeholder('-'),
				TextEntry::make('shipping_zip')
					->placeholder('-'),
				TextEntry::make('shipping_city')
					->placeholder('-'),
				TextEntry::make('shipping_country')
					->placeholder('-'),
				TextEntry::make('total')
					->numeric(),
				TextEntry::make('payment_method')
					->placeholder('-'),
				TextEntry::make('paid_at')
					->dateTime()
					->placeholder('-'),
				TextEntry::make('created_at')
					->dateTime()
					->placeholder('-'),
				TextEntry::make('updated_at')
					->dateTime()
					->placeholder('-'),
			]);
	}
}
