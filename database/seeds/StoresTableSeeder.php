<?php

use Illuminate\Database\Seeder;
use App\Models\Store;

class StoresTableSeeder extends Seeder
{
    protected $stores = [
        ['parent_id' => 0, 'store_name' => 'A'],
        ['parent_id' => 0, 'store_name' => 'B'],
        ['parent_id' => 0, 'store_name' => 'C'],

        ['parent_id' => 1, 'store_name' => 'D'],
        ['parent_id' => 1, 'store_name' => 'E'],

        ['parent_id' => 2, 'store_name' => 'F'],
        ['parent_id' => 2, 'store_name' => 'G'],
        ['parent_id' => 2, 'store_name' => 'H'],

        ['parent_id' => 3, 'store_name' => 'I'],

        ['parent_id' => 9, 'store_name' => 'J'],
        ['parent_id' => 9, 'store_name' => 'K'],

        ['parent_id' => 7, 'store_name' => 'L']
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Store::insert($this->stores);
    }
}
