<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facility;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FacilityController extends Controller
{
    /**
     * Tampilkan daftar fasilitas lapangan.
     */
    public function index(Request $request): View
    {
        $facilities = Facility::withCount('courts')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.facilities.index', compact('facilities'));
    }

    /**
     * Form tambah fasilitas.
     */
    public function create(): View
    {
        return view('admin.facilities.create');
    }

    /**
     * Simpan fasilitas baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:facilities,name'],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        Facility::create($validated);

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Fasilitas baru berhasil ditambahkan.');
    }

    /**
     * Form edit fasilitas.
     */
    public function edit(Facility $facility): View
    {
        return view('admin.facilities.edit', compact('facility'));
    }

    /**
     * Update data fasilitas.
     */
    public function update(Request $request, Facility $facility): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:facilities,name,' . $facility->id],
            'icon' => ['nullable', 'string', 'max:50'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $facility->update($validated);

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Fasilitas berhasil diperbarui.');
    }

    /**
     * Hapus fasilitas.
     */
    public function destroy(Facility $facility): RedirectResponse
    {
        $facility->courts()->detach();
        $facility->delete();

        return redirect()->route('admin.facilities.index')
            ->with('success', 'Fasilitas berhasil dihapus.');
    }
}
