<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Venue;
use App\Models\OperatingHour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VenueController extends Controller
{
    /**
     * Tampilkan daftar venue olahraga.
     */
    public function index(Request $request): View
    {
        $query = Venue::withCount('courts')->latest();

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        $venues = $query->paginate(10)->withQueryString();

        return view('admin.venues.index', compact('venues'));
    }

    /**
     * Form tambah venue baru.
     */
    public function create(): View
    {
        return view('admin.venues.create');
    }

    /**
     * Simpan venue baru beserta jam operasional default.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (Venue::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-" . $counter++;
        }

        $validated['slug'] = $slug;
        $validated['is_active'] = $request->boolean('is_active', true);

        $venue = Venue::create($validated);

        // Buat jam operasional default 7 hari (06:00 - 23:00)
        for ($day = 0; $day <= 6; $day++) {
            OperatingHour::create([
                'venue_id' => $venue->id,
                'day_of_week' => $day,
                'opens_at' => '06:00:00',
                'closes_at' => '23:00:00',
                'is_closed' => false,
            ]);
        }

        return redirect()->route('admin.venues.index')
            ->with('success', 'Venue baru berhasil ditambahkan beserta jam operasional default.');
    }

    /**
     * Form edit venue.
     */
    public function edit(Venue $venue): View
    {
        $venue->load('operatingHours');

        return view('admin.venues.edit', compact('venue'));
    }

    /**
     * Update data venue dan jam operasionalnya.
     */
    public function update(Request $request, Venue $venue): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
            'hours' => ['nullable', 'array'],
            'hours.*.opens_at' => ['nullable', 'date_format:H:i'],
            'hours.*.closes_at' => ['nullable', 'date_format:H:i'],
            'hours.*.is_closed' => ['nullable'],
        ]);

        if ($venue->name !== $validated['name']) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;
            while (Venue::where('slug', $slug)->where('id', '!=', $venue->id)->exists()) {
                $slug = "{$originalSlug}-" . $counter++;
            }
            $validated['slug'] = $slug;
        }

        $validated['is_active'] = $request->boolean('is_active');
        $venue->update($validated);

        // Update jam operasional jika diinput
        if ($request->has('hours')) {
            foreach ($request->input('hours') as $dayOfWeek => $hourData) {
                OperatingHour::updateOrCreate(
                    [
                        'venue_id' => $venue->id,
                        'day_of_week' => (int)$dayOfWeek,
                    ],
                    [
                        'opens_at' => !empty($hourData['opens_at']) ? $hourData['opens_at'] . ':00' : '06:00:00',
                        'closes_at' => !empty($hourData['closes_at']) ? $hourData['closes_at'] . ':00' : '23:00:00',
                        'is_closed' => isset($hourData['is_closed']),
                    ]
                );
            }
        }

        return redirect()->route('admin.venues.index')
            ->with('success', 'Data venue dan jam operasional berhasil diperbarui.');
    }

    /**
     * Hapus venue jika tidak memiliki lapangan.
     */
    public function destroy(Venue $venue): RedirectResponse
    {
        if ($venue->courts()->exists()) {
            return back()->with('error', 'Venue tidak dapat dihapus karena masih menaungi lapangan olahraga.');
        }

        $venue->delete();

        return redirect()->route('admin.venues.index')
            ->with('success', 'Venue berhasil dihapus.');
    }
}
