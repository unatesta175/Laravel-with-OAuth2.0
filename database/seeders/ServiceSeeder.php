<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Dang Anum',
                'description' => 'Berehat & bersantai dengan memanjakan diri di dalam kehangatan mandian campuran bunga dan susu terapi yang melembutkan kulit anda',
                'extradescription' => json_encode(["Rendam Kaki Panas", "Sauna Stim Herba", "Skrab Seluruh Badan", "Body Mask Belakang Badan", "Mandian Campuran Bunga & bunga"]),
                'price' => '290.00',
                'duration' => '120',
                'type' => 'normal',
                'category_id' => 1,
                'image' => 'storage/service/ps1.png',
            ],
            [
                'name' => 'Mahsuri',
                'description' => 'Alami sesi pembersihan badan santai kami sambil menenangkan fikiran anda',
                'extradescription' => json_encode(["Rendam Kaki Panas", "Sauna Stim Herba", "Skrab Seluruh Badan", "Body Mask Belakang Badan"]),
                'price' => '320.00',
                'duration' => '150',
                'type' => 'normal',
                'category_id' => 1,
                'image' => 'storage/service/ps2.png',
            ],
            [
                'name' => 'Pecahkan Lemak Ekstra',
                'description' => 'Rawatan ini khas untuk memecahkan lemak degil yang melitupi organ dalaman. Kami juga menawarkan pelan permakanan percuma, khas untuk anda mengurangkan berat badan anda sebanyak 2KG seminggu.',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Serum Pelangsingan Eksklusif",
                    "Balutan Perut & Pinggul",
                    "Sauna Wap Herba",
                    "Urut Badan Selulit",
                    "Urut Badan Sel Lemak",
                    "Skrab Seluruh Badan",
                    "Pek Panas",
                    "Pek Sejuk Mata"
                ]),
                'price' => '550.00',
                'duration' => '210',
                'type' => 'normal',
                'category_id' => 1,
                'image' => 'storage/service/ps3.png',
            ],
            [
                'name' => 'Puteri Gunung Ledang',
                'description' => 'Mulakan sesi dengan wap wangi herba aromatik untuk menyahtoksin badan anda',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Sauna Stim Herba",
                    "Skrab Seluruh Badan",
                    "Body Mask Belakang Badan",
                    "Mandian Susu"
                ]),
                'price' => '420.00',
                'duration' => '180',
                'type' => 'normal',
                'category_id' => 1,
                'image' => 'storage/service/ps4.png',

            ],
            [
                'name' => 'Tun Teja',
                'description' => 'Nikmati mandian bunga di suasana yang aman di spa kami dengan layanan bak ratu dengan pakej teristimewa Kapas',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Sauna Stim Herba",
                    "Skrab Seluruh Badan",
                    "Body Mask Belakang Badan",
                    "Mandi Susu & Bunga",
                    "Rawatan Muka Premium",
                    "Urut Seluruh Badan",
                    "Rawatan Menenangkan"
                ]),
                'price' => '590.00',
                'duration' => '240',
                'type' => 'normal',
                'category_id' => 1,
                'image' => 'storage/service/image_1710002234.png',

            ],
            [
                'name' => 'Traditional Massage',
                'description' => 'Urutan lembut yang boleh mengurangkan insomnia, stress dan bagus untuk peredaran darah',
                'extradescription' => json_encode([]),  // Empty array since it's [\"\"]
                'price' => '180.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 2,
                'image' => 'storage/service/image_1710053954.jpg',


            ]
            ,
            [
                'name' => 'Urutan Seluruh Badan',
                'description' => 'Urutan lembut yang boleh mengurangkan insomnia, stress dan bagus untuk peredaran darah',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Urutan Seluruh Badan",
                    "Pek Panas"
                ]),
                'price' => '210.00',
                'duration' => '90',
                'type' => 'normal',
                'category_id' => 2,
                'image' => 'storage/service/ub2.png',

            ],
            [
                'name' => 'Urutan 4-Tangan',
                'description' => 'Rasa relax yang berganda, merasakan puas dengan urutan yang akan dilakukan oleh 2 orang therapist',
                'extradescription' => json_encode([
                    "Rendam Kaki",
                    "Panas Urutan Seluruh Badan",
                    "Pek Panas"
                ]),
                'price' => '300.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 2,
                'image' => 'storage/service/ub3.jpg',

            ],
            [
                'name' => 'Urutan Seluruh Badan Aromaterapi',
                'description' => 'Urutan menggunakan telapak tangan yang hangat dapat menghilangkan rasa sakit dan meningkatkan kualiti tidur. Urutan ini menggunakan minyak urut aromaterapi semula jadi yang dirumus khas untuk memberikan aroma yang menyegarkan sekaligus menenangkan minda. Anda akan berasa lebih bertenaga dan santai selepas urutan 60 minit ini.',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Urutan Seluruh Badan Aromaterapi",
                    "Pek Panas"
                ]),
                'price' => '230.00',
                'duration' => '90',
                'type' => 'normal',
                'category_id' => 2,
                'image' => 'storage/service/ub4.png',

            ],
            [
                'name' => 'Rawatan Selulit Seluruh Badan',
                'description' => 'Dimulakan dengan sauna untuk menenangkan badan, mengurangkan selulit',
                'extradescription' => json_encode([
                    "Sauna Wap Herba",
                    "Radio Frequency",
                    "Cavitation Treatment",
                    "Losyen Anti Selulit + Selulit Massage"
                ]),
                'price' => '290.00',
                'duration' => '90',
                'type' => 'normal',
                'category_id' => 2,
                'image' => 'storage/service/ub5.jpg',

            ],
            [
                'name' => 'Urutan Belakang Badan',
                'description' => 'Urutan berfokus kepada bahagian belakang badan hingga belakang pinggang & kedua belah tangan & kepala. Urutan lembut untuk mengurangkan otot yang tegang.',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Urutan Belakang, Bahu, Tangan dan Kepala"
                ]),
                'price' => '80.00',
                'duration' => '30',
                'type' => 'normal',
                'category_id' => 2,
                'image' => 'storage/service/ub6.jpg',

            ],
            [
                'name' => 'Urutan Peronggaan Frekuensi Radio + Steam Sauna',
                'description' => 'Peronggaan ultrasonik menyelaraskan badan menggunakan frekuensi radio dan gelombang ultrasonik frekuensi rendah. Gelombang ini membentuk gelembung di sekitar deposit lemak di bawah kulit. Gelembung kemudian pecah, memecahkan deposit lemak ke dalam interstisial dan sistem limfa di mana ia disalirkan',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Balutan + Losyen Anti-Selulit",
                    "Sauna Wap Herba",
                    "RF Treatment (Untuk 1 bahagian badan)",
                    "Cavitation Treatment (Untuk 1 bahagian badan)"
                ]),
                'price' => '250.00',
                'duration' => '90',
                'type' => 'normal',
                'category_id' => 2,
                'image' => 'storage/service/ub7.jpg',

            ],
            [
                'name' => 'Urutan Kepala Bahu Leher',
                'description' => 'Urutan yang berfokus kepada bahagian kepala, leher dan bahu mengurangkan ketegangan & sakit kepala yang disebabkan otot yang tegang dibahagian bahu.',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Urutan Kepala Bahu Leher",
                    "Pek Panas"
                ]),
                'price' => '80.00',
                'duration' => '30',
                'type' => 'normal',
                'category_id' => 2,
                'image' => 'storage/service/image_1710054665.jpg',

            ],
            [
                'name' => 'Urut Kuasa Pecah Sel Lemak',
                'description' => 'Sangat baik untuk ‘toning’, meningkatkan peredaran darah, membantu dalam pecahan deposit lemak dan selulit',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Mesin Pelangsing G5",
                    "Sauna Wap Herba"
                ]),
                'price' => '180.00',
                'duration' => '75',
                'type' => 'normal',
                'category_id' => 2,
                'image' => 'storage/service/ub9.jpg',

            ],
            [
                'name' => 'Mask Belakang Badan',
                'description' => 'Membersihkan & mencerahkan bahagian belakang kulit.',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Mask Belakang",
                    "Badan Mandi"
                ]),
                'price' => '80.00',
                'duration' => '15',
                'type' => 'normal',
                'category_id' => 3,
                'image' => 'storage/service/sk1.jpg',

            ],
            [
                'name' => 'Skrub Belakang Badan',
                'description' => 'Berfokuskan membuang daki dan kulit mati bahagian belakang & melembutkan kulit',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Skrub Belakang Badan (20 minit)",
                    "Mandi (10 minit)"
                ]),
                'price' => '90.00',
                'duration' => '20',
                'type' => 'normal',
                'category_id' => 3,
                'image' => 'storage/service/sk2.png',

            ],

            [
                'name' => 'Skrub Seluruh Badan',
                'description' => 'Membuang daki dan kulit mati serta melembutkan kulit untuk seluruh badan',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Skrub Seluruh Badan (40 minit)",
                    "Mandi (10 minit)"
                ]),
                'price' => '130.00',
                'duration' => '40',
                'type' => 'normal',
                'category_id' => 3,
                'image' => 'storage/service/sk3.jpg',

            ],
            [
                'name' => 'Skrub Seluruh Badan & Mask Badan Belakang',
                'description' => 'Membuang daki dan kulit mati serta melembutkan kulit untuk seluruh badan. Membersihkan & mencerahkan bahagian kulit belakang.',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Skrub Seluruh Badan (40 minit)",
                    "Mask Belakang Badan (10 minit)",
                    "Mandi (10 minit)"
                ]),
                'price' => '190.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 3,
                'image' => 'storage/service/sk4.jpg',

            ],

            [
                'name' => 'Rawatan Tegakkan & Bentukkan Wajah',
                'description' => 'Meregangkan kulit wajah, menegakkan dan membentuk kulit',
                'extradescription' => json_encode([""]),
                'price' => '230.00',
                'duration' => '90',
                'type' => 'normal',
                'category_id' => 4,
                'image' => 'storage/service/rf1.jpg',

            ],
            [
                'name' => 'Rawatan Mata Eksklusif',
                'description' => 'Sama seperti tapak tangan, mengurut mata boleh menenangkan ketegangan mata. Gunakan jari yang bersih untuk menggosok kelopak mata anda dengan lembut, otot di atas kening anda, bawah mata anda dan tempat perlindungan. Dengan berbuat demikian, anda mengembangkan peredaran darah ke mata anda dan melegakan semua otot di sekeliling zon itu juga dengan rawatan muka kami yang menenangkan.',
                'extradescription' => json_encode([""]),
                'price' => '180.00',
                'duration' => '90',
                'type' => 'normal',
                'category_id' => 4,
                'image' => 'storage/service/rf2.jpg',

            ],
            [
                'name' => 'Rawatan Mata Menyegarkan',
                'description' => 'Sama seperti tapak tangan, mengurut mata boleh meredakan ketegangan mata. Gunakan jari yang bersih untuk mengurut kelopak mata anda, otot di atas kening, bawah mata dan tempat perlindungan anda. Dengan berbuat demikian, anda sedang mengembangkan peredaran darah ke mata anda dan meleraikan semua otot di sekeliling zon itu.',
                'extradescription' => json_encode([""]),
                'price' => '80.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 4,
                'image' => 'storage/service/rf3.jpg',

            ],
            [
                'name' => 'Rawatan Muka Antioxidant Collagen',
                'description' => 'Melembabkan kulit & cepat meresap ke dalam kulit. Sesuai untuk kulit yang kering dan kusam',
                'extradescription' => json_encode([""]),
                'price' => '180.00',
                'duration' => '90',
                'type' => 'normal',
                'category_id' => 4,
                'image' => 'storage/service/rf4.png',

            ],
            [
                'name' => 'Rawatan Muka BB Glow',
                'description' => 'Memulihkan kulit wajah dengan segera dan membuat wajah lebih berseri',
                'extradescription' => json_encode([""]),
                'price' => '230.00',
                'duration' => '120',
                'type' => 'normal',
                'category_id' => 4,
                'image' => 'storage/service/rf5.png',

            ],
            [
                'name' => 'Rawatan Wajah Ageless O2',
                'description' => 'Melembapkan kulit yang dehidrasi, mengelupas kulit pada tahap yang lebih dalam dan mengekalkan kulit kelihatan muda',
                'extradescription' => json_encode([""]),
                'price' => '180.00',
                'duration' => '90',
                'type' => 'normal',
                'category_id' => 4,
                'image' => 'storage/service/rf6.png',

            ],
            [
                'name' => 'Pembersihan Pori Dalam',
                'description' => 'Mengurangkan white head & black head dengan gabungan LED mask',
                'extradescription' => json_encode([""]),
                'price' => '100.00',
                'duration' => '75',
                'type' => 'normal',
                'category_id' => 4,
                'image' => 'storage/service/rf7.jpg',

            ],
            [
                'name' => 'Rawatan Wajah Menyegarkan',
                'description' => 'Menggunakan mask istimewa untuk wajah lebih segar',
                'extradescription' => json_encode([""]),
                'price' => '80.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 4,
                'image' => 'storage/service/rf8.png',

            ],
            [
                'name' => 'Mandian Bunga & Susu',
                'description' => 'Gabungan mandian yang menaikkan seri serta melembutkan kulit badan',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Mandian Bunga & Susu Mandi"
                ]),
                'price' => '150.00',
                'duration' => '30',
                'type' => 'normal',
                'category_id' => 5,
                'image' => 'storage/service/m1.png',

            ],
            [
                'name' => 'Mandian Garam Himalaya',
                'description' => 'Meredakan iritasi kulit, mencerahkan kulit dan menghilangkan radiasi dari badan',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Mandi Garam Himalaya",
                    "Mandi"
                ]),
                'price' => '120.00',
                'duration' => '30',
                'type' => 'normal',
                'category_id' => 5,
                'image' => 'storage/service/m2.png',

            ],
            [
                'name' => 'Mandian Herba',
                'description' => 'Mengurangkan stress & melembutkan kulit',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Mandian Herba",
                    "Mandi"
                ]),
                'price' => '110.00',
                'duration' => '30',
                'type' => 'normal',
                'category_id' => 5,
                'image' => 'storage/service/m3.png',

            ],
            [
                'name' => 'Mandian Susu',
                'description' => 'Melembabkan & melembutkan kulit',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Mandian Susu"
                ]),
                'price' => '110.00',
                'duration' => '30',
                'type' => 'normal',
                'category_id' => 5,
                'image' => 'storage/service/m4.png',

            ],
            [
                'name' => 'Sauna Wap Herba',
                'description' => 'Membuang toksin dari dalam badan, melembutkan otot yang tegang',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Sauna Wap Herba"
                ]),
                'price' => '50.00',
                'duration' => '30',
                'type' => 'normal',
                'category_id' => 6,
                'image' => 'storage/service/s1.jpg',

            ],
            [
                'name' => 'Foot Massage + Foot Paraffin Mask',
                'description' => 'Mengurangkan sakit & melancarkan peredaran darah',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Urutan Kaki",
                    "Foot Paraffin"
                ]),
                'price' => '180.00',
                'duration' => '90',
                'type' => 'normal',
                'category_id' => 7,
                'image' => 'storage/service/rk1.png',

            ],
            [
                'name' => 'Spa Kaki Eksklusif',
                'description' => 'Mengurangkan stress & miliki kaki yang bersih & lembut',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Bersihkan Kaki dengan Berus",
                    "Rawatan Kalus",
                    "Skrab Kaki",
                    "Mask Kaki",
                    "Kilauan Kuku",
                    "Moisturiser"
                ]),
                'price' => '180.00',
                'duration' => '90',
                'type' => 'normal',
                'category_id' => 7,
                'image' => 'storage/service/rk2.png',

            ],
            [
                'name' => 'Urutan Kaki',
                'description' => 'Mengurangkan sakit & melancarkan peredaran darah',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Urutan Kaki",
                    "Pek Sejuk Mata"
                ]),
                'price' => '110.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 7,
                'image' => 'storage/service/rk3.jpg',

            ],
            [
                'name' => 'Ketiak',
                'description' => 'Melambatkan pertumbuhan bulu & melembutkan kulit',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Wax Ketiak",
                    "Bersihkan Ketiak Se"
                ]),
                'price' => '80.00',
                'duration' => '40',
                'type' => 'normal',
                'category_id' => 8,
                'image' => 'storage/service/wx1.png',

            ],
            [
                'name' => 'Seluruh Kaki',
                'description' => 'Melambatkan pertumbuhan bulu & melembutkan kulit',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Wax Seluruh Kaki",
                    "Bersihkan Seluruh Kaki"
                ]),
                'price' => '180.00',
                'duration' => '40',
                'type' => 'normal',
                'category_id' => 8,
                'image' => 'storage/service/wx2.png',

            ],
            [
                'name' => 'Seluruh Tangan',
                'description' => 'Melambatkan pertumbuhan bulu & melembutkan kulit',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Wax Seluruh Tangan",
                    "Bersihkan Seluruh Tangan"
                ]),
                'price' => '150.00',
                'duration' => '40',
                'type' => 'normal',
                'category_id' => 8,
                'image' => 'storage/service/wx3.png',

            ],
            [
                'name' => 'Bekam Angin',
                'description' => 'Membantu mengeluarkan angin dalam badan dan melegakan otot yang regang',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "17 Cup",
                    "Bekam",
                    "Lampu Inframerah",
                    "Urutan Lembut"
                ]),
                'price' => '110.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 9,
                'image' => 'storage/service/bs1.png',

            ],
            [
                'name' => 'Bekam Sunnah ( 13 Cups )',
                'description' => 'Membantu membuang toksin dalam darah dan melegakan otot yang tegang',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "13 Cups",
                    "Bekam",
                    "Lampu Inframerah",
                    "Urutan Lembut"
                ]),
                'price' => '150.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 9,
                'image' => 'storage/service/bs2.png',

            ],
            [
                'name' => 'Bekam Sunnah ( 23 Cups )',
                'description' => 'Membantu membuang toksin dalam darah dan melegakan otot yang tegang',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "23 Cups",
                    "Bekam",
                    "Lampu Inframerah",
                    "Urutan Lembut"
                ]),
                'price' => '190.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 9,
                'image' => 'storage/service/bs3.png',

            ],
            [
                'name' => 'Rawatan Resdung',
                'description' => 'Membersihkan sinus, mengelakkan jangkitan dan mengelakkan meningitis iaitu sejenis penyakit yang amat berbahaya',
                'extradescription' => json_encode([]),
                'price' => '80.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 10,
                'image' => 'storage/service/rr1.png',

            ],
            [
                'name' => 'Rawatan Resdung Facial',
                'description' => 'Rawatan resdung digabungkan dengan rawatan muka yang menyegarkan',
                'extradescription' => json_encode([]),
                'price' => '150.00',
                'duration' => '90',
                'type' => 'normal',
                'category_id' => 10,
                'image' => 'storage/service/rr2.png',

            ],
            [
                'name' => 'Balutan Perut',
                'description' => 'Meningkatkan metabolisme dan detoksifikasi dalam badan dan mengetatkan kulit',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Sapukan Losyen",
                    "Balutan Perut"
                ]),
                'price' => '50.00',
                'duration' => '30',
                'type' => 'normal',
                'category_id' => 11,
                'image' => 'storage/service/bb1.png',

            ],
            [
                'name' => 'Balutan Pinggang',
                'description' => 'Meningkatkan metabolisme dan detoksifikasi dalam badan dan mengetatkan kulit.',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Sapukan Losyen",
                    "Balutan Pinggang"
                ]),
                'price' => '60.00',
                'duration' => '30',
                'type' => 'normal',
                'category_id' => 11,
                'image' => 'storage/service/bb2.png',

            ],
            [
                'name' => 'Serum Berganda',
                'description' => 'Pakej ini adalah tambahan kepada pakej lain.',
                'extradescription' => json_encode([]),
                'price' => '50.00',
                'duration' => '30',
                'type' => 'normal',
                'category_id' => 11,
                'image' => 'storage/service/bb3.png',

            ],

            [
                'name' => 'Kecederaan Buku Lali',
                'description' => '',
                'extradescription' => json_encode([
                    "Sakit, terutamanya apabila anda menanggung berat pada kaki yang terjejas",
                    "Kelembutan apabila anda menyentuh buku lali",
                    "Bengkak",
                    "Lebam",
                    "Julat pergerakan terhad",
                    "Ketidakstabilan pada buku lali",
                    "Sensasi atau bunyi meletus pada masa kecederaan Set"
                ]),
                'price' => '100.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 12,
                'image' => 'storage/service/fs1.png',

            ],
            [
                'name' => 'Bahu Kaku',
                'description' => 'Bahu dan/atau lengan lemah dan kekakuan.',
                'extradescription' => json_encode([]),
                'price' => '100.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 12,
                'image' => 'storage/service/fs6.png',

            ],
            [
                'name' => 'Cambuk',
                'description' => '',
                'extradescription' => json_encode([
                    "Sakit leher dan kekakuan",
                    "Kesakitan yang semakin teruk dengan pergerakan leher",
                    "Kehilangan julat pergerakan di leher",
                    "Sakit kepala, paling kerap bermula di pangkal tengkorak",
                    "Kelembutan atau sakit pada bahu, bahagian atas belakang atau lengans",
                    "Kesemutan atau kebas pada lengan",
                    "Keletihan"
                ]),
                'price' => '110.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 12,
                'image' => 'storage/service/fs7.png',

            ],
            [
                'name' => 'Rehabilitasi Lutut',
                'description' => '',
                'extradescription' => json_encode([
                    "Anterior Cruciate Ligament (ACL)",
                    "Posterior Cruciate Ligament (PCL)",
                    "Osteoporosis",
                    "Osteoarthritis",
                    "Patellar dislocation"
                ]),
                'price' => '150.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 12,
                'image' => 'storage/service/fs2.png',

            ],
            [
                'name' => 'Sindrom Carpal Tunnel',
                'description' => '',
                'extradescription' => json_encode([]),
                'price' => '100.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 12,
                'image' => 'storage/service/fs3.png',

            ],
            [
                'name' => 'Tendonitis',
                'description' => '',
                'extradescription' => json_encode([]),
                'price' => '100.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 12,
                'image' => 'storage/service/fs4.png',

            ],
            [
                'name' => 'Urutan Sciatica',
                'description' => '',
                'extradescription' => json_encode([]),
                'price' => '180.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 12,
                'image' => 'storage/service/fs5.png',

            ],[
                'name' => 'Pedikur + Foot Scrub + Callus Treatment',
                'description' => 'Membersihkan kuku, merawat kulit kering & membuat kuku lebih bersinar',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Bersihkan Kaki",
                    "Potong Kuku",
                    "Bentuk Kuku",
                    "Sapukan Penyingkiran Kutikula",
                    "Penolak kutikula",
                    "Mesin",
                    "Berkilat"
                ]),
                'price' => '150.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 13,
                'image' => 'storage/service/ll1.png',

            ],
            [
                'name' => 'Manikur + Skrub Tangan',
                'description' => 'Membersihkan kuku, merawat kulit kering & membuat kuku lebih bersinar',
                'extradescription' => json_encode([
                    "Rendam Kaki Panas",
                    "Rendam Tangan",
                    "Bersihkan Tangan",
                    "Potong Kuku",
                    "Bentuk Kuku",
                    "Sapukan Penyingkiran Kutikula",
                    "Penolak kutikula",
                    "Mesin",
                    "Berkilat"
                ]),
                'price' => '100.00',
                'duration' => '60',
                'type' => 'normal',
                'category_id' => 13,
                'image' => 'storage/service/ll2.png',

            ]
        ];

        foreach ($services as $serviceData) {
            Service::create([
                'category_id' => $serviceData['category_id'],
                'name' => $serviceData['name'],
                'description' => $serviceData['description'],
                'extradescription' => $serviceData['extradescription'],
                'price' => $serviceData['price'],
                'duration' => $serviceData['duration'],
                'type' => $serviceData['type'],
                'image' => $serviceData['image'],
                'is_active' => true,
            ]);
        }
    }
}
