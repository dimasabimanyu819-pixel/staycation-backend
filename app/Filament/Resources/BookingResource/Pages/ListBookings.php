<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Create Bawaan
            Actions\CreateAction::make(),
            
            // TOMBOL EXPORT BARU KITA
            Action::make('export_excel')
                ->label('Download Laporan (CSV)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->url(route('export.bookings')) // Arahkan ke route yang kita buat tadi
                ->openUrlInNewTab(),
        ];
    }
}