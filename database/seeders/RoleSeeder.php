<?php

namespace Database\Seeders;

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
        DB::table('roles')->insert(
            [ 'name' => 'ADMIN' ]
        );
        DB::table('roles')->insert(
            [ 'name' => 'GERANT' ]
        );
        DB::table('roles')->insert(
            [ 'name' => 'OPERATEUR' ],
        );
    }
}
