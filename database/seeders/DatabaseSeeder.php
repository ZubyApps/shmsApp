<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enum\PayClass;
use App\Models\Patient;
use App\Models\User;
use DateTime;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //  User::factory(1)->create();
        //  SponsorCategory::factory(1)->create();
        //  Sponsor::factory(1)->create();

        User::create([
            'firstname' => 'User',
            'middlename' => 'Super',
            'lastname' => 'Admin',
            'email' => 'admin@example.com',
            'address' => '24 J.S Tarka Way, Wadata',
            'date_of_birth' => new DateTime('1982/06/28'),
            'highest_qualification' => 'MBBS',
            'sex' => 'male',
            'marital_status' => 'Married',
            'username' => 'Admin User',
            'phone_number' => '09022812281',
            'state_of_origin' => 'Anambra',
            'next_of_kin' => 'Nzube Okoye',
            'next_of_kin_rship' => 'Son',
            'next_of_kin_phone' => '08035999029',
            'date_of_employment' => new DateTime('1982/06/28'),
            'password' => Hash::make('mylovelywife'), // password
        ]);
        User::create([
            'firstname' => 'Stephnie',
            'middlename' => 'Iniobong',
            'lastname' => 'Nkonim',
            'email' => 'nkonim@example.com',
            'address' => 'Demekpe way, Demekpe Mkd',
            'date_of_birth' => new DateTime('1996/03/10'),
            'highest_qualification' => 'Btech',
            'sex' => 'female',
            'marital_status' => 'Single',
            'username' => 'Steph IT',
            'phone_number' => '09023185763',
            'state_of_origin' => 'Akwa Ibom',
            'next_of_kin' => 'Emmanuel Nkonim',
            'next_of_kin_rship' => 'Father',
            'next_of_kin_phone' => '08136196683',
            'date_of_employment' => new DateTime('2020/06/31'),
            'password' => Hash::make('Stephanie'), // password
        ]);

         //Sponsor Categories
         \App\Models\SponsorCategory::create([
            'user_id' => 1,
            'name' => 'Individual',
            'description' => 'Sponsored by the Patient',
            'pay_class' => PayClass::from('Cash'),
            'approval'  => filter_var('false', FILTER_VALIDATE_BOOL),
            'bill_matrix' => '40',
            'balance_required' => filter_var('true', FILTER_VALIDATE_BOOL),
            'consultation_fee' => '1500'
         ]);
         \App\Models\SponsorCategory::create([
            'user_id' => 1,
            'name' => 'Family',
            'description' => 'Sponsored by the family',
            'pay_class' => PayClass::from('Cash'),
            'approval'  => filter_var('false', FILTER_VALIDATE_BOOL),
            'bill_matrix' => '40',
            'balance_required' => filter_var('true', FILTER_VALIDATE_BOOL),
            'consultation_fee' => '1500'
         ]);
         \App\Models\SponsorCategory::create([
            'user_id' => 1,
            'name' => 'HMO',
            'description' => 'Sponsored by Private Insurance',
            'pay_class' => PayClass::from('Credit'),
            'approval'  => filter_var('true', FILTER_VALIDATE_BOOL),
            'bill_matrix' => '100',
            'balance_required' => filter_var('false', FILTER_VALIDATE_BOOL),
            'consultation_fee' => '2500'
         ]);
         \App\Models\SponsorCategory::create([
            'user_id' => 1,
            'name' => 'NHIS',
            'description' => 'Sponsored by Govt. Insurance',
            'pay_class' => PayClass::from('Cash'),
            'approval'  => filter_var('true', FILTER_VALIDATE_BOOL),
            'bill_matrix' => '10',
            'balance_required' => filter_var('false', FILTER_VALIDATE_BOOL),
            'consultation_fee' => '0'
         ]);
         \App\Models\SponsorCategory::create([
            'user_id' => 1,
            'name' => 'Retainership',
            'description' => 'Sponsored by Organization',
            'pay_class' => PayClass::from('Credit'),
            'approval'  => filter_var('true', FILTER_VALIDATE_BOOL),
            'bill_matrix' => '0',
            'balance_required' => filter_var('true', FILTER_VALIDATE_BOOL),
            'consultation_fee' => '1500'
         ]);

        //Sponsor 
        \App\Models\Sponsor::create([
            'user_id' => 1,
            'name' => 'Self',
            'phone' => '00000000000',
            'email' => '',
            'registration_bill' => '2000',
            'sponsor_category_id' => 1,
            'category_name'  => 'Individual'
        ]);

        //Patient
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
            'name' => 'Suspension',
            'description' => 'Constituted medications',
            'user_id'   => 1,
            'resource_category_id'   => 1
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Syrup',
            'description' => 'Liquid medications including tonics',
            'user_id'   => 1,
            'resource_category_id'   => 1
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Dispersable',
            'description' => 'Medications disolved in a solution',
            'user_id'   => 1,
            'resource_category_id'   => 1
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Topical',
            'description' => 'Powders, Creams, Ointments',
            'user_id'   => 1,
            'resource_category_id'   => 1
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Drop',
            'description' => 'Eye drops, Ear drops, Infant Drops',
            'user_id'   => 1,
            'resource_category_id'   => 1
        ]);
        // \App\Models\ResourceSubCategory::create([
        //     'name' => 'Treatment',
        //     'description' => 'Monitoring, Dressings, Care treatments',
        //     'user_id'   => 1,
        //     'resource_category_id'   => 1
        // ]);

        //ResourceSubCategories for Resource Category 2
        \App\Models\ResourceSubCategory::create([
            'name' => 'Microbiology',
            'description' => 'Microbiological Tests eg MP',
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
            'name' => 'Hermatology',
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
            'description' => 'Tests for toxins',
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
        // \App\Models\ResourceSubCategory::create([
        //     'name' => 'Supplies',
        //     'description' => 'All Items required for lab fucntions eg. test strips, reagents, etc',
        //     'user_id'   => 1,
        //     'resource_category_id'   => 2
        // ]);

        //ResourceSubCategories for Resource Category 3
        \App\Models\ResourceSubCategory::create([
            'name' => 'Consultation',
            'description' => 'Gp Consultations & Reviews, Specialist Consultation & Reviews ',
            'user_id'   => 1,
            'resource_category_id'   => 3
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Operation',
            'description' => 'Invasive interventions',
            'user_id'   => 1,
            'resource_category_id'   => 3
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Procedure',
            'description' => 'Non-invasive or less invasive interventions',
            'user_id'   => 1,
            'resource_category_id'   => 3
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Clinical Care',
            'description' => 'Professional Care Services',
            'user_id'   => 1,
            'resource_category_id'   => 3
        ]);
        
        //ResourceSubCategories for Resource Category 4
        \App\Models\ResourceSubCategory::create([
            'name' => 'Device',
            'description' => 'Countable Items eg. Gloves, Syringes, Cannulas etc.',
            'user_id'   => 1,
            'resource_category_id'   => 4
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Material',
            'description' => 'Wound care materials eg. Cotton wool, guaze etc.',
            'user_id'   => 1,
            'resource_category_id'   => 4
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Uncountable',
            'description' => 'Liquid Disinfectants eg. Spirit, Purit etc.',
            'user_id'   => 1,
            'resource_category_id'   => 4
        ]);

        //ResourceSubCategories for Resource Category 5
        \App\Models\ResourceSubCategory::create([
            'name' => 'Accommodation',
            'description' => 'Wards and Beds',
            'user_id'   => 1,
            'resource_category_id'   => 5
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Amenity',
            'description' => 'Water, Light, Intercom',
            'user_id'   => 1,
            'resource_category_id'   => 5
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'In-House Service',
            'description' => 'Disposables, Laundry',
            'user_id'   => 1,
            'resource_category_id'   => 5
        ]);

        //ResourceSubCategories for Resource Category 6
        \App\Models\ResourceSubCategory::create([
            'name' => 'Document',
            'description' => 'Birth Certificates, Death Certificates, Medical Reports etc.',
            'user_id'   => 1,
            'resource_category_id'   => 6
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Fine',
            'description' => 'Penalty for inproper conduct etc.',
            'user_id'   => 1,
            'resource_category_id'   => 6
        ]);
        \App\Models\ResourceSubCategory::create([
            'name' => 'Outside Service',
            'description' => 'Home, Office or Out of Hospital Inverventions etc.',
            'user_id'   => 1,
            'resource_category_id'   => 6
        ]);
        //Designation for super admin
        \App\Models\Designation::create([
            'designation' => 'Admin',
            'access_level'   => 6,
            'designator'  => 'Super Admin',
            'user_id'   => 1,
        ]);
        \App\Models\Designation::create([
            'designation' => 'IT Officer',
            'access_level'   => 5,
            'designator'  => 'Super Admin',
            'user_id'   => 2,
        ]);
    }
}
