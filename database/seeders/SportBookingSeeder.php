<?php

namespace Database\Seeders;

use App\Models\Court;
use App\Models\Facility;
use App\Models\OperatingHour;
use App\Models\Sport;
use App\Models\Venue;
use Illuminate\Database\Seeder;

class SportBookingSeeder extends Seeder
{
    public function run(): void
    {
        // JENIS OLAHRAGA
        $padel = Sport::updateOrCreate(
            ['slug' => 'padel'],
            [
                'name' => 'Padel',
                'description' => 'Olahraga raket yang seru dan kompetitif.',
                'icon' => 'sports-tennis',
                'is_active' => true,
            ]
        );

        $miniSoccer = Sport::updateOrCreate(
            ['slug' => 'mini-soccer'],
            [
                'name' => 'Mini Soccer',
                'description' => 'Permainan sepak bola dengan format lapangan mini.',
                'icon' => 'sports-soccer',
                'is_active' => true,
            ]
        );

        // LOKASI ARENA DEMO
        $venue = Venue::updateOrCreate(
            ['slug' => 'kinetic-court-arena'],
            [
                'name' => 'Kinetic Court Arena',
                'description' => 'Arena olahraga untuk Padel dan Mini Soccer.',
                'address' => 'Alamat demo, silakan sesuaikan',
                'city' => 'Jakarta Selatan',
                'province' => 'DKI Jakarta',
                'phone' => null,
                'email' => null,
                'is_active' => true,
            ]
        );

        // LAPANGAN DEMO
        $courtA = Court::updateOrCreate(
            ['slug' => 'padel-panoramic-a'],
            [
                'venue_id' => $venue->id,
                'sport_id' => $padel->id,
                'name' => 'Padel Panoramic A',
                'description' => 'Lapangan Padel dengan dinding kaca panoramik.',
                'price_per_hour' => 250000,
                'capacity' => 4,
                'surface_type' => 'Rumput sintetis',
                'size' => null,
                'status' => 'active',
                'is_featured' => true,
            ]
        );

        $courtB = Court::updateOrCreate(
            ['slug' => 'sunset-padel-b'],
            [
                'venue_id' => $venue->id,
                'sport_id' => $padel->id,
                'name' => 'Sunset Padel Court B',
                'description' => 'Lapangan Padel untuk bermain bersama teman.',
                'price_per_hour' => 220000,
                'capacity' => 4,
                'surface_type' => 'Rumput sintetis',
                'size' => null,
                'status' => 'active',
                'is_featured' => true,
            ]
        );

        $courtC = Court::updateOrCreate(
            ['slug' => 'mini-soccer-arena-1'],
            [
                'venue_id' => $venue->id,
                'sport_id' => $miniSoccer->id,
                'name' => 'Mini Soccer Arena 1',
                'description' => 'Lapangan Mini Soccer untuk pertandingan tim.',
                'price_per_hour' => 500000,
                'capacity' => 14,
                'surface_type' => 'Rumput sintetis',
                'size' => null,
                'status' => 'active',
                'is_featured' => true,
            ]
        );

        // FASILITAS
        $facilityNames = [
            'Parkir',
            'Toilet',
            'Ruang Ganti',
            'Wi-Fi',
            'Kantin',
            'Mushola',
            'Tribun Penonton',
            'Penerangan LED',
        ];

        $facilities = collect($facilityNames)->map(
            fn ($name) => Facility::firstOrCreate(
                ['name' => $name]
            )
        );

        // HUBUNGKAN FASILITAS KE LAPANGAN
        $courtA->facilities()->sync(
            $facilities->whereIn('name', [
                'Parkir',
                'Toilet',
                'Ruang Ganti',
                'Wi-Fi',
                'Penerangan LED',
            ])->pluck('id')
        );

        $courtB->facilities()->sync(
            $facilities->whereIn('name', [
                'Parkir',
                'Toilet',
                'Ruang Ganti',
                'Kantin',
                'Penerangan LED',
            ])->pluck('id')
        );

        $courtC->facilities()->sync(
            $facilities->whereIn('name', [
                'Parkir',
                'Toilet',
                'Mushola',
                'Kantin',
                'Tribun Penonton',
                'Penerangan LED',
            ])->pluck('id')
        );

        // JAM OPERASIONAL: SETIAP HARI 07.00 - 23.00
        for ($day = 0; $day <= 6; $day++) {
            OperatingHour::updateOrCreate(
                [
                    'venue_id' => $venue->id,
                    'day_of_week' => $day,
                ],
                [
                    'opens_at' => '07:00:00',
                    'closes_at' => '23:00:00',
                    'is_closed' => false,
                ]
            );
        }
    }
}