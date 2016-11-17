<?php

use Illuminate\Database\Seeder;

class UsersProductionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = factory(App\User::class, 'admin')->create(['email' => 'aan.boyz10@gmail.com']);
        $this->command->info("New Admin created. Username: $admin->email,  Password: anisku11");
    }
}
