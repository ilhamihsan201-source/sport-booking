<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\Facility;
use App\Models\CourtImage;
use App\Models\Sport;
use App\Models\Venue;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CourtController extends Controller
{
    /**
     * Tampilkan daftar lapangan olahraga.
     */
    public function index(Request $request): View
    {
        $query = Court::with(['sport', 'venue'])
            ->withCount('bookings')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->query('sport_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $courts = $query->paginate(10)->withQueryString();
        $sports = Sport::all();

        return view('admin.courts.index', compact('courts', 'sports'));
    }

    /**
     * Form tambah lapangan baru.
     */
    public function create(): View
    {
        $sports = Sport::where('is_active', true)->get();
        $venues = Venue::where('is_active', true)->get();
        $facilities = Facility::all();

        return view('admin.courts.create', compact('sports', 'venues', 'facilities'));
    }

    /**
     * Simpan lapangan baru (mendukung langsung unggah foto).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'venue_id' => ['required', 'exists:venues,id'],
            'sport_id' => ['required', 'exists:sports,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price_per_hour' => ['required', 'numeric', 'min:0'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'surface_type' => ['nullable', 'string', 'max:100'],
            'size' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:active,maintenance,inactive'],
            'is_featured' => ['nullable', 'boolean'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['exists:facilities,id'],
            'images' => ['nullable', 'array', 'max:5'],
            'images.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'images.max' => 'Maksimum 5 foto yang dapat diunggah sekaligus.',
            'images.*.image' => 'Setiap berkas harus berupa gambar valid.',
            'images.*.mimes' => 'Format foto yang diizinkan hanya JPG, JPEG, PNG, atau WebP.',
            'images.*.max' => 'Ukuran setiap foto tidak boleh melebihi 5 MB.',
        ]);

        $court = DB::transaction(function () use ($validated, $request) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;
            while (Court::where('slug', $slug)->exists()) {
                $slug = "{$originalSlug}-" . $counter++;
            }

            $validated['slug'] = $slug;
            $validated['is_featured'] = $request->boolean('is_featured');

            $courtData = collect($validated)->except(['facilities', 'images'])->toArray();
            $court = Court::create($courtData);

            if ($request->has('facilities')) {
                $court->facilities()->sync($request->input('facilities'));
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $file) {
                    $path = $file->store('courts', 'public');
                    $court->images()->create([
                        'image_path' => $path,
                        'alt_text' => $court->name . ' - Foto ' . ($index + 1),
                        'is_primary' => $index === 0,
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            return $court;
        });

        return redirect()->route('admin.courts.index')
            ->with('success', 'Lapangan baru berhasil ditambahkan.');
    }

    /**
     * Form edit lapangan.
     */
    public function edit(Court $court): View
    {
        $court->load(['facilities', 'images']);
        $sports = Sport::all();
        $venues = Venue::all();
        $facilities = Facility::all();

        return view('admin.courts.edit', compact('court', 'sports', 'venues', 'facilities'));
    }

    /**
     * Update data lapangan.
     */
    public function update(Request $request, Court $court): RedirectResponse
    {
        $validated = $request->validate([
            'venue_id' => ['required', 'exists:venues,id'],
            'sport_id' => ['required', 'exists:sports,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price_per_hour' => ['required', 'numeric', 'min:0'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'surface_type' => ['nullable', 'string', 'max:100'],
            'size' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:active,maintenance,inactive'],
            'is_featured' => ['nullable', 'boolean'],
            'facilities' => ['nullable', 'array'],
            'facilities.*' => ['exists:facilities,id'],
        ]);

        if ($court->name !== $validated['name']) {
            $slug = Str::slug($validated['name']);
            $originalSlug = $slug;
            $counter = 1;
            while (Court::where('slug', $slug)->where('id', '!=', $court->id)->exists()) {
                $slug = "{$originalSlug}-" . $counter++;
            }
            $validated['slug'] = $slug;
        }

        $validated['is_featured'] = $request->boolean('is_featured');

        $court->update($validated);
        $court->facilities()->sync($request->input('facilities', []));

        return redirect()->route('admin.courts.index')
            ->with('success', 'Data lapangan berhasil diperbarui.');
    }

    /**
     * Aktifkan atau nonaktifkan status lapangan secara cepat.
     */
    public function toggleStatus(Court $court): RedirectResponse
    {
        $newStatus = $court->status === 'active' ? 'inactive' : 'active';
        $court->update(['status' => $newStatus]);

        $statusLabel = $newStatus === 'active' ? 'diaktifkan kembali' : 'dinonaktifkan';

        return redirect()->route('admin.courts.index')
            ->with('success', "Lapangan \"{$court->name}\" berhasil {$statusLabel}.");
    }

    /**
     * Hapus lapangan hanya jika belum memiliki riwayat booking apa pun.
     */
    public function destroy(Court $court): RedirectResponse
    {
        // Periksa apakah lapangan memiliki riwayat booking dalam status apa pun
        if ($court->bookings()->exists()) {
            return redirect()->route('admin.courts.index')
                ->with('error', 'Lapangan tidak dapat dihapus karena memiliki riwayat booking. Silakan nonaktifkan lapangan jika tidak ingin menerima pemesanan baru.');
        }

        DB::transaction(function () use ($court) {
            // Hapus file fisik foto lapangan jika tidak digunakan oleh entitas lain
            foreach ($court->images as $image) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    $isUsedElsewhere = CourtImage::where('image_path', $image->image_path)
                        ->where('id', '!=', $image->id)
                        ->exists();

                    if (!$isUsedElsewhere) {
                        Storage::disk('public')->delete($image->image_path);
                    }
                }
            }

            // Bersihkan data relasi sebelum menghapus record lapangan
            $court->images()->delete();
            $court->facilities()->detach();
            $court->delete();
        });

        return redirect()->route('admin.courts.index')
            ->with('success', "Lapangan \"{$court->name}\" berhasil dihapus.");
    }
}
