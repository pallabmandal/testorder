<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                "name" => "Admin",
                "code" => "admin",
            ],
            [
                "name" => "Customer",
                "code" => "customer",
            ]
        ];

        foreach ($roles as $value) {
            
            $existRole = \App\Models\Role::where('code', $value['code'])->first();

            if(empty($existRole)){
                $existRole = new \App\Models\Role();
                $existRole->name = $value['name'];
                $existRole->code = $value['code'];
            } else {
                $existRole->name = $value['name'];
            }

            $existRole->save();
        
        }


    }
}
