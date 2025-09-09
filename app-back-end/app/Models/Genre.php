<?php

namespace App\Models;

use App\Enum\GenreTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Genre extends Model
{
    protected $guarded = ['id'];

    public function songs(): BelongsToMany
    {
        return $this->belongsToMany(Song::class);
    }

//    public function showcased_album(): BelongsTo
//    {
//        return $this->belongsTo(Album::class);
//    }
}
