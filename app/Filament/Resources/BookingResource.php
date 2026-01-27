<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Card;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar'; // Ikon Duit
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $navigationLabel = 'Data Booking';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    // --- SEKSI 1: DATA TAMU ---
                    TextInput::make('customer_name')
                        ->label('Nama Tamu')
                        ->required(),
                    TextInput::make('customer_phone')
                        ->label('WhatsApp')
                        ->tel()
                        ->required(),
                    TextInput::make('guest_attire')
                        ->label('Pakaian/Ciri-ciri')
                        ->placeholder('Contoh: Kemeja Hitam'),
                    TextInput::make('total_guests')
                        ->label('Jumlah Orang')
                        ->numeric()
                        ->default(2),

                    // --- SEKSI 2: DATA BOOKING ---
                    Select::make('unit_id')
                        ->label('Pilih Unit Kamar')
                        ->relationship('unit', 'unit_number')
                        ->required()
                        ->searchable()
                        ->preload(),
                    
                    DateTimePicker::make('start_time')
                        ->label('Waktu Check-in')
                        ->required(),

                    Select::make('duration')
                        ->label('Durasi Paket')
                        ->options([
                            3 => '3 Jam',
                            6 => '6 Jam',
                            9 => '9 Jam',
                            12 => '12 Jam',
                            24 => '24 Jam (Full Day)',
                        ])
                        ->required(),

                    TextInput::make('total_price')
                        ->label('Total Harga (Rp)')
                        ->numeric()
                        ->prefix('Rp')
                        ->required(),
                    
                    // --- SEKSI 3: STATUS & BUKTI ---
                    Select::make('status')
                        ->label('Status Booking')
                        ->options([
                            'pending' => 'Pending (Belum Bayar)',
                            'confirmed' => 'Confirmed (Lunas)',
                            'cancelled' => 'Batal',
                        ])
                        ->default('pending')
                        ->required(),

                    FileUpload::make('payment_proof')
                        ->label('Bukti Transfer (Kosongkan jika Cash)')
                        ->image()
                        ->directory('payments')
                        ->visibility('public')
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // 1. Check-in
                TextColumn::make('start_time')
                    ->label('Check-in')
                    ->dateTime('d M, H:i')
                    ->sortable(),

                // 2. Tamu & WA
                TextColumn::make('customer_name')
                    ->label('Tamu')
                    ->description(fn (Booking $record): string => $record->customer_phone)
                    ->searchable(),

                // --- [BARU] 3. LOKASI APARTEMEN ---
                TextColumn::make('roomType.apartment.name')
                    ->label('Apartemen')
                    ->badge()           // Tampil ala badge
                    ->color('info')     // Warna Biru
                    ->placeholder('-')  // Jika kosong (booking lama), muncul strip
                    ->sortable(),

                // 4. Nomor Unit
                TextColumn::make('unit.unit_number')
                    ->label('Unit')
                    ->badge()
                    ->color('gray')
                    ->searchable(), // Saya tambahkan searchable biar bisa cari nomor unit

                // --- [BARU] 5. DURASI ---
                TextColumn::make('duration')
                    ->label('Durasi')
                    ->suffix(' Jam')    // Tambah tulisan " Jam" di belakang angka
                    ->alignCenter()     // Rata tengah biar rapi
                    ->sortable(),

                // 6. Total Harga
                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),

                // 7. METODE BAYAR (Kolom Virtual)
                TextColumn::make('payment_method_virtual')
                    ->label('Metode')
                    ->getStateUsing(function (Booking $record) {
                        return $record->payment_proof ? 'Transfer' : 'Cash';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Transfer' => 'info',    
                        'Cash'     => 'success', 
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Transfer' => 'heroicon-o-credit-card', 
                        'Cash'     => 'heroicon-o-banknotes',   
                    }),

                // 8. Bukti Foto
                ImageColumn::make('payment_proof')
                    ->label('Bukti')
                    ->circular()
                    ->stacked()
                    ->limit(1)
                    ->placeholder('-'), 

                // 9. Status
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'confirmed' => 'success',
                        'pending'   => 'warning',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'confirmed' => 'Confirmed',
                        'cancelled' => 'Cancelled',
                    ]),
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
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }
}