<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProfilTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('profil')->delete();
        
        \DB::table('profil')->insert(array (
            0 => 
            array (
                'id' => 1,
                'nama_profil' => 'MasterKu',
                'no_id' => NULL,
                'alamat' => NULL,
                'no_telp' => '085320555394',
                'no_wa' => '085320555394',
                'email' => 'masterku@gmail.com',
                'instagram' => NULL,
                'facebook' => NULL,
                'youtube' => NULL,
                'website' => NULL,
                'deskripsi_1' => NULL,
                'deskripsi_2' => NULL,
                'deskripsi_3' => NULL,
                'logo' => '20241111163129_LOGO_LAPAK_TASIK.webp',
                'favicon' => '20241111163129_LOGO_LAPAK_TASIK.webp',
            'banner' => '20241111163129_THUMBNAIL_AS-SUNDAWY_MENGAJI_(2).webp',
                'embed_youtube' => NULL,
                'embed_map' => NULL,
                'created_at' => '2024-11-11 12:51:01',
                'updated_at' => '2024-11-11 16:31:30',
            ),
        ));
        
        
    }
}