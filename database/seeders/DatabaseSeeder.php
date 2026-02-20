<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Visitor;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        /*Remove this to populate users and visitors
        User::factory(10)->create();
        Visitor::factory(100)->create();*/
        $this->call(DepartmentSeeder::class);
        $this->call(EmployeeSeeder::class);
        $this->call(PrcSeeder::class);
        $this->call(SubjectSeeder::class);
        $this->call(tags::class); 
        $this->call(SharepointSeeder::class);
        $this->call(InformationHubSeeder::class);
        $this->call(KpiSeeder::class);
        $this->call(KpiSegmentationSeeder::class);
        $this->call(KpiAccreditationSeeder::class);
        $this->call(KpiDimensionsSeeder::class);
    }
}
