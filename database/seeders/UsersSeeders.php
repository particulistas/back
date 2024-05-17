<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Carbon\Carbon;

class UsersSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin'
            ],
            [
                'name' => 'client'
            ],
        ];

        DB::transaction(function () use ($roles) {
            foreach ($roles as $role) {
                Role::updateOrCreate(
                    ['name' => $role['name']],
                    [
                        'guard_name'  => 'api'
                    ]
                );
            }

        });

        $userAdmin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('usuario12345'),
                'created_at' => Carbon::now()
            ]
        );

        $userAdmin->assignRole('admin');

        $userclient = User::updateOrCreate(
            ['email' => 'user@email.com'],
            [
                'name' => 'User Name',
                'password' => Hash::make('usuario12345'),
                'created_at' => Carbon::now()
            ]
        );

        $userclient->assignRole('client');
    }
}
