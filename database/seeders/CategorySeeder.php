<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Facial Treatments',
                'description' => 'Rejuvenating facial therapies for glowing skin',
                'is_active' => true,
            ],
            [
                'name' => 'Body Treatments',
                'description' => 'Relaxing full-body wellness experiences',
                'is_active' => true,
            ],
            [
                'name' => 'Hair & Scalp',
                'description' => 'Professional hair care and scalp treatments',
                'is_active' => true,
            ],
            [
                'name' => 'Massage Therapy',
                'description' => 'Therapeutic massage for relaxation and healing',
                'is_active' => true,
            ],
            [
                'name' => 'Nail Care',
                'description' => 'Professional manicure and pedicure services',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}




