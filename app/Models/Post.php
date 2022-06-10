<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Cviebrock\EloquentSluggable\Sluggable;


class Post extends Model
{
    use HasFactory;
    use sluggable;

    // protected $fillable =  ['title','slug','excerpt','body']; 

    protected $guarded = ["id"];
    protected $with = ['category','user'];


    public function scopeFilter($query, array $filters){
        $query->when($filters['search'] ?? false, function($query,$search){
            return $query->where('title','like','%' . $search . '%')
            ->orWhere('body','like','%' . $search . '%');
        });

        $query->when($filters['category'] ?? false, function($query,$category){
            return $query->whereHas('category',function($query) use ($category){
                $query->where('slug',$category);
            });
        });

        $query->when($filters['user'] ?? false, fn($query,$user)=>
            $query->whereHas('user', fn($query)=>
                $query->where('username',$user)
            )
        );
    }


    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }
}
