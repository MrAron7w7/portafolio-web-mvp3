<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityComment extends Model
{
    protected $fillable = [
        'user_id',
        'content',
        'rating',
        'is_featured',
        'type'
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'rating' => 'integer'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
