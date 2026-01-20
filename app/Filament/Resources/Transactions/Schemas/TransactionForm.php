<?php

namespace App\Filament\Resources\Transactions\Schemas;

use Filament\Schemas\Schema;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ToggleButtons;

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
                ToggleButtons::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'processing' => 'Diantar',
                        'completed' => 'Selesai',
                        'cancelled' => 'Batal',
                    ])
                    ->colors([
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                    ])->grouped()
                    ->icons([
                        'pending' => 'heroicon-o-clock',
                        'processing' => 'heroicon-o-truck',
                        'completed' => 'heroicon-o-check-circle',
                        'cancelled' => 'heroicon-o-x-circle',
                    ])
                    ->default('pending'),
                ToggleButtons::make('payment_method')
                    ->options([
                        'qris' => 'QRIS',
                        'transfer' => 'Transfer',
                        'e_wallet' => 'E-Wallet',
                        'cod' => 'COD',
                    ])->grouped()
                    ->default('qris')
                    ->colors([
                        'qris' => 'primary',
                        'transfer' => 'secondary',
                        'e_wallet' => 'success',
                        'cod' => 'warning',
                    ])
                    ->nullable(),
                ToggleButtons::make('payment_status')
                    ->options([
                        'unpaid' => 'Belum Bayar',
                        'paid' => 'Lunas',
                        'refunded' => 'Dikembalikan',
                    ])->grouped()
                    ->colors([
                        'unpaid' => 'danger',
                        'paid' => 'success',
                        'refunded' => 'warning',
                    ])
                    ->default('unpaid'),
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
