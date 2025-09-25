<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            // Facial Treatments
            [
                'category_name' => 'Facial Treatments',
                'services' => [
                    ['name' => 'Deep Cleansing Facial', 'description' => 'Deep pore cleansing with extraction', 'price' => 120.00, 'duration' => 60],
                    ['name' => 'Anti-Aging Treatment', 'description' => 'Advanced anti-aging therapy with collagen boost', 'price' => 180.00, 'duration' => 90],
                    ['name' => 'Hydrating Facial', 'description' => 'Intense hydration for dry and dehydrated skin', 'price' => 150.00, 'duration' => 75],
                    ['name' => 'Brightening Facial', 'description' => 'Vitamin C treatment for glowing complexion', 'price' => 140.00, 'duration' => 70],
                ]
            ],
            // Body Treatments
            [
                'category_name' => 'Body Treatments',
                'services' => [
                    ['name' => 'Full Body Massage', 'description' => 'Complete relaxation massage for stress relief', 'price' => 200.00, 'duration' => 90],
                    ['name' => 'Hot Stone Therapy', 'description' => 'Therapeutic hot stone treatment for deep relaxation', 'price' => 250.00, 'duration' => 120],
                    ['name' => 'Body Scrub & Wrap', 'description' => 'Exfoliating scrub with nourishing body wrap', 'price' => 180.00, 'duration' => 90],
                    ['name' => 'Aromatherapy Massage', 'description' => 'Essential oils massage for mind and body wellness', 'price' => 220.00, 'duration' => 90],
                ]
            ],
            // Hair & Scalp
            [
                'category_name' => 'Hair & Scalp',
                'services' => [
                    ['name' => 'Hair Treatment', 'description' => 'Nourishing hair restoration therapy', 'price' => 100.00, 'duration' => 60],
                    ['name' => 'Scalp Massage', 'description' => 'Relaxing scalp therapy for hair health', 'price' => 80.00, 'duration' => 45],
                    ['name' => 'Hair Styling', 'description' => 'Professional hair styling and blowdry', 'price' => 60.00, 'duration' => 30],
                    ['name' => 'Keratin Treatment', 'description' => 'Smoothing keratin treatment for frizzy hair', 'price' => 300.00, 'duration' => 180],
                ]
            ],
            // Massage Therapy
            [
                'category_name' => 'Massage Therapy',
                'services' => [
                    ['name' => 'Swedish Massage', 'description' => 'Classic relaxation massage technique', 'price' => 160.00, 'duration' => 60],
                    ['name' => 'Deep Tissue Massage', 'description' => 'Intensive massage for muscle tension relief', 'price' => 190.00, 'duration' => 75],
                    ['name' => 'Thai Massage', 'description' => 'Traditional Thai stretching and pressure point massage', 'price' => 170.00, 'duration' => 90],
                    ['name' => 'Prenatal Massage', 'description' => 'Gentle massage for expecting mothers', 'price' => 180.00, 'duration' => 60],
                ]
            ],
            // Nail Care
            [
                'category_name' => 'Nail Care',
                'services' => [
                    ['name' => 'Classic Manicure', 'description' => 'Traditional nail care and polish', 'price' => 50.00, 'duration' => 45],
                    ['name' => 'Gel Manicure', 'description' => 'Long-lasting gel polish manicure', 'price' => 70.00, 'duration' => 60],
                    ['name' => 'Classic Pedicure', 'description' => 'Foot care and nail polish', 'price' => 60.00, 'duration' => 50],
                    ['name' => 'Spa Pedicure', 'description' => 'Luxury foot spa with massage', 'price' => 90.00, 'duration' => 75],
                ]
            ],
        ];

        foreach ($services as $categoryData) {
            $category = Category::where('name', $categoryData['category_name'])->first();

            if ($category) {
                foreach ($categoryData['services'] as $serviceData) {
                    Service::create([
                        'category_id' => $category->id,
                        'name' => $serviceData['name'],
                        'description' => $serviceData['description'],
                        'price' => $serviceData['price'],
                        'duration' => $serviceData['duration'],
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}




