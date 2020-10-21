<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DeviseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('devises')->insert(
            [ 
                'name' => 'Franc CFA BCEAO',
                'symbole' => 'F CFA'
            ]
        );
        DB::table('devises')->insert(
            [ 
                'name' => 'Dollar',
                'symbole' => '$'
            ]
        );
        DB::table('devises')->insert(
            [ 
                'name' => 'Euro',
                'symbole' => '€'
            ]
        );
    }
}
