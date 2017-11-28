<?php

use Illuminate\Database\Seeder;

class FileGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Components\File\Models\FileGroup::create([
            'name' => 'General',
            'description' => 'The default file group.',
        ]);
    }
}
