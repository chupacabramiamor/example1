<?php

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::where('email', 'like', '%example.com')->delete();

        for ($i = 1; $i <= 5; $i++) {
            factory(User::class)->create([
                'email' => "customer{$i}@example.com",
                'password' => '123456'
            ]);
        }

    }
}
