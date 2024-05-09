<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Book;
use App\Models\Review;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Book::factory(33)->create()->each(function($book){
            $numReviews=random_int(5,30);
            //min 5 max 30 yorum
            Review::factory()->count($numReviews)
            ->good()
            ->for($book)
            //associated with book (id olusturuyor)
            ->create();
            //it will create 33 books
        });

        Book::factory(34)->create()->each(function($book){
            $numReviews=random_int(5,30);
            //min 5 max 30 yorum
            Review::factory()->count($numReviews)
            ->average()
            ->for($book)
            //associated with book (id olusturuyor)
            ->create();
            //it will create 33 books
        });

        Book::factory(34)->create()->each(function($book){
            $numReviews=random_int(5,30);
            //min 5 max 30 yorum
            Review::factory()->count($numReviews)
            ->bad()
            ->for($book)
            //associated with book (id olusturuyor)
            ->create();
            //it will create 33 books
        });
    }
}
