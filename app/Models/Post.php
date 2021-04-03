<?php

namespace App\Models;

use App\Models\Like;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'body'
    ];

    protected $appends = ['howLong'];

    public function getHowLongAttribute()
    {
        return $this->created_at->diffForHumans();
    }

    public function likedBy(User $user){
        return $this->likes->contains('user_id', $user->id);  //contains: colleciton method
    }

    // public function ownedBy(User $user)
    // {
    //     return (int)$user->id === (int)$this->user_id;
    // }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function likes()
    {
        return $this->hasMany(Like::class);
    }
}
