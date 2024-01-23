<?php

namespace Database\Seeders;

use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Database\Seeder;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurants = [
            [
                'name' => 'Restaurant A',
                'address' => 'Bandung, Indonesia',
                'phone' => '+62812345678',
            ],
            [
                'name' => 'Restaurant B',
                'address' => 'Yogyakarta, Indonesia',
                'phone' => '+62812345679',
            ],
        ];

        foreach ($restaurants as $restaurant) {
            $result = Restaurant::create($restaurant);

            for ($i=0; $i < 8; $i++) {
                Table::create([
                    'restaurant_id' => $result->id,
                    'name' => 'Table ' . str($i+1),
                    'capacity' => random_int(2, 12)
                ]);
            }
        }
    }
}
