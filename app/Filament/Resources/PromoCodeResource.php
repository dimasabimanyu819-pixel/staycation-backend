<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PromoCodeResource\Pages;
use App\Models\PromoCode;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;

class PromoCodeResource extends Resource
{
    protected static ?string $model = PromoCode::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket'; // Ikon Tiket
    protected static ?string $navigationGroup = 'Marketing'; // Menu Group Baru
    protected static ?string $navigationLabel = 'Kode Promo';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    // 1. Input Kode Promo
                    TextInput::make('code')
                        ->label('Kode Promo')
                        ->required()
                        ->unique(ignoreRecord: true) // Mencegah kode kembar
                        ->placeholder('Contoh: MERDEKA45')
                        ->helperText('Gunakan HURUF KAPITAL tanpa spasi untuk hasil terbaik.')
                        ->maxLength(20),

                    // 2. Input Nominal Diskon
                    TextInput::make('discount_amount')
                        ->label('Nominal Potongan (Rp)')
                        ->required()
                        ->numeric()
                        ->prefix('Rp') // Tanda Mata Uang
                        ->placeholder('10000'),

                    // 3. Status Aktif
                    Toggle::make('is_active')
                        ->label('Aktifkan Kode Ini?')
                        ->onColor('success')
                        ->offColor('danger')
                        ->default(true)
                        ->helperText('Jika dimatikan, kode tidak bisa digunakan oleh tamu.'),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Kolom Kode
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->weight('bold')
                    ->copyable() // Biar admin gampang copy kode
                    ->sortable(),

                // Kolom Diskon
                TextColumn::make('discount_amount')
                    ->label('Besar Diskon')
                    ->money('IDR')
                    ->sortable(),

                // Kolom Toggle Status (Bisa on/off langsung dari tabel)
                ToggleColumn::make('is_active')
                    ->label('Status Aktif'),
                
                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->label('Dibuat Tanggal')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filter biar bisa lihat mana yang aktif/tidak
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Filter Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPromoCodes::route('/'),
            'create' => Pages\CreatePromoCode::route('/create'),
            'edit' => Pages\EditPromoCode::route('/{record}/edit'),
        ];
    }
}