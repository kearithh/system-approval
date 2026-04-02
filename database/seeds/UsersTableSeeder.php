<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $signature = [
            'img/sign/sign1.png',
            'img/sign/sign2.png',
            'img/sign/sign3.png',
            'img/sign/sign4.png',
            'img/sign/sign5.png',
            'img/sign/sign6.png',
            'img/sign/sign7.png',
            'img/sign/sign8.png',
            'img/sign/sign9.png',
        ];
        rand(0,8);
        // Seed User by Position
        $position = \App\Position::all();
        foreach ($position as $item) {
            DB::table('users')->insert([
                'position_id' => $item->id,
                'name' => $item->short_name,
                'username' => strtolower($item->short_name),
                'email' => NULL,
                'email_verified_at' => now(),
                'password' => Hash::make('123456'),
                'role' => NULL,
                'company_id' => 1,
                'signature' => $signature[rand(0, 8)],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
        $this->command->info($position->count() .' Users is create...');


        // Position
            // department, level,

        $head = [
            'ប្រធាននាយកដ្ឋានអភិវឌ្ឍន៌ធុរកិច្ច',
            'ប្រធាននាយកដ្ឋានប្រតិបត្តិកាធុរកិច្ច',
            'ប្រធាននាយកដ្ឋានហិរញ្ញវត្ថុ',
            'ប្រធាននាយកដ្ឋានធនធានមនុស្ស និងរដ្ឋបាល',
            'ប្រធាននាយកដ្ឋានសវនកម្មផ្ទៃក្នុង',
            'ប្រធាននាយកដ្ឋានព័ត៌មានវិទ្យា',
            'ប្រធាននាយកដ្ឋានធនធានមនុស្ស',
            'ប្រធាននាយកដ្ឋានប្រតិបត្តិការ',
            'ប្រធាននាយកដ្ឋានឥណទាន',
            'ប្រធាននាយកដ្ឋានរដ្ឋបាល',
            'ប្រធាននាយកដ្ឋានទីផ្សារ',
            'ប្រធាននាយកដ្ឋានធនធានមនុស្ស និងរដ្ឋបាលស្ដីទី',
        ];
    }
}

