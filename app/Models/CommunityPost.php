<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityPost extends Model
{
    protected $fillable = [
        'user_id',
        'portfolio_id',
        'title',
        'content',
        'views_count',
    ];

    protected $casts = [
        'views_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function portfolio()
    {
        return $this->belongsTo(Portfolio::class);
    }

    public function comments()
    {
        return $this->hasMany(CommunityComment::class, 'community_post_id');
    }

    public function ratings()
    {
        return $this->hasMany(CommunityPostRating::class, 'community_post_id');
    }
}
