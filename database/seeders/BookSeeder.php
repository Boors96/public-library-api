<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('books')->insert([
            'title'=> "الجنرال في متاهته",
            'other'=> "غابرييل غارسيا ماركيز",
            'category'=> "الأدب الإنجليزي",
            'section'=> "918",
            'likes'=> 148,
            'pages'=> 287,
            'path'=> 'general_in_his_maze.pdf',
            'first_publish'=> '1990',
            'comments'=> [
                [
                    'user'=> "boors",
                    'comment'=> "its a very good book",
                    'date'=> date('Y-M-D'),
                ]
            ]
        ]);
    }
}
