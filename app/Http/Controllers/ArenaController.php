<?php

namespace App\Http\Controllers;

use App\Models\Sport;
use App\Models\Court;

class ArenaController extends Controller
{
    /**
     * Menampilkan katalog seluruh arena.
     */
    public function index()
    {
        $sports = Sport::where('is_active', true)
            ->withCount(['courts' => fn ($query) => $query->where('status', 'active')])
            ->orderBy('name')
            ->get();

        $courts = Court::with([
            'sport',
            'venue',
            'images',
            'facilities',
        ])
            ->where('status', 'active')
            ->whereHas('venue', fn ($query) => $query->where('is_active', true))
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->get();

        return view('arenas.index', compact(
            'sports',
            'courts'
        ));
    }

    /**
     * Menampilkan arena berdasarkan jenis olahraga.
     */
    public function show(Sport $sport)
    {
        $sport->loadCount(['courts' => fn ($query) => $query->where('status', 'active')]);

        $courts = Court::with([
            'sport',
            'venue',
            'images',
            'facilities',
        ])
            ->where('sport_id', $sport->id)
            ->where('status', 'active')
            ->whereHas('venue', fn ($query) => $query->where('is_active', true))
            ->orderByDesc('is_featured')
            ->orderBy('name')
            ->get();

        return view('arenas.show', compact(
            'sport',
            'courts'
        ));
    }
}