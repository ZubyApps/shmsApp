<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Patient;
use App\Models\Sponsor;
use App\Models\SponsorCategory;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
         User::factory(1)->create();
         SponsorCategory::factory(1)->create();
         Sponsor::factory(1)->create();
         Patient::factory(1)->create();

        \App\Models\ResourceCategory::create([
            'name' => 'Medication',
            'description' => 'Pills, Injectables, Topicals, Drops',
            'user_id'   => 1
        ]);
        \App\Models\ResourceCategory::create([
            'name' => 'Labortory',
            'description' => 'Chemistry, Microbiology, Imagings',
            'user_id'   => 1
        ]);
        \App\Models\ResourceCategory::create([
            'name' => 'Medical Service',
            'description' => 'Consultations, Procedures, Operations, Dressings',
            'user_id'   => 1
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Pills',
            'description' => 'Tablets, Capsules, Suppositories, Dispersables',
            'user_id'   => 1,
            'resource_category_id'   => 1
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Injectables',
            'description' => 'Infusions, Injections',
            'user_id'   => 1,
            'resource_category_id'   => 1
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Microbiology',
            'description' => 'Microbiological Tests',
            'user_id'   => 1,
            'resource_category_id'   => 2
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Imaging',
            'description' => 'X-rays, Ultrasounds, MRIs, CTScans, Other Scans',
            'user_id'   => 1,
            'resource_category_id'   => 2
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Consultations',
            'description' => 'Gp Consultations & Reviews, Specialist Consultation & Reviews ',
            'user_id'   => 1,
            'resource_category_id'   => 3
        ]);
    }
}
