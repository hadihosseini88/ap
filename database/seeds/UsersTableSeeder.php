<?php

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
        $this->createAdminUser();
        $this->createUser();
    }

    private function createAdminUser()
    {
        $user = factory(\App\User::class)->make([
            'type' => \App\User::TYPE_ADMIN,
            'name' => 'ادمین اصلی',
            'mobile' => '+989111111111',
            'email' => 'admin@ap.test',
        ]);
        $user->save();

        $this->command->info('کاربر ادمین ایجاد شد');
    }

    private function createUser()
    {
        $user = factory(\App\User::class)->make([
            'name' => 'کاربر معمولی',
            'mobile' => '+989222222222',
            'email' => 'user@ap.test',
        ]);
        $user->save();

        $this->command->info('کاربر معمولی ایجاد شد');
    }
}
