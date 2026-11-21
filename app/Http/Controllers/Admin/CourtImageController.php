<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Court;
use App\Models\CourtImage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CourtImageController extends Controller
{
    /**
     * Upload dan simpan foto lapangan (mendukung multiple upload hingga 5 foto).
     */
    public function store(Request $request, Court $court): RedirectResponse
    {
        // Dukung kedua format: 'image' (tunggal) dan 'images[]' (multiple batch hingga 5)
        if ($request->hasFile('image') && !$request->hasFile('images')) {
            $request->validate([
                'image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'alt_text' => ['nullable', 'string', 'max:255'],
                'is_primary' => ['nullable', 'boolean'],
            ], [
                'image.image' => 'Berkas harus berupa gambar valid.',
                'image.mimes' => 'Format foto yang diizinkan hanya JPG, JPEG, PNG, atau WebP.',
                'image.max' => 'Ukuran foto tidak boleh melebihi 5 MB.',
            ]);

            $files = [$request->file('image')];
        } else {
            $request->validate([
                'images' => ['required', 'array', 'min:1', 'max:5'],
                'images.*' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
                'alt_text' => ['nullable', 'string', 'max:255'],
                'is_primary' => ['nullable', 'boolean'],
            ], [
                'images.required' => 'Silakan pilih minimal 1 foto untuk diunggah.',
                'images.max' => 'Maksimum 5 foto yang dapat diunggah dalam satu kali proses.',
                'images.*.image' => 'Setiap berkas harus berupa gambar valid.',
                'images.*.mimes' => 'Format foto yang diizinkan hanya JPG, JPEG, PNG, atau WebP.',
                'images.*.max' => 'Ukuran setiap foto tidak boleh melebihi 5 MB.',
            ]);

            $files = $request->file('images');
        }

        $uploadedPaths = [];
        $isPrimaryFlag = $request->boolean('is_primary');
        $hasExistingImages = $court->images()->count() > 0;

        try {
            DB::transaction(function () use ($court, $request, $files, &$uploadedPaths, $isPrimaryFlag, $hasExistingImages) {
                $sortOrder = $court->images()->max('sort_order') ?? 0;

                foreach ($files as $index => $file) {
                    $path = $file->store('courts', 'public');
                    $uploadedPaths[] = $path;

                    // Jika ditandai sebagai foto utama, terapkan pada foto pertama dari batch yang diunggah
                    $isPrimary = false;
                    if ($index === 0) {
                        if ($isPrimaryFlag || !$hasExistingImages) {
                            $isPrimary = true;
                            $court->images()->update(['is_primary' => false]);
                        }
                    }

                    $sortOrder++;
                    $court->images()->create([
                        'image_path' => $path,
                        'alt_text' => $request->input('alt_text') ?? ($court->name . ' - Foto ' . $sortOrder),
                        'is_primary' => $isPrimary,
                        'sort_order' => $sortOrder,
                    ]);
                }
            });
        } catch (Throwable $e) {
            // Bersihkan file yang sempat tersimpan di storage jika database gagal
            foreach ($uploadedPaths as $path) {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
            throw $e;
        }

        $count = count($uploadedPaths);

        return back()->with('success', "Berhasil mengunggah {$count} foto lapangan.");
    }

    /**
     * Tetapkan sebagai foto utama lapangan.
     */
    public function setPrimary(CourtImage $image): RedirectResponse
    {
        CourtImage::where('court_id', $image->court_id)->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);

        return back()->with('success', 'Foto utama berhasil diperbarui.');
    }

    /**
     * Hapus foto lapangan.
     */
    public function destroy(CourtImage $image): RedirectResponse
    {
        if (Storage::disk('public')->exists($image->image_path)) {
            Storage::disk('public')->delete($image->image_path);
        }

        $image->delete();

        return back()->with('success', 'Foto lapangan berhasil dihapus.');
    }
}
