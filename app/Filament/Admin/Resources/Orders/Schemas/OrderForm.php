<?php

namespace App\Filament\Admin\Resources\Orders\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class OrderForm
{
	public static function configure(Schema $schema): Schema
	{
		return $schema
			->components([
				TextInput::make('uuid')
					->label('UUID')
					->required(),
				TextInput::make('customer_name')
					->required(),
				TextInput::make('customer_email')
					->email()
					->required(),
				TextInput::make('customer_phone')
					->tel(),
				TextInput::make('invoice_company'),
				TextInput::make('invoice_street')
					->required(),
				TextInput::make('invoice_street_number')
					->required(),
				TextInput::make('invoice_zip')
					->required(),
				TextInput::make('invoice_city')
					->required(),
				TextInput::make('invoice_country')
					->required()
					->default('CH'),
				Toggle::make('use_invoice_address')
					->required(),
				TextInput::make('shipping_company'),
				TextInput::make('shipping_street'),
				TextInput::make('shipping_street_number'),
				TextInput::make('shipping_zip'),
				TextInput::make('shipping_city'),
				TextInput::make('shipping_country'),
				TextInput::make('total')
					->required()
					->numeric(),
				TextInput::make('payment_method'),
				DateTimePicker::make('paid_at'),
			]);
	}
}
