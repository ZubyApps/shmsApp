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

        // Resource Categories
        \App\Models\ResourceCategory::create([
            'name' => 'Medications',
            'description' => 'Pills, Injectables, Topicals, Drops',
            'user_id'   => 1
        ]);
        \App\Models\ResourceCategory::create([
            'name' => 'Investigations',
            'description' => 'Chemistry, Microbiology, Imagings',
            'user_id'   => 1
        ]);
        \App\Models\ResourceCategory::create([
            'name' => 'Medical Services',
            'description' => 'Consultations, Procedures, Operations, Dressings',
            'user_id'   => 1
        ]);
        \App\Models\ResourceCategory::create([
            'name' => 'Consumables',
            'description' => 'Parenteral devices and non-reusable, non-medication items',
            'user_id'   => 1
        ]);
        \App\Models\ResourceCategory::create([
            'name' => 'Hospital Services',
            'description' => 'All non medical services offered by the facility',
            'user_id'   => 1
        ]);
        \App\Models\ResourceCategory::create([
            'name' => 'Other Services',
            'description' => 'Other services otherwise categorized',
            'user_id'   => 1
        ]);

        //ResourceSubCategories for Resource Category 1
        \App\Models\ResourceSubCategory::create([
            'name' => 'Pill',
            'description' => 'Tablets, Capsules, Suppositories, Dispersables',
            'user_id'   => 1,
            'resource_category_id'   => 1
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Injectable',
            'description' => 'Infusions, Injections',
            'user_id'   => 1,
            'resource_category_id'   => 1
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Topical',
            'description' => 'Powders, Creams',
            'user_id'   => 1,
            'resource_category_id'   => 1
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Drop',
            'description' => 'Eye drops, Ear drops, Infant Drops',
            'user_id'   => 1,
            'resource_category_id'   => 1
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Treatment',
            'description' => 'Dressings, Care treatments',
            'user_id'   => 1,
            'resource_category_id'   => 1
        ]);

        //ResourceSubCategories for Resource Category 2
        \App\Models\ResourceSubCategory::create([
            'name' => 'Microbiology',
            'description' => 'Microbiological Tests',
            'user_id'   => 1,
            'resource_category_id'   => 2
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Chemistry',
            'description' => 'Chemistry Tests',
            'user_id'   => 1,
            'resource_category_id'   => 2
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Hermatology/Microscopy',
            'description' => 'Blood tests',
            'user_id'   => 1,
            'resource_category_id'   => 2
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Serology/Immunology',
            'description' => 'Immunity tests',
            'user_id'   => 1,
            'resource_category_id'   => 2
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Toxicology',
            'description' => 'Immunity tests',
            'user_id'   => 1,
            'resource_category_id'   => 2
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Pathology',
            'description' => 'Bodily Fluid tests',
            'user_id'   => 1,
            'resource_category_id'   => 2
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Imaging',
            'description' => 'X-rays, Ultrasounds, MRIs, CTScans, Other Scans',
            'user_id'   => 1,
            'resource_category_id'   => 2
        ]);

        //ResourceSubCategories for Resource Category 3
        \App\Models\ResourceSubCategory::create([
            'name' => 'Consultations',
            'description' => 'Gp Consultations & Reviews, Specialist Consultation & Reviews ',
            'user_id'   => 1,
            'resource_category_id'   => 3
        ]);
    }
}
