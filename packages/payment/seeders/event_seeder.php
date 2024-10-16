<?php

declare(strict_types=1);

use Hyperf\Database\Seeders\Seeder;
use Hyperf\DbConnection\Db;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Db::table('events')->insert([
            'name' => 'Some Event',
            'description' => 'Some Description',
            'quota' => 10,
            'created_at' => \Hyperf\Support\now(),
            'updated_at' => \Hyperf\Support\now(),
        ]);
    }
}
