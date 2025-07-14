<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;



class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();
        $this->call(DepartmentSeeder::class);
        $this->call(EmployeeSeeder::class);
        $this->call(PrcSeeder::class);
        $this->call(SubjectSeeder::class);
        $this->call(tags::class); 
        $this->call(NewSeeder::class); 
        $this->call(SharepointSeeder::class);
    }
}
