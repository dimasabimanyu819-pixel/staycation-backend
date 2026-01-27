<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function export_csv()
    {
        $filename = "laporan-booking-" . date('Y-m-d-His') . ".csv";
        
        $headers = [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $columns = ['ID Invoice', 'Nama Tamu', 'WhatsApp', 'Unit', 'Check-in', 'Durasi', 'Total (Rp)', 'Status', 'Metode Bayar'];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            $bookings = Booking::with('unit')->latest()->get();

            foreach ($bookings as $row) {
                fputcsv($file, [
                    '#' . $row->id,
                    $row->customer_name,
                    $row->customer_phone,
                    $row->unit ? $row->unit->unit_number : 'Deleted',
                    $row->start_time,
                    $row->duration . ' Jam',
                    $row->total_price,
                    strtoupper($row->status),
                    $row->payment_proof ? 'Transfer' : 'Cash'
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}