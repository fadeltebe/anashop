<?php

namespace App\Filament\Resources\Products\Tables;

use Filament\Tables\Table;
use Filament\Actions\EditAction;
// use Filament\Tables\Columns\Column;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ImageColumn;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\ToggleColumn;

// use Filament\Tables\Columns\Column;

class ProductsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                Split::make([
                    Stack::make([
                        ImageColumn::make('thumbnail')
                            ->disk('public')
                            // ->label('Gambar')
                            ->circular()
                            ->height(50)
                            ->width(50),


                        TextColumn::make('name')
                            ->searchable(),

                        TextColumn::make('category.name')
                            ->sortable()
                            ->badge(),

                    ]),

                    Stack::make([
                        TextColumn::make('code')
                            ->searchable(),
                        TextColumn::make('price')
                            ->numeric()
                            ->prefix('Rp ')
                            ->sortable(),
                        TextColumn::make('discount_price')
                            ->numeric()
                            ->prefix('Rp ')
                            ->sortable(),
                        TextColumn::make('stock')
                            ->numeric()
                            ->sortable()
                            ->formatStateUsing(fn($state) => str_pad('Stok', 15, ' ', STR_PAD_RIGHT) . $state),

                    ])
                    // ->extraHeaderAttributes([
                    //     'style' => 'position: sticky; left: 0; z-index: 1; background-color: #f9fafb;', // bg-gray-100
                    //     'class' => 'dark:bg-gray-900', // biar tetap support dark mode
                    // ])
                    // ->extraCellAttributes([
                    //     'style' => 'position: sticky; left: 0; z-index: 1; background-color: #ffffff;', // bg-white
                    //     'class' => 'dark:bg-gray-900',
                    // ])
                    ,

                    // Stack::make([
                    //     Stack::make([


                    //         TextColumn::make('total_sales')
                    //             ->numeric()
                    //             ->sortable()
                    //             ->formatStateUsing(fn($state) => str_pad('Terjual', 15, ' ', STR_PAD_RIGHT) . $state),

                    //         TextColumn::make('weight')
                    //             ->numeric()
                    //             ->sortable()
                    //             ->formatStateUsing(fn($state) => str_pad('Berat', 15, ' ', STR_PAD_RIGHT) . $state . ' g'),

                    //         TextColumn::make('rating')
                    //             ->numeric()
                    //             ->sortable()
                    //             ->formatStateUsing(fn($state) => str_pad('Rating', 15, ' ', STR_PAD_RIGHT) . $state),

                    //         TextColumn::make('rating_count')
                    //             ->numeric()
                    //             ->sortable()
                    //             ->formatStateUsing(fn($state) => str_pad('Jumlah Rating', 15, ' ', STR_PAD_RIGHT) . ': ' . $state),
                    //     ])

                    // ]),


                    Split::make([
                        // KIRI: label (gunakan kolom dummy, pakai 'id' sebagai sumber state agar ada nilai)
                        Stack::make([
                            TextColumn::make('id') // pakai kolom yang pasti ada
                                ->label('') // header kosong
                                ->formatStateUsing(fn($id, $record) => str_pad('Tampil?', 15, ' ', STR_PAD_RIGHT))
                                ->extraCellAttributes(['class' => 'text-right pr-2']),
                            TextColumn::make('id')
                                ->label('')
                                ->formatStateUsing(fn($id, $record) => str_pad('Live?', 15, ' ', STR_PAD_RIGHT))
                                ->extraCellAttributes(['class' => 'text-right pr-2']),
                            TextColumn::make('id')
                                ->label('')
                                ->formatStateUsing(fn($id, $record) => str_pad('Unggulan?', 15, ' ', STR_PAD_RIGHT))
                                ->extraCellAttributes(['class' => 'text-right pr-2']),
                            TextColumn::make('id')
                                ->label('')
                                ->formatStateUsing(fn($id, $record) => str_pad('Sale?', 15, ' ', STR_PAD_RIGHT))
                                ->extraCellAttributes(['class' => 'text-right pr-2']),
                        ]),

                        // KANAN: toggles interaktif (tanpa label)
                        Stack::make([
                            ToggleColumn::make('is_published')->label(''),
                            ToggleColumn::make('is_live')->label(''),
                            ToggleColumn::make('is_featured')->label(''),
                            ToggleColumn::make('is_flash_sale')->label(''),
                        ]),
                    ])->columnSpan(2), // agar tidak terlalu lebar
                ])->columnSpan(12), // full width
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
