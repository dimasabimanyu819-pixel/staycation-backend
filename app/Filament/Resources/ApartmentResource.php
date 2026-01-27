<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApartmentResource\Pages;
use App\Filament\Resources\ApartmentResource\RelationManagers;
use App\Models\Apartment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Card;

class ApartmentResource extends Resource
{
    protected static ?string $model = Apartment::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
{
    return $form
        ->schema([
            Card::make()->schema([
                TextInput::make('name')
                    ->label('Nama Apartemen')
                    ->required()
                    ->placeholder('Contoh: Apartemen Grand Center'),

                TextInput::make('address')
                    ->label('Alamat Lengkap')
                    ->placeholder('Jl. Jendral Sudirman No. 1...'),
            ])
        ]);
}

// Cari juga bagian table() di bawahnya untuk menampilkan kolom di list
public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')->sortable()->searchable()->label('Nama'),
            Tables\Columns\TextColumn::make('address')->label('Alamat'),
        ])
        ->filters([]); // biarkan default
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
            'index' => Pages\ListApartments::route('/'),
            'create' => Pages\CreateApartment::route('/create'),
            'edit' => Pages\EditApartment::route('/{record}/edit'),
        ];
    }
}
