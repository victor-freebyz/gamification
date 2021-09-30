<?php

namespace Database\Seeders;

use App\Models\Games;
use Illuminate\Database\Seeder;

class GamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $games = [
            ['id' => '1', 'name' => 'QUESTION BANK', 'type' => 'INTEL', 'number_of_winners' => '3']
        ];

        foreach($games as $game)
        {
            Games::updateOrCreate($game);
        }
    }
}
