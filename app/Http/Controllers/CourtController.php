<?php

namespace App\Http\Controllers;

use App\Models\Court;
use Illuminate\View\View;

class CourtController extends Controller
{
    /**
     * Menampilkan halaman detail lapangan publik, fasilitas, dan galeri foto.
     */
    public function show(Court $court): View
    {
        if ($court->status !== 'active') {
            abort(404, 'Lapangan sedang tidak aktif atau dalam pemeliharaan.');
        }

        $court->load([
            'sport',
            'venue.operatingHours',
            'images',
            'facilities',
        ]);

        return view('courts.show', compact('court'));
    }
}
