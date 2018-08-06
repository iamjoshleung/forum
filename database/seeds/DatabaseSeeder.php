<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        DB::table('users')->insert([
            'name' => 'JoshuaLeung',
            'email' => 'joshuajazleung@gmail.com',
            'password' => bcrypt('joshua'),
            'created_at' => \Carbon\Carbon::now()
        ]);

        factory(App\Thread::class, 50)->create()->each(function ($u) {
            $u->replies()->save(factory(App\Reply::class)->make());
        });
    }
}
