<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insertOrIgnore([
            'id'                    => 1,
            'name'                  => 'Test User',
            'email'                 => 'test',
            'email_verified_at'     => '2026-04-04 00:20:32',
            'password'              => Hash::make('1234'),
            'remember_token'        => '1234',
            'created_at'            => '2026-04-04 00:20:39',
            'updated_at'            => '2026-04-29 19:40:37',
            'spotify_access_token'  => null,
            'spotify_refresh_token' => null,
            'spotify_expires_at'    => null,
        ]);
    }
}
