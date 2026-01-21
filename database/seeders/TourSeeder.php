<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tours = [
            [
                'name'              => 'National Park 2 Days Tour',
                'slug'              => Str::slug('National Park 2 Days Tour'),
                'price'             => 115000,
                'duration_minutes'  => 60,
                'max_participants'  => 4,
                'location'          => 'Bromo',
                'short_description' => 'Jelajahi keindahan Taman Nasional Bromo dalam tur 2 hari penuh petualangan.',
                'order_display'     => 1,
                'status'            => 'active',
            ],
            [
                'name'              => 'The Dark Forest Adventure',
                'slug'              => Str::slug('The Dark Forest Adventure'),
                'price'             => 115000,
                'duration_minutes'  => 60,
                'max_participants'  => 4,
                'location'          => 'Bromo',
                'short_description' => 'Petualangan seru menyusuri hutan gelap di sekitar Gunung Bromo.',
                'order_display'     => 2,
                'status'            => 'active',
            ],
            [
                'name'              => 'Discover Depth of Beach',
                'slug'              => Str::slug('Discover Depth of Beach'),
                'price'             => 115000,
                'duration_minutes'  => 60,
                'max_participants'  => 4,
                'location'          => 'Bromo',
                'short_description' => 'Temukan keindahan pantai tersembunyi dan bawah laut di sekitar area Bromo.',
                'order_display'     => 3,
                'status'            => 'active',
            ],
            [
                'name'              => 'Moscow Red City Land',
                'slug'              => Str::slug('Moscow Red City Land'),
                'price'             => 115000,
                'duration_minutes'  => 60,
                'max_participants'  => 4,
                'location'          => 'Bromo',
                'short_description' => 'Pengalaman unik menikmati lanskap merah khas Bromo yang menakjubkan.',
                'order_display'     => 4,
                'status'            => 'active',
            ],
            [
                'name'              => 'Magic of Italy Tours',
                'slug'              => Str::slug('Magic of Italy Tours'),
                'price'             => 115000,
                'duration_minutes'  => 60,
                'max_participants'  => 4,
                'location'          => 'Bromo',
                'short_description' => 'Rasakan keajaiban alam Bromo layaknya berada di destinasi Eropa yang magis.',
                'order_display'     => 5,
                'status'            => 'active',
            ],
            [
                'name'              => 'Sunrise at Mount Bromo',
                'slug'              => Str::slug('Sunrise at Mount Bromo'),
                'price'             => 115000,
                'duration_minutes'  => 60,
                'max_participants'  => 4,
                'location'          => 'Bromo',
                'short_description' => 'Saksikan matahari terbit paling indah di puncak Gunung Bromo.',
                'order_display'     => 6,
                'status'            => 'active',
            ],
        ];

        foreach ($tours as $tour) {
            DB::table('tours')->updateOrInsert(
                ['slug' => $tour['slug']], // cek berdasarkan slug agar tidak duplikat saat dijalankan ulang
                array_merge($tour, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}