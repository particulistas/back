<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Profile;
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

            $userAdminProfile = $userAdmin->profile()->updateOrCreate([
                'firstname'  => 'Usuario Admin',
                'lastname'   => 'Administrador principal',
                'gender'     => 'M',
                'phone'     => '+555555555',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);

            $userclient = User::updateOrCreate(
                ['email' => 'user@email.com'],
                [
                    'name' => 'User Name',
                    'password' => Hash::make('usuario12345'),
                    'created_at' => Carbon::now()
                ]
            );

            $userclient->assignRole('client');

            $userClientProfile = $userclient->profile()->updateOrCreate([
                'firstname'  => 'Usuario Client',
                'lastname'   => 'Usuario Cliente',
                'gender'     => 'F',
                'phone'     => '+555555555',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
    }
}
