<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model
{
    protected $guarded = ['id'];

    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class);
    }
}
