<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  public function run()
  {
    DB::table('users')->insert([
      [
        'name'              => 'Seller One',
        'email'             => 'seller1@example.com',
        'email_verified_at' => now(),
        'password'          => Hash::make('111111111'),
        'created_at'        => now(),
        'updated_at'        => now(),
      ],
      [
        'name'              => 'Seller Two',
        'email'             => 'seller2@example.com',
        'email_verified_at' => now(),
        'password'          => Hash::make('111111111'),
        'created_at'        => now(),
        'updated_at'        => now(),
      ],
      [
        'name'              => 'Unassigned User',
        'email'             => 'user3@example.com',
        'email_verified_at' => now(),
        'password'          => Hash::make('111111111'),
        'created_at'        => now(),
        'updated_at'        => now(),
      ],
    ]);
  }
}
