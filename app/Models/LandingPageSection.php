<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // Added this line
use Illuminate\Database\Eloquent\Model;

class LandingPageSection extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'content', 'images'];

    protected $casts = [
        'content' => 'array',
        'images' => 'array',
    ];
}
