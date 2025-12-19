<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityPostRating extends Model
{
    protected $fillable = [
        'user_id',
        'community_post_id',
        'rating',
    ];

    public function post()
    {
        return $this->belongsTo(CommunityPost::class, 'community_post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
