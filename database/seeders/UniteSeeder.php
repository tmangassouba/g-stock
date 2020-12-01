<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UniteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('unites')->insert([ 'name' => 'Carton', ]);
        DB::table('unites')->insert([ 'name' => 'Sac', ]);
        DB::table('unites')->insert([ 'name' => 'Paquet', ]);
    }
}
