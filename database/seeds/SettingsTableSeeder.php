<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'name' => config('app.approver_setting_report'),
            'value' => [11]
        ];
        $data = new \App\Model\Setting($param);
        $data->save();
        $this->command->info('Setting report was created');
    }
}

