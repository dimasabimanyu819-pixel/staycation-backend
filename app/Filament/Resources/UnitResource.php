<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitResource\Pages;
use App\Models\Unit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\TextColumn;

class UnitResource extends Resource
{
    protected static ?string $model = Unit::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Unit Kamar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    // 1. DROPDOWN TIPE KAMAR
                    Select::make('room_type_id')
                        ->label('Tipe Kamar')
                        ->relationship('roomType', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    // 2. INPUT NOMOR UNIT
                    TextInput::make('unit_number')
                        ->label('Nomor Pintu / Unit')
                        ->placeholder('Contoh: 101, 202, A1')
                        ->required()
                        ->maxLength(255),

                    // 3. STATUS
                    Select::make('status')
                        ->label('Status Saat Ini')
                        ->options([
                            'available' => 'Tersedia (Available)',
                            'booked' => 'Sedang Dipakai (Booked)',
                            'maintenance' => 'Perbaikan (Maintenance)',
                        ])
                        ->default('available')
                        ->required(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // K1. Nomor Unit
                TextColumn::make('unit_number')
                    ->label('Nomor Unit')
                    ->sortable()
                    ->searchable()
                    ->weight('bold'),

                // --- [BARU] KOLOM LOKASI APARTEMEN ---
                // Mengambil data dari Unit -> RoomType -> Apartment -> Name
                TextColumn::make('roomType.apartment.name')
                    ->label('Lokasi Apartemen')
                    ->sortable()     // Bisa diurutkan per apartemen
                    ->searchable()   // Bisa dicari nama apartemennya
                    ->badge()        // Tampil sebagai badge kotak kecil
                    ->color('info')  // Warna Biru Muda
                    ->placeholder('-'), // Jika belum diset, muncul strip

                // K2. Tipe Kamar
                TextColumn::make('roomType.name')
                    ->label('Tipe Kamar')
                    ->sortable()
                    ->searchable(),

                // K3. Status (Dengan Logic Anti Error Warna)
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'available' => 'success',   // Hijau
                        'active'    => 'success',   // Handle active jadi hijau
                        'booked'    => 'danger',    // Merah
                        'maintenance' => 'warning', // Kuning
                        default => 'gray',          
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'available' => 'Tersedia',
                        'active'    => 'Tersedia',
                        'booked'    => 'Terisi',
                        'maintenance' => 'Perbaikan',
                        default => $state,
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('room_type_id')
                    ->label('Filter Tipe Kamar')
                    ->relationship('roomType', 'name'),
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
            'index' => Pages\ListUnits::route('/'),
            'create' => Pages\CreateUnit::route('/create'),
            'edit' => Pages\EditUnit::route('/{record}/edit'),
        ];
    }
}