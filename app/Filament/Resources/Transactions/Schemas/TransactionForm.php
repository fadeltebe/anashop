<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('customer_id')
                    ->relationship('customer', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                DatePicker::make('transaction_date')
                    ->required(),
                TextInput::make('total')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('discount')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('additional_fee')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('grand_total')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                Radio::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Diantar' => 'Diantar',
                        'Selesai' => 'Selesai',
                        'Batal' => 'Batal',
                    ])
                    ->default('pending'),
                Select::make('payment_method')
                    ->options([
                        'qris' => 'QRIS',
                        'transfer' => 'Transfer',
                        'e_wallet' => 'E-Wallet',
                        'cod' => 'COD',
                    ])
                    ->nullable(),
                Select::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ])
                    ->default('pending'),
                FileUpload::make('payment_proof')
                    ->nullable()
                    ->maxSize(2048) // Maksimum 2MB per foto
                    ->directory('payment-proofs')
                    ->disk('public')
                    ->multiple()
                    ->reorderable()
                    ->panelLayout('grid') // tampil dalam grid
                    ->visibility('public')
                    ->columnSpanFull(),
                TextInput::make('note')
                    ->nullable()
                    ->maxLength(1000)
                    ->columnSpanFull(),
                TextInput::make('admin_note')
                    ->nullable()
                    ->maxLength(1000)
                    ->columnSpanFull(),
            ]);
    }
}
