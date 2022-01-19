<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'name' => "User",
                'key' => "user",
                'description' => "User description here",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                'name' => "Admin",
                'key' => "admin",
                'description' => "Admin description here",
                "created_at" => now(),
                "updated_at" => now()
            ]
        ];

        return Role::insert($data);
    }
}
