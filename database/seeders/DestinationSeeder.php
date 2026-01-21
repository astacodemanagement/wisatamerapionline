<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DestinationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $destinations = [
            [
                'name'               => 'Gunung Bromo',
                'slug'               => Str::slug('Gunung Bromo'),
                'description'        => 'Gunung Bromo adalah gunung berapi aktif yang terkenal dengan kalderanya yang luas dan pemandangan sunrise yang spektakuler. Terletak di Taman Nasional Bromo Tengger Semeru, destinasi ini menjadi salah satu ikon wisata Indonesia yang wajib dikunjungi.',
                'short_description'  => 'Nikmati sunrise terbaik di Indonesia dari puncak Gunung Bromo.',
                'location_details'   => 'Taman Nasional Bromo Tengger Semeru, Jawa Timur',
                'views'              => 1250,
                'order_display'      => 1,
                'status'             => 'active',
            ],
            [
                'name'               => 'Pulau Bali',
                'slug'               => Str::slug('Pulau Bali'),
                'description'        => 'Bali dikenal sebagai Pulau Dewata dengan pantai indah, budaya yang kaya, sawah terasering, dan kuil-kuil bersejarah. Destinasi ini menawarkan kombinasi sempurna antara wisata alam, budaya, dan relaksasi.',
                'short_description'  => 'Pulau surga dengan pantai eksotis dan budaya yang mempesona.',
                'location_details'   => 'Provinsi Bali, Indonesia',
                'views'              => 980,
                'order_display'      => 2,
                'status'             => 'active',
            ],
            [
                'name'               => 'Borobudur',
                'slug'               => Str::slug('Borobudur'),
                'description'        => 'Candi Borobudur adalah candi Buddha terbesar di dunia yang dibangun pada abad ke-9. Situs warisan dunia UNESCO ini memiliki relief indah dan stupa-stupa yang mengelilingi struktur utama.',
                'short_description'  => 'Candi Buddha terbesar di dunia dengan arsitektur megah.',
                'location_details'   => 'Magelang, Jawa Tengah',
                'views'              => 870,
                'order_display'      => 3,
                'status'             => 'active',
            ],
            [
                'name'               => 'Raja Ampat',
                'slug'               => Str::slug('Raja Ampat'),
                'description'        => 'Raja Ampat adalah surga bawah laut dengan keanekaragaman hayati laut tertinggi di dunia. Ribuan pulau karst dan air jernih membuatnya menjadi destinasi diving dan snorkeling kelas dunia.',
                'short_description'  => 'Surga diving dengan terumbu karang terindah di dunia.',
                'location_details'   => 'Papua Barat, Indonesia',
                'views'              => 760,
                'order_display'      => 4,
                'status'             => 'active',
            ],
            [
                'name'               => 'Labuan Bajo',
                'slug'               => Str::slug('Labuan Bajo'),
                'description'        => 'Labuan Bajo adalah gerbang menuju Taman Nasional Komodo, rumah bagi kadal purba Komodo. Selain itu, destinasi ini menawarkan pantai pink, pulau-pulau kecil, dan spot diving yang luar biasa.',
                'short_description'  => 'Gerbang menuju habitat asli Komodo dan pulau-pulau eksotis.',
                'location_details'   => 'Nusa Tenggara Timur',
                'views'              => 680,
                'order_display'      => 5,
                'status'             => 'active',
            ],
            [
                'name'               => 'Yogyakarta',
                'slug'               => Str::slug('Yogyakarta'),
                'description'        => 'Yogyakarta atau Jogja adalah kota budaya dengan Kraton Sultan, Malioboro, dan candi-candi bersejarah seperti Prambanan. Kota ini juga terkenal dengan seni, batik, dan kuliner khasnya.',
                'short_description'  => 'Kota budaya dengan keraton, candi, dan seni tradisional.',
                'location_details'   => 'Daerah Istimewa Yogyakarta',
                'views'              => 920,
                'order_display'      => 6,
                'status'             => 'active',
            ],
        ];

        foreach ($destinations as $destination) {
            DB::table('destinations')->updateOrInsert(
                ['slug' => $destination['slug']], // cegah duplikat jika seeder dijalankan ulang
                array_merge($destination, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}