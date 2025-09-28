<?php

namespace Database\Seeders;

use App\Models\ServiceCategory;
use App\Models\ServiceCategoryTag;
use Illuminate\Database\Seeder;

class ServiceCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Pakej Spa' => [
                'description' => 'Serlahkan aura kewanitaan anda dengan pakej spa esklusif Kapas Beauty. Hilangkan toksin dari badan, bersihkan kulit dan kembalikan kecantikan dalaman anda dengan pakej spa menyeluruh.',
                'tags' => ['Wanita Aktif', 'Golongan Berumur', 'Suri Rumah', 'Ibu Lepas Bersalin', 'Wanita Bekerjaya'],
                'image' => 'storage/service-category/sc1.png',
            ],
            'Urutan Badan' => [
                'description' => 'Bawa minda dan badan anda ke dalam suasana ketenangan serta aktifkan proses penyembuhan pada masa yang sama. Kesejahteraan umum anda terjamin dalam jagaan kami.',
                'tags' => ['Bakal Pengantin', 'Suri Rumah', 'Ibu Lepas Bersalin', 'Wanita Bekerjaya'],
                'image' => 'storage/service-category/sc2.png',
            ],
            'Skrub' => [
                'description' => 'Rutin kecantikan Mesir sejak zaman dulu lagi untuk membuang sel-sel kulit mati dan kotoran daripada lapisan kulit anda. Skrub membantu memberi lapisan kelembapan kulit dan lapisan minyak badan baharu dan bersih.',
                'tags' => ['Bakal Pengantin', 'Suri Rumah', 'Ibu Lepas Bersalin', 'Wanita Bekerjaya'],
                'image' => 'storage/service-category/sc4.png',
            ]
            ,
            'Rawatan Facial' => [
                'description' => 'Tampak lebih segar dan bertenaga. Pilih pakej rawatan wajah yang bersesuain untuk membantu anda menyelesaikan masalah kulit muka.',
                'tags' => ['Bakal Pengantin', 'Suri Rumah', 'Ibu Lepas Bersalin', 'Wanita Bekerjaya'],
                'image' => 'storage/service-category/sc5.png',
            ],
            'Mandian' => [
                'description' => 'Tenangkan otot anda, bersihkan kulit dan tenangkan minda anda dengan pakej-pakej mandian Kapas.',
                'tags' => ['Bakal Pengantin', 'Suri Rumah', 'Ibu Lepas Bersalin', 'Wanita Bekerjaya'],
                'image' => 'storage/service-category/sc6.png',
            ],
            'Sauna' => [
                'description' => 'Tarik nafas secara perlahan dan rehatkan otot anda, dari kepala hingga ke kaki di sauna Kapas. Sauna juga sememangnya terbukti dalam membantu proses pengurangan berat badan anda.',
                'tags' => ['Bakal Pengantin', 'Suri Rumah', 'Ibu Lepas Bersalin', 'Wanita Bekerjaya'],
                'image' => 'storage/service-category/sc7.png',
            ],
            'Rawatan Kaki' => [
                'description' => 'Lepaskan segala ketegangan anda dengan urutan kaki yang melegakan dan selesa. Bayangkan perasaan yang memuaskan dan lega selepas kami lepaskan titik-titik sakit di kaki anda. Itulah jaminan Kapas.',
                'tags' => ['Bakal Pengantin', 'Suri Rumah', 'Ibu Lepas Bersalin', 'Wanita Bekerjaya'],
                'image' => 'storage/service-category/sc8.png',
            ],
            'Waxing' => [
                'description' => 'Dapatkan kulit yang lebih halus dan lembut, pertumbuhan bulu yang lebih nipis dan lebih halus, serta tiada lagi pertumbuhan bulu di tempat yang tidak diingini.',
                'tags' => ['Bakal Pengantin', 'Suri Rumah', 'Ibu Lepas Bersalin', 'Wanita Bekerjaya'],
                'image' => 'storage/service-category/sc9.png',
            ],
            'Bekam Sunnah' => [
                'description' => 'Amalan yang diamalkan oleh Rasullulah S.A.W. sebagai kaedah perubatan dan pengubatan penyakit.',
                'tags' => ['Bakal Pengantin', 'Suri Rumah', 'Ibu Lepas Bersalin', 'Wanita Bekerjaya'],
                'image' => 'storage/service-category/sc10.png',
            ],
            'Rawatan Resdung' => [
                'description' => 'Masalah resdung membuatkan anda berasa tidak selesa dan hilang mood. Jangan risau, anda akan lega setelah kami selesaikan semuanya untuk anda!',
                'tags' => ['Bakal Pengantin', 'Suri Rumah', 'Ibu Lepas Bersalin', 'Wanita Bekerjaya'],
                'image' => 'storage/service-category/sc11.png',
            ],
            'Balutan Badan' => [
                'description' => 'Hilangkan toksin dari badan, baiki kontur badan anda buat sementara waktu, buang kulit mati, pelembapan intensif dan relaksasi anda dengan pakej balutan badan menyeluruh.',
                'tags' => ['Bakal Pengantin', 'Suri Rumah', 'Ibu Lepas Bersalin', 'Wanita Bekerjaya'],
                'image' => 'storage/service-category/sc12.png',
            ],
            'Fisioterapi' => [
                'description' => 'Redakan kesakitan dan dapatkan kembali fleksibiliti anda untuk meluangkan masa yang lebih berkualiti bersama orang tersayang.',
                'tags' => ['Bakal Pengantin', 'Suri Rumah', 'Ibu Lepas Bersalin', 'Wanita Bekerjaya'],
                'image' => 'storage/service-category/sc13.png',
            ],
            'Lain-lain' => [
                'description' => 'Walaupun nampak kecil, namun impaknya besar. Pilih pakej – pakej eyelash perming dan manikur/pedikur untuk kuku anda.',
                'tags' => ['Bakal Pengantin', 'Suri Rumah', 'Ibu Lepas Bersalin', 'Wanita Bekerjaya'],
                'image' => 'storage/service-category/sc14.png'
            ],
        ];

        foreach ($categories as $categoryName => $categoryData) {
            // Create the service category
            $serviceCategory = ServiceCategory::create([
                'name' => $categoryName,
                'description' => $categoryData['description'],
                'image' => $categoryData['image'],
                'is_active' => true,
            ]);

            // Create and attach tags
            foreach ($categoryData['tags'] as $tagName) {
                $tag = ServiceCategoryTag::firstOrCreate([
                    'name' => $tagName,
                    'is_active' => true,
                ]);

                $serviceCategory->tags()->attach($tag->id);
            }
        }
    }
}
