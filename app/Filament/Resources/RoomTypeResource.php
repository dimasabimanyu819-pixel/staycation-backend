<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomTypeResource\Pages;
use App\Models\RoomType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

// --- PASTIKAN DAFTAR INI ADA SEMUA ---
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput; // <--- INI YANG HILANG TADI
use Filament\Forms\Components\Select;    // <--- INI YANG BARU KITA TAMBAH
use Filament\Forms\Components\Textarea;  // (Jaga-jaga kalau Anda pakai description)
use Filament\Forms\Components\FileUpload; // (Jaga-jaga kalau Anda pakai upload foto)
// -------------------------------------

class RoomTypeResource extends Resource
{
    // ... kode di bawahnya biarkan saja ...
    protected static ?string $model = RoomType::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static ?string $navigationLabel = 'Tipe Kamar';
    
    protected static ?string $pluralModelLabel = 'Data Kamar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- BAGIAN 1: INFORMASI UMUM ---
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Select::make('apartment_id')
    ->relationship('apartment', 'name') // Mengambil data dari tabel apartments
    ->label('Lokasi Apartemen')
    ->searchable()
    ->preload()
    ->required(), // Wajib diisi agar sistem tidak bingung

                            TextInput::make('name')
                            ->required()
                            ->maxLength(255)
                            ->label('Nama Tipe Kamar'),
                        
                        Forms\Components\FileUpload::make('image')
                            ->image()
                            ->directory('room-types')
                            ->label('Foto Kamar'),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi / Fasilitas')
                            ->rows(3),
                    ]),

                // --- BAGIAN 2: HARGA UTAMA (FOKUS 3 JAM) ---
                Forms\Components\Section::make('Setting Harga Utama')
                    ->description('Harga yang tampil pertama kali di website (Default 3 Jam).')
                    ->schema([
                        // 1. INI JADI INPUT UTAMA SEKARANG
                        Forms\Components\TextInput::make('price_3_hours')
                            ->required() // Kita paksa isi biar tidak Rp 0
                            ->numeric()
                            ->prefix('Rp')
                            ->label('Harga Paket 3 Jam (UTAMA)')
                            ->helperText('Harga ini yang akan muncul pertama kali di web.'),

                        // 2. Tambahan Weekend
                        Forms\Components\TextInput::make('weekend_price')
                            ->numeric()
                            ->prefix('Rp')
                            ->label('Tambahan Weekend (+)')
                            ->helperText('Ditambahkan otomatis saat hari Sabtu/Minggu.')
                            ->default(0),
                    ])->columns(2),

                // --- BAGIAN 3: HARGA DURASI LAINNYA ---
                Forms\Components\Section::make('Harga Durasi Lain (Opsional)')
                    ->schema([
                        Forms\Components\TextInput::make('price_6_hours')->numeric()->prefix('Rp')->label('6 Jam'),
                        Forms\Components\TextInput::make('price_9_hours')->numeric()->prefix('Rp')->label('9 Jam'),
                        
                        // Harga 12 Jam (Dulu Default)
                        Forms\Components\TextInput::make('price_12_hours')->numeric()->prefix('Rp')->label('12 Jam'),
                        
                        Forms\Components\TextInput::make('price_24_hours')->numeric()->prefix('Rp')->label('24 Jam (Full Day)'),

                        // Kolom 'price' (Backup Database) - Wajib ada biar tidak error
                        Forms\Components\TextInput::make('price')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->label('Harga Sistem (Backup)')
                            ->helperText('Isi sama dengan harga 12 jam atau biarkan sebagai cadangan.'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Foto'),

                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->label('Nama Kamar'),

                // ...
Tables\Columns\TextColumn::make('name')->label('Nama Kamar')->searchable(),

// --- TAMBAHKAN INI ---
Tables\Columns\TextColumn::make('apartment.name') // <--- "Ambil nama dari relasi apartment"
    ->label('Lokasi Apartemen')
    ->sortable()
    ->badge() // Opsional: Biar tampilannya kayak badge warna-warni
    ->color('info'),

Tables\Columns\TextColumn::make('price')->money('IDR')->label('Harga 3 Jam'),
// ...

                // Tampilkan Harga 3 Jam di Tabel Depan biar gampang ngecek
                Tables\Columns\TextColumn::make('price_3_hours')
                    ->money('IDR')
                    ->label('Harga 3 Jam')
                    ->sortable(),

                Tables\Columns\TextColumn::make('weekend_price')
                    ->money('IDR')
                    ->label('Extra Weekend'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
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
            'index' => Pages\ListRoomTypes::route('/'),
            'create' => Pages\CreateRoomType::route('/create'),
            'edit' => Pages\EditRoomType::route('/{record}/edit'),
        ];
    }
}