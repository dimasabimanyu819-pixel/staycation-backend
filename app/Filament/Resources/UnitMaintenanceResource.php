<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitMaintenanceResource\Pages;
use App\Models\UnitMaintenance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\TextColumn;

class UnitMaintenanceResource extends Resource
{
    protected static ?string $model = UnitMaintenance::class;
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver'; 
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?string $navigationLabel = 'Jadwal Maintenance';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()->schema([
                    Select::make('unit_id')
                        ->label('Pilih Unit yang Rusak')
                        ->relationship('unit', 'unit_number')
                        ->searchable()
                        ->preload()
                        ->required(),
                    
                    TextInput::make('reason')
                        ->label('Alasan Perbaikan')
                        ->placeholder('Contoh: AC Bocor, Renovasi Cat')
                        ->required(),

                    DateTimePicker::make('start_time')
                        ->label('Mulai Perbaikan')
                        ->required(),

                    DateTimePicker::make('end_time')
                        ->label('Selesai Perbaikan')
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('unit.unit_number')->label('Unit')->sortable()->badge()->color('danger'),
                TextColumn::make('reason')->label('Alasan')->searchable(),
                TextColumn::make('start_time')->dateTime('d M Y, H:i')->label('Mulai'),
                TextColumn::make('end_time')->dateTime('d M Y, H:i')->label('Selesai'),
            ])
            ->defaultSort('start_time', 'desc')
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
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUnitMaintenances::route('/'),
            'create' => Pages\CreateUnitMaintenance::route('/create'),
            'edit' => Pages\EditUnitMaintenance::route('/{record}/edit'),
        ];
    }
}