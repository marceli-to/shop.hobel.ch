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
				TextEntry::make('invoice_salutation')
					->label('Anrede')
					->placeholder('-'),
				TextEntry::make('invoice_firstname')
					->label('Vorname'),
				TextEntry::make('invoice_lastname')
					->label('Nachname'),
				TextEntry::make('invoice_street')
					->label('Strasse'),
				TextEntry::make('invoice_street_number')
					->label('Nr.')
					->placeholder('-'),
				TextEntry::make('invoice_zip')
					->label('PLZ'),
				TextEntry::make('invoice_city')
					->label('Ort'),
				TextEntry::make('invoice_country')
					->label('Land'),
				TextEntry::make('invoice_email')
					->label('E-Mail'),
				TextEntry::make('invoice_phone')
					->label('Telefon')
					->placeholder('-'),
				IconEntry::make('use_invoice_address')
					->label('Lieferadresse = Rechnungsadresse')
					->boolean(),
				TextEntry::make('shipping_salutation')
					->label('Anrede (Lieferung)')
					->placeholder('-'),
				TextEntry::make('shipping_firstname')
					->label('Vorname (Lieferung)')
					->placeholder('-'),
				TextEntry::make('shipping_lastname')
					->label('Nachname (Lieferung)')
					->placeholder('-'),
				TextEntry::make('shipping_street')
					->label('Strasse (Lieferung)')
					->placeholder('-'),
				TextEntry::make('shipping_street_number')
					->label('Nr. (Lieferung)')
					->placeholder('-'),
				TextEntry::make('shipping_zip')
					->label('PLZ (Lieferung)')
					->placeholder('-'),
				TextEntry::make('shipping_city')
					->label('Ort (Lieferung)')
					->placeholder('-'),
				TextEntry::make('shipping_country')
					->label('Land (Lieferung)')
					->placeholder('-'),
				TextEntry::make('total')
					->label('Total')
					->numeric(),
				TextEntry::make('payment_method')
					->label('Zahlungsmethode')
					->placeholder('-'),
				TextEntry::make('payment_reference')
					->label('Zahlungsreferenz')
					->placeholder('-'),
				TextEntry::make('paid_at')
					->label('Bezahlt am')
					->dateTime()
					->placeholder('-'),
				TextEntry::make('created_at')
					->label('Erstellt am')
					->dateTime()
					->placeholder('-'),
				TextEntry::make('updated_at')
					->label('Aktualisiert am')
					->dateTime()
					->placeholder('-'),
			]);
	}
}
