<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    // Setting biar refresh otomatis tiap 15 detik (Realtime-ish)
    protected static ?string $pollingInterval = '15s'; 

    protected function getStats(): array
    {
        return [
            // KARTU 1: TOTAL OMZET (Hanya yang statusnya Confirmed)
            Stat::make('Total Omzet', 'Rp ' . number_format(Booking::where('status', 'confirmed')->sum('total_price'), 0, ',', '.'))
                ->description('Pemasukan bersih')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([7, 2, 10, 3, 15, 4, 17]) // Hiasan grafik
                ->color('success'),

            // KARTU 2: BOOKING PENDING (Perlu Tindakan)
            Stat::make('Butuh Verifikasi', Booking::where('status', 'pending')->count())
                ->description('Booking belum dibayar/dikonfirmasi')
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color('warning'),

            // KARTU 3: TOTAL TAMU
            Stat::make('Total Transaksi', Booking::count())
                ->description('Semua riwayat booking')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),
        ];
    }
}