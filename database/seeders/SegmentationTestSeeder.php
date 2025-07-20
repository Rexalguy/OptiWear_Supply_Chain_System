<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SegmentationTestSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data
        DB::table('segmentation_results')->truncate();
        
        // Insert sample segmentation data
        DB::table('segmentation_results')->insert([
            [
                'segment_label' => 'Male 26-35',
                'gender' => 'Male',
                'age_group' => '26-35',
                'shirt_category' => 'casual',
                'total_purchased' => 150,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'segment_label' => 'Female 18-25',
                'gender' => 'Female',
                'age_group' => '18-25',
                'shirt_category' => 'formal',
                'total_purchased' => 120,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'segment_label' => 'Male 36-50',
                'gender' => 'Male',
                'age_group' => '36-50',
                'shirt_category' => 'business',
                'total_purchased' => 200,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'segment_label' => 'Female 26-35',
                'gender' => 'Female',
                'age_group' => '26-35',
                'shirt_category' => 'casual',
                'total_purchased' => 180,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'segment_label' => 'Male 18-25',
                'gender' => 'Male',
                'age_group' => '18-25',
                'shirt_category' => 'sports',
                'total_purchased' => 95,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'segment_label' => 'Female 36-50',
                'gender' => 'Female',
                'age_group' => '36-50',
                'shirt_category' => 'formal',
                'total_purchased' => 165,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
