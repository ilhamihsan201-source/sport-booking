<?php

namespace App\Http\Controllers;

use App\Models\Court;
use App\Models\Facility;
use App\Models\Sport;
use App\Models\Venue;

class HomeController extends Controller
{
    public function index()
    {
        // Kategori olahraga aktif beserta jumlah lapangannya.
        $sports = Sport::query()
            ->where('is_active', true)
            ->withCount([
                'courts' => fn ($query) => $query
                    ->where('status', 'active')
                    ->whereHas('venue', fn ($venue) => $venue->where('is_active', true)),
            ])
            ->orderBy('name')
            ->get();

        // Ambil kategori dari parameter URL.
        $selectedSport = $sports->firstWhere('slug', request('sport'));

        // Query katalog lapangan.
        $courtsQuery = Court::with([
            'sport',
            'venue',
            'images',
            'facilities',
        ])
            ->where('status', 'active')
            ->whereHas('venue', fn ($query) => $query->where('is_active', true))
            ->whereHas('sport', fn ($query) => $query->where('is_active', true));

        // Terapkan filter jika kategori valid.
        if ($selectedSport) {
            $courtsQuery->where('sport_id', $selectedSport->id);
        }

        $courts = $courtsQuery
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->get();

        // Statistik aktual dari database.
        $stats = [
            'courts' => Court::where('status', 'active')
                ->whereHas('venue', fn ($query) => $query->where('is_active', true))
                ->whereHas('sport', fn ($query) => $query->where('is_active', true))
                ->count(),

            'venues' => Venue::where('is_active', true)->count(),

            'sports' => $sports->count(),
        ];

        $facilities = Facility::orderBy('name')->get();

        $venue = Venue::where('is_active', true)
            ->with('operatingHours')
            ->first();

        $todayHours = $venue?->operatingHours
            ->firstWhere('day_of_week', now()->dayOfWeek);

        return view('home.index', compact(
            'sports',
            'courts',
            'facilities',
            'venue',
            'stats',
            'selectedSport',
            'todayHours'
        ));
    }
}