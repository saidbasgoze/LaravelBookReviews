<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class Book extends Model
{
    use HasFactory;
    public function reviews()
    {
        return $this->hasMany(Review::class);
        //one bok can have many reviewscl
    }
    public function scopeTitle(Builder $query,string $title):Builder{
        return $query->where("TITLE","LIKE","%"."$title"."%");
        //%ler önünde veya sonunda herhangi bir karaktere izin verirç yani aranan kelime varsa listele
    }

    public function scopeWithReviewsCount(Builder $query, $from = null, $to = null):Builder|QueryBuilder{
        return $query->withCount(["reviews" => 
        fn(Builder $q)=>$this->dateRangeFilter($q,$from,$to)
          //arrow function kullandık
        //arrow functionda use kullanmaya gerek yok accessimiz var dıs fonksiyona
    ]);
    }


    public function scopeWithAvgRating(Builder $query, $from = null, $to = null):Builder|QueryBuilder{
        return $query->withAvg(["reviews" => 
        fn(Builder $q)=>$this->dateRangeFilter($q,$from,$to)
        //arrow function kullandık
        //arrow functionda use kullanmaya gerek yok accessimiz var dıs fonksiyona
        ],"rating");
    }

    public function scopePopular(Builder $query, $from = null, $to = null):Builder|QueryBuilder{
        return $query->withReviewsCount()
        
        
        ->orderBy("reviews_count","desc");
        
        //reviews_count will be added after we run withCount
        //descending order yani azalan sıralama
    }

    public function scopeHighestRated(Builder $query, $from = null, $to = null):Builder|QueryBuilder{
        return $query->withAvgRating()
        ->orderBy("reviews_avg_rating","desc");
        //bu da withAvg kullanınca otomatik olusuyor 
    }

    public function scopeMinReviews(Builder $query, int $minReviews): Builder {
        return $query->withCount('reviews as reviews_count')
                     ->having('reviews_count', '>=', $minReviews);
                     //mesela en az 2 yorum vs.
    }

    private function dateRangeFilter(Builder $query,$from=null,$to=null):Builder|QueryBuilder{
        if ($from && !$to){
            $query->where("created_at",">=","$from");
            //şu tarihten
        } elseif (!$from && $to)
            {
                $query->where("created_at","<=","$to");
                //şu tarihe kadar
            } elseif ($from && $to)
            {
                $query->whereBetween("created_at",[$from,$to]);
                //şu tarihle şu tarih arası.
            }
            return $query;
    }

    public function scopePopularLastMonth(Builder $query) : Builder | QueryBuilder {
        return $query->popular(now()->subMonth(),now())
        ->highestRated(now()->subMonth(),now())
        ->minReviews(2);
    }

    public function scopePopularLast6Months(Builder $query) : Builder | QueryBuilder {
        return $query->popular(now()->subMonths(6),now())
        ->highestRated(now()->subMonths(6),now())
        ->minReviews(5);
    }
    public function scopeHighestRatedLastMonth(Builder $query) : Builder | QueryBuilder {
        return $query->highestRated(now()->subMonth(),now())
        ->popular(now()->subMonth(),now())
        ->minReviews(2);
    }

    public function scopeHighestRatedLast6Months(Builder $query) : Builder | QueryBuilder {
        return $query->highestRated(now()->subMonths(6),now())
        ->popular(now()->subMonths(6),now())
        ->minReviews(5);
    }
    protected static function booted(){
        static::updated(fn (Review $review) => cache()->forget("book:" . $book->id) );
        static::deleted(fn (Review $review) => cache()->forget("book:" . $book->id) );
    }

}
